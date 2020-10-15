<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\FollowerController;
use App\Http\Controllers\Api\CommentController;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Event;
use App\Follower;
use App\Block;
use App\Comment;
use App\Stream;
use App\Report;
use App\Payment;
use Validator;
class UserController extends Controller
{
    //

    public  function profile($id=NULL)
    {
		$loggedUser=$this->authUser();
		if(!$id)
		{
			$user=$loggedUser;
			$id=$user->id;
		}
		elseif(!is_numeric($id))
			$user=User::where("username",$id)->first();
		else
			$user=User::find($id);
			
		if(!$user)
			return response()->json(["status"=>200,'message'=>'Profile Data','data'=>null],200);
			
        $followObj=new FollowerController();
		$commObj=new CommentController();
        
        $followers=$followObj->returnFollowers($user->id);
        $following=$followObj->returnFollowing($user->id);
		$comments=$commObj->returnComments($user->id,2);
		$user->follow=0;
		foreach($followers as $follower)
		{
			if($follower->following_id==$loggedUser->id)
			 {$user->follow=1; break;}
		}
		
		
		$time = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . " -12 hours"));
		$user->stream=Stream::where("user_id",$user->id)->where('status',2)->where('updated_at','>',$time)->latest()->first();
		$user->streaming=0;
		if($user->stream)
		{
		    	$user->stream->is_paid=0; $user->streaming=1;
		    if(Payment::where('user_id',$loggedUser->id)->where('stream_id',$user->stream->id)->where("status","ppv")->first())
				$user->stream->is_paid=1;
		}
		
		$user->is_blocked=0; 	$user->am_i_blocked=0;
		if($user->id!==$loggedUser->id)	
		{
			if(Block::where('blocked_id',$user->id)->where('user_id',$loggedUser->id)->first())
				$user->is_blocked=1;
				
			if(Block::where('blocked_id',$loggedUser->id)->where('user_id',$user->id)->first())
				$user->am_i_blocked=1;
		}
		
        $user->total_followers=sizeof($followers);
        $user->total_following=sizeof($following);

        $data['user']=$user;
        $data['followers']=$followers;
        $data['following']=$following;
		$data['comments']=$comments;
        return response()->json(["status"=>200,'message'=>'Profile Data','data'=>$data],200);
    }
    
	
	public function updateProfilePhoto(Request $request){
		
		request()->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg,bmp,tiff|max:20480',

        ]);
		
			$user=$this->authUser();
			$photo = time().rand(999,9999).'.'.request()->photo->getClientOriginalExtension();
			if(request()->photo->move('images/profiles', $photo))
			{
				$oldPhoto=$user->photo;
				$user->photo="images/profiles/".$photo;
				if($user->save())
				{
					if($oldPhoto && file_exists($oldPhoto))
						unlink($oldPhoto);
					return response()->json(["status"=>200,"message" =>"Profile Updated Successfully",'data'=>$user]);
				}
			}
            return response()->json(["status"=>400,"message" =>"Ooops Profile Could  Not be updated"]);
	}
	
	
	public function updateCoverPhoto(Request $request){
		
		request()->validate([
            'cover_photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10240',

        ]);
			$user=$this->authUser();
			$cover_photo = time().rand(999,9999).'.'.request()->cover_photo->getClientOriginalExtension();
			if(request()->cover_photo->move('images/covers', $cover_photo))
			{
				$oldPhoto=$user->cover_photo;
				$user->cover_photo="images/covers/".$cover_photo;
				if($user->save())
				{
					if($oldPhoto && file_exists($oldPhoto))
						unlink($oldPhoto);
					return response()->json(["status"=>200,"message" =>"Cover Updated Successfully",'data'=>$user]);
				}
			}
            return response()->json(["status"=>400,"message" =>"Ooops Cover Could  Not be updated"]);
	}
	
	

    public function updateFCM(Request $request){
        $rules=[
            'fcm_token' => ['required','max:255','min:100']
            ];
			
		$this->validateInput($rules,$request);
		
        $user=$this->authUser();
        $user->fcm_token=$request->fcm_token;
        if($user->save())
            return response()->json(["status"=>200,"message" =>"Token Updated Successfully",'data'=>$user]);
            return response()->json(["status"=>400,"message" =>"Ooops Token Could  Not be updated"]);
    }
	
	
		public function updateProfile(Request $request){
		  $rules=[
            'name' => ['max:255','min:2'],
            'phone' => ['max:14','min:10'],
            'address' => ['max:100'],
            'username' => ['max:24'],
            'withdrawl_account' => ['max:40']
            ];
			
		$this->validateInput($rules,$request);
		
        $user=$this->authUser();
        
		if($request->email)
		{
		    if(User::where("email",$request->email)->where("id",'!=',$user->id)->first())
		         return response()->json(["status"=>400,"message" =>"This Email is already in someones use"]);
			$user->email=$request->email;
		}
		
		if($request->username)
		{
		    if(User::where("username",$request->username)->where("id",'!=',$user->id)->first())
		         return response()->json(["status"=>400,"message" =>"This username is already in someone's use"]);
			$user->username=$request->username;
		}
		
		if($request->phone)
		{
		    if(User::where("phone",$request->phone)->where("id",'!=',$user->id)->first())
		         return response()->json(["status"=>400,"message" =>"This phone is already in someone's use"]);
			$user->phone=$request->phone;
		}
		
		if($request->name)
			$user->name=$request->name;
		if($request->address)
			$user->address=$request->address;
		if($request->latitude)
			$user->latitude=$request->latitude;
		if($request->longitue)
			$user->longitue=$request->longitue;
		if($request->withdrawl_account)
            $user->withdrawl_account=$request->withdrawl_account;
        if($user->save())
            return response()->json(["status"=>200,"message" =>"Profile Updated Successfully",'data'=>$user]);
            return response()->json(["status"=>400,"message" =>"Ooops Profile Could  Not be updated"]);
    }
	
	public function search(){
    
        if(isset($_GET['token']))
            unset($_GET['token']);
            
        $loggedUser=$this->authUser();
	    $users=[]; $events=[]; $WHERE="WHERE users.id!=$loggedUser->id "; $SELECT=" SELECT users.* "; $ORDERBY=""; $HAVING="";
		if(isset($_GET['user']) && $_GET['user'])
		{
		    $user=$_GET['user'];
		    //if(sizeof($_GET)>1)
		    if(isset($_GET['latitude']) && $_GET['latitude'])
		        $OPR="OR"; 
		    else 
		        $OPR="AND";
		        
		    $WHERE.=" $OPR (name LIKE '%$user%' OR email='$user' OR username LIKE '%$user%') ";
		}
		
		if(isset($_GET['latitude']) && isset($_GET['longitude']) && $_GET['latitude']  && $_GET['longitude'])
		{
		    $lat=$_GET['latitude']; $lng=$_GET['longitude'];
		    $SELECT.=",( 3959 * acos( cos( radians($lat) ) * cos( radians(latitude ) ) * cos( radians(longitude ) - radians($lng) ) + sin( radians($lat) ) * sin( radians(latitude ) ) ) ) AS distance ";
		    $HAVING=" HAVING distance<=70"; $ORDERBY=" ORDER BY distance ASC ";
		}
		 $query=" $SELECT FROM users  
		 LEFT JOIN blocks on blocks.blocked_id=users.id 
		 LEFT JOIN blocks as blocks1 on blocks1.user_id=users.id
		 AND blocks.blocked_id IS NULL   AND blocks1.user_id IS NULL
		  $WHERE
		 GROUP BY users.id  $HAVING
		 $ORDERBY   LIMIT 50";
		 
		 if(  (!isset($_GET['user']) || !$_GET['user']) && (!isset($_GET['latitude']) || !$_GET['latitude']))
		    $users=[];
		 else
		    $users =DB::select(DB::raw($query));
		 // die($query);
		foreach($users as $user)
		{
		    if(!isset($user->distance))
		        $user->distance=0;
		    if(isset($user->password))
		        unset($user->password);
		}
		
		
		
		$WHERE="WHERE events.ends_at>'".time()."' "; $SELECT=" SELECT events.*,users.name,email,photo";
		
		if(isset($_GET['user']) && $_GET['user'])
		{
		    $title=$_GET['user'];
		    //if(sizeof($_GET)>1)
		  if((isset($_GET['latitude']) && $_GET['latitude']) || (isset($_GET['category_id']) && $_GET['category_id']) || (isset($_GET['subcategory_id']) && $_GET['subcategory_id']) )
		        $OPR="OR"; 
		    else 
		        $OPR="AND";
		        
		    $WHERE.=" $OPR title LIKE '%$title%'  ";
		}
		
		if(isset($_GET['category_id']) && $_GET['category_id'] && is_numeric($_GET['category_id']))
		{
		    $user=$_GET['user'];
		    $WHERE.=" AND category_id=".$_GET['category_id'];
		}
		
			if(isset($_GET['latitude']) && isset($_GET['longitude']) && isset($_GET['latitude'])  && isset($_GET['longitude']))
		{
		    $lat=$_GET['latitude']; $lng=$_GET['longitude'];
		    $SELECT.=",( 3959 * acos( cos( radians($lat) ) * cos( radians(events.latitude ) ) * cos( radians(events.longitude ) - radians($lng) ) + sin( radians($lat) ) * sin( radians(events.latitude ) ) ) ) AS distance ";
		    $WHERE.=" HAVING distance<=70   ORDER BY distance ASC ";
		}
		 $query=" $SELECT FROM events
		 INNER JOIN users on users.id=events.user_id
		 $WHERE";
		 
		 
		 $events =DB::select(DB::raw($query));
		 
		 $resp['users']=$users;
		 $resp['events']=$events;
		return response()->json(["status"=>200,'message'=>'Search Profiles Data','data'=>$resp]);
	}
	
	public function block(Request $request){
		
		$rules=[
            'blocked_id' => ['required','numeric','min:1']
            ];
        
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		if($user->id==$request->blocked_id)
		    return response()->json(["status"=>400,'message'=>'Ooops You  Cannot Block yourself']);
		
		$blocked=Block::where('user_id',$user->id)->where('blocked_id',$request->blocked_id)->first();
        if($blocked)
            return response()->json(["status"=>400,'message'=>'This user is already Blocked']);
            
		$block=new Block();
		$block->user_id=$user->id;
		$block->blocked_id=$request->blocked_id;
		if($block->save())
	    {
	        $follow=Follower::where('following_id',$user->id)->where('followed_id',$request->blocked_id)->first();
	        if($follow)
	            $follow->delete();
			return response()->json(["status"=>200,'message'=>'User Blocked Successfully','data'=>$block]);
	    }
		return response()->json(["status"=>400,'message'=>'Ooops User Could Not be Blocked']);
			
	}
	
	
	public function unblock($id){
        $user=$this->authUser();
        $blocked=Block::where('user_id',$user->id)->where('blocked_id',$id);
        if($blocked->delete())
            return response()->json(["status"=>200,"message" =>"unblocked Successfully"]);
        return response()->json(["status"=>400,"message" =>"Ooops Could Not unblocked"]);
    }
    
    
   public function blocked(){
		$user=$this->authUser();
		$users = DB::table('blocks')
            ->join('users', 'users.id', '=', 'blocks.blocked_id')
            ->select('blocked_id','blocks.created_at as blocked_at','name','email','photo','phone','address','cover_photo')
			->where('user_id',$user->id)
            ->get();
		  return response()->json(["status"=>200,"message" =>"blocked Users",'data'=>$users]);
	}
	
	
	public function report(Request $request){
		
		$rules=[
            'title' => ['required','max:191','min:1'],
			'message' => ['required','max:1000','min:10'],
			'reported_id' => ['required','numeric','min:0']
            ];
			
		$this->validateInput($rules,$request);
		
		$user=$this->authUser();
		$report=new Report();
		$report->reporting_id=$user->id;
		$report->reported_id=$request->reported_id;
		$report->title=$request->title;
		$report->message=$request->message;
		if($request->attachment)
		{
			$attachment = time().rand(999,9999).'.'.request()->attachment->getClientOriginalExtension();
			if(request()->attachment->move('images/reports', $attachment))
			{
				$report->attachment="images/reports/".$attachment;
			}
		}
		
		if($report->save())
			return response()->json(["status"=>200,"message" =>"Report Submitted",'data'=>$report]);
		return response()->json(["status"=>200,"message" =>"Report could not be submitted"]);
	}
	
	public function logout(){
		$user=$this->authUser();
		$user->fcm_token=null;
		if($user->save())
		{
			auth()->logout();
			return response()->json(["status"=>200,"message" =>"Logged out Successfully"]);
		}
		return response()->json(["status"=>400,"message" =>"Error while Logging out"]);
	}
	
	
}
