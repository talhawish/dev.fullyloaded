<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\User;
use App\Follower;
use JWTAuth;
class LoginController extends Controller
{
    //
	
	public function login(Request $request){
		
		if($request->fb_id || $request->twitter_id)
		{
		    $newUser=false;
			if($request->fb_id)
				$user = User::where('fb_id',$request->fb_id)->first();
			else
				$user = User::where('twitter_id',$request->twitter_id)->first();
    		if(!$user){
    		    
    		        if($request->email)
    		            $user = User::where('email',$request->email)->first();
    		        elseif($request->phone)
    		            $user = User::where('phone',$request->phone)->first();
    		        if(!$user)
    		        {
        			    $user=new User();
        			    $newUser=true;
    		        }
    		        
        			if($user->email && !$request->email) {}
        			else
            		    $user->email=$request->email;
            		if($request->fb_id)
            		    $user->fb_id=$request->fb_id;
            		if($request->twitter_id)
            		    $user->twitter_id=$request->twitter_id;
            		if($request->fcm_token)
            		    $user->fcm_token=$request->fcm_token;
                    elseif(!isset($user->password) || !$user->password)
                         $user->password=bcrypt(rand(111111,999999));
            		if($request->name)
            		    $user->name=$request->name;
            		if($request->photo)
					{
						$path="images/profiles/".time().rand(10,99).".jpg";
						file_put_contents($path, file_get_contents($request->photo));
						$user->photo=$path;
					}
					if($request->latitude)
            		    $user->latitude=$request->latitude;
            		if($request->longitude)
            		    $user->longitude=$request->longitude;
            		if($request->phone)
            		    $user->phone=$request->phone;
            		if(!$user->save())
			            return response()->json(["status"=>401,"message" => "Ooops Error Occured While Registering User"]);
			            
            	// Auto Follow Super User;
            	if($newUser){
                		$superUser=User::where("status",2)->first();
                		if($superUser)
                		{
                			$follow=new Follower();
                			$follow->following_id=$user->id;
                			$follow->followed_id=$superUser->id;
                			$follow->save();
                		}
            	}
    		}
			else
			{
				if($request->photo)
				{
					$oldPhoto=$user->photo;
					$path="images/profiles/".time().rand(10,99).".jpg";
					file_put_contents($path, file_get_contents($request->photo));
					$user->photo=$path;
					if($user->save())
					{
						if($oldPhoto)
							unlink($oldPhoto);
					}
				}
			}
			$token=JWTAuth::fromUser($user);
		}
		else
		{
			$creds=$request->only(['email','password']);
			if(!$token=auth()->attempt($creds))
			{
				 return response()->json(["status"=>401,"message" => "Incorrect Credentials"]);
			}	
			$user=auth()->user();
		}
		return response()->json(["status"=>200,"token" => $token,'message'=>'Logged In successfully','data'=>$user]);
	}
	
	
	public function refresh(){
		try {
			$token=auth()->refresh();
		} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['status'=>403,'message'=>$e->getMessage()]);
		}
		
		return response()->json(["token" => $token]);
	}
}
