<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\DB;
use Braintree_Transaction;
use App\User;
use App\Category;
use App\Stream;
use App\Follower;
use App\Payment;
use Validator;
class StreamController extends Controller
{
    //

    public function streams($id=NULL)
    {
		$time = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -12 hours"));
		
		$user=$this->authUser();
		if(!$id)
			$streams=Stream::with("payments")->where('user_id',$user->id)->where('status',2)->where('updated_at','>',$time)->latest()->take(50)->get();
		elseif(is_numeric($id))
			$streams=Stream::where('user_id',$id)->latest()->take(50)->get();
		elseif($id=="global")
		{
			if(!isset($_GET['category_id']))
				$streams=Stream::with("payments")->where('user_id','!=',$id)->where('status',2)->where('updated_at','>',$time)->latest()->take(50)->get();
			else
				$streams=Stream::with("payments")->where('user_id','!=',$id)->where('status',2)->where('updated_at','>',$time)->where("category_id",$_GET['category_id'])->latest()->take(50)->get();
		}
		elseif($id=="following")
		{
			$ids=[];
				$following=Follower::where('following_id',$user->id)->get();
			foreach($following as $foll)
				$ids[]=$foll->followled_id;
			if(!isset($_GET['category_id']))
				$streams=Stream::with("payments")->whereIn('user_id',$ids)->where('status',2)->where('updated_at','>',$time)->latest()->take(50)->get();
			else
				$streams=Stream::with("payments")->whereIn('user_id',$ids)->where('status',2)->where('updated_at','>',$time)->where("category_id",$_GET['category_id'])->latest()->take(50)->get();
		}
		else
		$streams=Stream::with("payments")->where('status',2)->where('user_id',$id)->where('updated_at','>',$time)->latest()->take(50)->get();
	
		if($streams)
		{
			$userIDs=[];
			foreach($streams as $stream)
			{
				$userIDs[]=$stream->user_id;
				$stream->is_paid=0;
    			foreach($stream->payments as $payment)
    			{
    				if($payment->user_id==$user->id && $payment->status=='ppv')
    				{$stream->is_paid=1; break;}
    			}
    				unset($stream->payments);
			}
				
				
			$users = DB::table('users')
				->select('name','photo','id')
				->whereIn('id',$userIDs)
				->orderBy('id', 'DESC')
				->get();
			$userIDs=[];
			foreach($users as $user)
					$userIDs[]=$user->id;
					
			foreach($streams as $stream)
			{
				$index=array_search($stream->user_id,$userIDs);
				$stream->streamer_name=$users[$index]->name;
				$stream->streamer_photo=$users[$index]->photo;
			}
		}
			
        return response()->json(["status"=>200,'message'=>'Streams Data','data'=>$streams],200);
    }
	
	public function store(Request $request){
		$rules=[
            'category_id' => ['required','min:1'],
			 'title' => ['required','min:2','max:30'],
			 'amount' => ['numeric','min:0'],
			 'paid' => ['in:0,1'],
			 'location' => ['in:0,1'],
			 'status' => ['in:0,1,2,3'],
            ];
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		
		$stream=new Stream();
		$stream->title=$request->title;
		$stream->user_id=$user->id;
		$stream->category_id=$request->category_id;

		$stream->platformID=substr(str_replace(' ', '',preg_replace('/[[:^print:]]/', '',$request->title)),0,9).time().rand(1,99);
		if($request->amount)
			$stream->amount=$request->amount;
		if($request->paid)
			$stream->paid=$request->paid;
		if($request->location)
			$stream->location=$request->location;
		if($request->latitude)
			$stream->latitude=$request->latitude;
		if($request->longitude)
			$stream->longitude=$request->longitude;
		
		if($stream->save())
		{
		    $oldCStream=Stream::where("status",2)->where("user_id",$user->id)->first();
		    if($oldCStream)
		    {
		        $oldCStream->status=3;
		        $oldCStream->save();
		    }
			return response()->json(["status"=>200,'message'=>'Stream Added Successfully','data'=>$stream]);
		}
		return response()->json(["status"=>400,'message'=>'Ooops Stream Could Not be added']);
	}
	
	
	public function buy(Request $request){
		
		$rules=[
            'stream_id'=> ['numeric','min:1'],
			'event_id' => ['numeric','min:1'],
			'amount'   => ['required','numeric','min:0'],
			'nonce'    => ['required','min:12','max:64'],
            ];
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		$payment=new PaymentController();
		$transaction=$payment->makeTransaction($request->amount,$request->nonce);
		if($transaction)
		{
			$payment=new Payment();
			$payment->user_id=$user->id;
			$payment->stream_id=$request->stream_id;
			$payment->event_id=null;
			$payment->event_id=$request->event_id;
			$payment->amount=$request->amount;
			$payment->transactionID=$transaction;
			$payment->status='ppv';
			$payment->ticketID="T".time().rand(1,9);
			if($payment->save())
			{
			    $stream=Stream::find($payment->stream_id);
			    
			    //update balance of the streamer
			    $profile=User::find($stream->user_id);
			    $tax=1+($payment->amount*0.1);
			    $profile->balance=$profile->balance+($payment->amount-$tax);
			    $profile->save();
			    
			    
			    return response()->json(["status"=>200,'message'=>'Payment Successful','data'=>$payment]);
			}
		}
		return response()->json(["status"=>400,'message'=>'Ooops Payment Failed']);;
	}
	
	
	public function donate(Request $request){
		
		$rules=[
            'stream_id'=> ['numeric','min:1'],
			'event_id' => ['numeric','min:1'],
			'amount'   => ['required','numeric','min:0'],
			'nonce'    => ['required','min:12','max:64'],
            ];
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		$payment=new PaymentController();
		$transaction=$payment->makeTransaction($request->amount,$request->nonce);
		if($transaction)
		{
			$payment=new Payment();
			$payment->user_id=$user->id;
			$payment->stream_id=$request->stream_id;
			$payment->event_id=null;
			$payment->event_id=$request->event_id;
			$payment->amount=$request->amount;
			$payment->transactionID=$transaction;
			$payment->status='tip';
			$payment->ticketID=null;
			if($payment->save())
			{
			    $stream=Stream::find($payment->stream_id);
			    
			    //update balance of the streamer
			    $profile=User::find($stream->user_id);
			    $tax=1+($payment->amount*0.1);
			     $tax=0;
			    $profile->balance=$profile->balance+($payment->amount-$tax);
			    $profile->save();
			    
			    //Send notification
			    $notification=new NotificationController();
                $notification->sendToSingle($stream->user_id,"New donation",$user->name." has donated $".$payment->amount,$user->id,$payment->id,'donation');
			    return response()->json(["status"=>200,'message'=>'Donation Successful','data'=>$payment]);
			}
		}
		return response()->json(["status"=>400,'message'=>'Ooops Donation Failed']);
	}
	
    public function start($id)
	{
	    $stream=Stream::find($id);
	    
	    if($stream)
	    {
	        if($stream->status==2)
			return response()->json(["status"=>400,'message'=>'Stream is already started']);
		    elseif($stream->status==3)
			return response()->json(["status"=>400,'message'=>'Stream is already completed']);
			
	       $stream->status=2;
	       if($stream->save())
	       {
			   $user=$this->authUser();  $ids=[];
			   $following=Follower::where('followed_id',$user->id)->get();
			   foreach($following as $foll)
				  $ids[]=$foll->following_id;
			   $notification=new NotificationController();
			   $notification->sendToMultiple($ids,$user->name,'has started streaming "'.$stream->title.'"',$user->id,$stream->id,'stream');
	           return response()->json(["status"=>200,'message'=>'Stream started successfully','data'=>$stream]);
	       }
	       return response()->json(["status"=>400,'message'=>'Ooops Stream Could Not be started']);
	    }
	    else
	    return response()->json(["status"=>400,'message'=>'Stream Not Found']);
	}
	
	
	public function complete($id)
	{
	    $stream=Stream::find($id);
	    if($stream)
	    {
	       $stream->status=3;
	       if($stream->save())
	       {
	           return response()->json(["status"=>200,'message'=>'Stream Completed Successfully','data'=>$stream]);
	       }
	       return response()->json(["status"=>400,'message'=>'Ooops Stream Could Not be Completed']);
	    }
	    else
	    return response()->json(["status"=>400,'message'=>'Stream Not Found']);
	}

}
