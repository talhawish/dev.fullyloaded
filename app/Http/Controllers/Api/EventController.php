<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Category;
use App\Event;
use App\Checkin;
use App\Payment;
use App\Follower;
use Validator;
class EventController extends Controller
{
    //

    public function events()
    {
			$data=DB::table('events')
            ->select('events.*','name','photo','categories.title as category_title','subcategories.title as subcategory_title')
			->join('users','users.id','=','events.user_id')
			->join('categories', 'categories.id', '=', 'events.category_id')
			->leftJoin('subcategories', 'subcategories.id', '=', 'events.subcategory_id');
			
			if(isset($_GET['dateTime']) && $_GET['dateTime'])
			{
				$_GET['dateTime']=date('Y-m-d',strtotime($_GET['dateTime']));
				$dateTime=strtotime($_GET['dateTime']);
				$data->where('starts_at', '<',$dateTime+86400);
			}
			if(isset($_GET['category_id']) && $_GET['category_id'])
				$data->where('events.category_id', '=',$_GET['category_id']);
				
			if(isset($_GET['subcategory_id']) && $_GET['subcategory_id'])
				$data->where('events.subcategory_id', '=',$_GET['subcategory_id']);
				
			if(isset($_GET['user_id']) && $_GET['user_id'])
				$data->where('events.user_id', '=',$_GET['user_id']);
				
		    if(isset($_GET['status']) && $_GET['status']=="paid")
		    {
		        $user=$this->authUser();
		        $eventIDS=DB::table('payments')->where("user_id",$user->id)->where("status",'event')->pluck("event_id")->toArray();
				$data->whereIn('events.id',$eventIDS);
		    }
		    
		    if(isset($_GET['status']) && $_GET['status']=="checkedin")
		    {
		        $user=$this->authUser();
		        $eventIDS=DB::table('checkins')->where("user_id",$user->id)->pluck("event_id")->toArray();
				$data->whereIn('events.id',$eventIDS);
		    }
				
			if((!isset($_GET['dateTime']) || !$_GET['dateTime'])  && (!isset($_GET['user_id']) ||  !$_GET['user_id']))	
			    $data->where('starts_at','>=',time()+2000);
			    
			$events=$data->orderBy('starts_at')->take(70)->get();
        return response()->json(["status"=>200,'message'=>'Events Data','data'=>$events],200);
    }
    
    
    
     public function event($id)
    {
			$event = DB::table('events')
            ->select('events.*','name','photo','categories.title as category_title','subcategories.title as subcategory_title')
			->join('users','users.id','=','events.user_id')
			->join('categories', 'categories.id', '=', 'events.category_id')
			->leftJoin('subcategories', 'subcategories.id', '=', 'events.subcategory_id')
			->where('events.id', '=',$id)
			->orderBy('starts_at')
            ->first();

			if($event)
			{
			    $user=$this->authUser();
				$event->ticketID=null;
				$event->checked_in=0;
				$event->is_paid=0;
				
				if($checkin=Checkin::where("event_id",$id)->where("user_id",$user->id)->first())
				{
					$event->checked_in=1;
					$event->ticketID=$checkin->ticketID;
				}

				if($payment=Payment::where("event_id",$id)->where("user_id",$user->id)->first())
					$event->is_paid=1;
					
			    $event->ticketsSold=Checkin::where("event_id",$id)->count();
			}
			
        return response()->json(["status"=>200,'message'=>'Event Data','data'=>$event],200);
    }
	
	public function create(Request $request){
		$rules=[
		'category_id' => ['required'],
		'title' => ['required', 'min:3', 'max:255'],
		'starts_at' => ['required'],
		'ends_at' => ['required'],
		'amount' => ['numeric','min:0'],
		'tickets' => ['numeric','min:0'],
		'attachment' => 'mimes:jpeg,png,jpg,gif,svg,pdf,mp4|max:28000'
		];
		
		if($request->starts_at>$request->ends_at)
			return response()->json(["status"=>400,'message'=>'End Time Cannot be greater than Start Time']);

		/*$event=Event::where("starts_at",'<=',strtotime($request->ends_at))->where("ends_at",'>=',strtotime($request->starts_at))->first();
		if($event)
			return response()->json(["status"=>400,'message'=>'You  already have event Pending in this time duration']);*/
		
		
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		$event =new Event();
		$event->user_id=$user->id;
		$event->category_id=$request->category_id;
		$event->subcategory_id=$request->subcategory_id;
		$event->latitude=$request->latitude;
		$event->longitude=$request->longitude;
		$event->title=$request->title;
		if($request->tickets)
			$event->tickets=$request->tickets;
		else
			$event->tickets=200;
		if(!$request->type)
			$event->type=1;
		else
			$event->type=$request->type;
		$event->starts_at=strtotime($request->starts_at);
		$event->ends_at=strtotime($request->ends_at);
		if($request->paid)
			$event->paid=$request->paid;
		if(!$request->amount)
			$event->amount=0;
		else
			$event->amount=$request->amount;
		if($request->limited)
			$event->limited=$request->limited;
		if($request->attachment)
		{
			$photo = time().rand(999,9999).'.'.request()->attachment->getClientOriginalExtension();
			if(request()->attachment->move(public_path('images/events'), $photo))
			{
				$event->attachment="images/events/".$photo;
			}
		}
		else
			$event->attachment=null;
		
		if($event->save())
		{
			$following=Follower::where('following_id',$user->id)->get();
			foreach($following as $foll)
			   $ids[]=$foll->followed_id;
			$notification=new NotificationController();
			$notification->sendToMultiple($ids,$user->name,'has created an event "'.$event->title.'"',$user->id,$event->id,'event');
			return response()->json(["status"=>200,'message'=>'Event Created Successfully','data'=>$event]);
		}
		return response()->json(["status"=>400,'message'=>'oops Event Cannot be created']);
	
	}
	
	public function checkin(Request $request)
	{
		$rules=[
		'event_id' => ['required','min:0']
		];
		
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		
		$checkin=Checkin::where("event_id",$request->event_id)->where("user_id",$user->id)->first();
		if($checkin)
			return response()->json(["status"=>400,'message'=>'You have already Checked in']);
		$event=Event::find($request->event_id);
		if(!$event)
			return response()->json(["status"=>400,'message'=>'Event Not Found']);
		elseif($event->paid)
			return response()->json(["status"=>400,'message'=>'This Event is paid']);
		elseif($event->status==3)
		    return response()->json(["status"=>400,'message'=>'Event already completed']);
		
		$checkins=Checkin::where("event_id",$request->event_id)->count();
		if($checkins>=$event->tickets)
			return response()->json(["status"=>400,'message'=>'Limit Reached']);
			
		$checkin=new Checkin();
		$checkin->user_id=$user->id;
		$checkin->event_id=$request->event_id;
		$checkin->ticketID=substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,3).time();
		if($checkin->save())	
			return response()->json(["status"=>200,'message'=>'Checkin Successfull','data'=>$checkin]);
		return response()->json(["status"=>400,'message'=>'oops Checkin Failed']);
			
	}
	
	
	
	public function streamStart(Request $request){
		$rules=[
			'event_id' => ['numeric','min:1'],
			'platformID'   => ['required','min:7']
            ];
		$this->validateInput($rules,$request);
		
		$event=Event::find($request->event_id);
		
		if($event)
	    {
	        /*if($event->status==2 && $event->platformID)
				return response()->json(["status"=>400,'message'=>'Stream is already started']);*/
		    if($event->status==3)
				return response()->json(["status"=>400,'message'=>'Event is already completed']);
			
	       $event->platformID=$request->platformID;
		   $event->status=2;
	       if($event->save())
	       {
			   $user=$this->authUser();  $ids=[];
			   $checkins=Checkin::where('event_id',$event->id)->get();
			   foreach($checkins as $pers)
				  $ids[]=$pers->user_id;
			   $notification=new NotificationController();
			   $notification->sendToMultiple($ids,$user->name,'has started streaming "'.$event->title.'"',$user->id,$event->id,'event_stream');
	           return response()->json(["status"=>200,'message'=>'Stream started successfully','data'=>$event]);
	       }
	       return response()->json(["status"=>400,'message'=>'Ooops Stream Could Not be started']);
	    }
	    else
	    return response()->json(["status"=>400,'message'=>'Event Not Found']);
	}
	
	
	public function complete($id){
	    $event=Event::find($id);
	    if(!$event)
	        return response()->json(["status"=>400,'message'=>'Event Not Found']);
	   elseif($event->status==3)
		    return response()->json(["status"=>400,'message'=>'Event already completed']);
	        
	   $event->status=3;
	   if($event->save())
	        return response()->json(["status"=>200,'message'=>'Event completed successfully']);
	   return response()->json(["status"=>200,'message'=>'Event Could not be completedd ,error occured']);
	}
	
	
	public function buy(Request $request){
		
		$rules=[
			'event_id' => ['numeric','min:1'],
			'amount'   => ['required','numeric','min:0'],
			'nonce'    => ['required','min:12','max:64'],
            ];
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		$event=Event::find($request->event_id);
		if($event->status==3)
		    return response()->json(["status"=>400,'message'=>'Event already completed']);
		    
		$payment=new PaymentController();
		$transaction=$payment->makeTransaction($request->amount,$request->nonce);
		if($transaction)
		{
			$payment=new Payment();
			$payment->user_id=$user->id;
			$payment->event_id=$request->event_id;
			$payment->stream_id=null;
			$payment->event_id=$request->event_id;
			$payment->amount=$request->amount;
			$payment->transactionID=$transaction;
			$payment->status='event';
			$ticketID=substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(3/strlen($x)) )),1,3).time();
			$payment->ticketID=$ticketID;
			if($payment->save())
			{
				$checkin=new Checkin();
				$checkin->user_id=$user->id;
				$checkin->event_id=$request->event_id;
				$checkin->ticketID=$ticketID;
				if($checkin->save())	
				{
				     //update balance of the streamer
    			    $profile=User::find($event->user_id);
    			    $tax=1+($payment->amount*0.1);
    			    $profile->balance=$profile->balance+($payment->amount-$tax);
    			    $profile->save();
    			    
    			    $notification=new NotificationController();
			        $notification->sendToSingle($event->user_id,$user->name,'has paid for event "'.$event->title.'"',$user->id,$event->id,'paid_checkin');
    			    
					return response()->json(["status"=>200,'message'=>'Checkin Successful','data'=>$checkin]);
				}
			}
		}
		return response()->json(["status"=>400,'message'=>'Ooops Payment Failed']);
	}

}
