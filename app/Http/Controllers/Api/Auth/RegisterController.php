<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\User;
use App\Follower;
use Validator;
use JWTAuth;
class RegisterController extends Controller
{
    //
	
	public function Register(Request $request){
		$rules=[
		'email' => ['required', 'unique:users', 'max:32'],
		'name' => ['required'],
		'fb_id' => ['unique:users'],
		'twitter_id' => ['unique:users'],
		'username' => ['unique:users','max:24'],
		'password' => ['required','min:6', 'confirmed']
		];
		
		$this->validateInput($rules,$request);
		if(!$request->password)
			$request->password=rand(111111,999999);
		
		$user=new User();
		$user->email=$request->email;
		$user->fb_id=$request->fb_id;
		$user->twitter_id=$request->twitter_id;
		$user->username=$request->username;
		$user->fcm_token=$request->fcm_token;
		$user->password=bcrypt($request->password);
		$user->name=$request->name;
		$user->photo=$request->photo;
		$user->phone=$request->phone;
        $user->latitude=$request->latitude;
        $user->longitude=$request->longitude;
		if(!$user->save())
			return response()->json(["status"=>401,"message" => "Ooops Error Occured While Registering User"],401);
		
		$token=JWTAuth::fromUser($user);
		
		// Auto Follow Super User;
		$superUser=User::where("status",2)->first();
		if($superUser)
		{
			$follow=new Follower();
			$follow->following_id=$user->id;
			$follow->followed_id=$superUser->id;
			$follow->save();
		}
		
	 
		return response()->json(["status"=>200,"token" => $token,'message'=>'Registered successfully','data'=>$user],200);
	}
	
	
	public function refresh(){
		try {
			$token=auth()->refresh();
		} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
			return response()->json(['status'=>403,'message'=>$e->getMessage()],401);
		}
		
		return response()->json(["token" => $token]);
	}
}
