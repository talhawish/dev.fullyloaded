<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use App\Follower;
use App\User;
use App\Http\Controllers\NotificationController;

class FollowerController extends Controller
{
    //

    public function follow($id){
        $user=$this->authUser();

        $follow=new Follower();
        $alreadyFollow=$follow::where('following_id',$user->id)->where('followed_id',$id)->get();
        if(isset($alreadyFollow[0]))
            return response()->json(["status"=>400,"message" =>"You are already following"]);
        $follow->following_id=$user->id;
        $follow->followed_id=$id;
        if($follow->save())
        {
            $notification=new NotificationController();
            $notification->sendToSingle($id,$user->name,"has started following you",$user->id,$follow->id,'follow');
            return response()->json(["status"=>200,"message" =>"Followed Successfully"]);
        }
        return response()->json(["status"=>400,"message" =>"Ooops Could Not Follow"]);
    }


    public function unfollow($id){
        $user=$this->authUser();
        
        $profile=User::find($id);
        if(!$profile)
             return response()->json(["status"=>400,"message" =>"Profile not found"]);
        if($profile->status==2)
             return response()->json(["status"=>400,"message" =>"You cannot unfollow the super profile"]);
             
        $follow=Follower::where('following_id',$user->id)->where('followed_id',$id);
        if($follow->delete())
            return response()->json(["status"=>200,"message" =>"unfollowed Successfully"]);
        return response()->json(["status"=>400,"message" =>"Ooops Could Not unfollow"]);
    }
    
    
    public function followers($id=null)
    {
        if(!$id)
        {
            $user=$this->authUser();
            $id=$user->id;
        }
        $followers=$this->returnFollowers($id);
        return response()->json(["status"=>200,'message'=>'Followers Data','data'=>$followers]);
    }



    public function returnFollowers($id)
    {
        $query="SELECT followers.*,following.name as following_name,following.photo  as following_profile,following.email as following_email,following.username as following_username,
	    followed.name as followed_name,followed.photo  as followed_profile,followed.email as followed_email,followed.username as followed_username
	    from followers 
	    INNER JOIN users as following ON following.id=followers.following_id
	    INNER JOIN users as followed ON followed.id=followers.followed_id
	    WHERE  followed_id=$id";
        $followers=DB::select(DB::raw($query));
        return $followers;
    }

    public function returnFollowing($id)
    {
        $query="SELECT followers.*,following.name as following_name,following.photo  as following_profile,following.email as following_email,following.username as following_username,
	    followed.name as followed_name,followed.photo  as followed_profile,followed.email as followed_email,followed.username as followed_username
	    from followers 
	    INNER JOIN users as following ON following.id=followers.following_id
	    INNER JOIN users as followed ON followed.id=followers.followed_id
	    WHERE  following_id=$id";
        $following=DB::select(DB::raw($query));
        return $following;
    }
}
