<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use Mail;
class ResetPasswordController extends Controller
{
    //
	
	public function sendResetPasswordEmail(Request $request)
    {
		$rules=[
		'email' => ['required','min:10','max:55']
		];
		$this->validateInput($rules,$request);
		
        $user = User::where("email",$request->email)->first();
		if(!$user)
			return response()->json(["status"=>400,'message'=>'This Email is not Registered in our Application']);
		$otp=rand(11111,99999);
		DB::table('password_resets')->where('email', '=', $request->email)->delete();
		DB::table('password_resets')->insert([
		'email'      => $request->email,
		'token'             => $otp,
		'created_at'       => date('Y-m-d H:i:s'),
		]);
		
        if(Mail::send('email.passwordreset', ['otp' => $otp], function ($m) use ($user) {
            $m->from(env("MAIL_USERNAME","noreply@livewaves.app"), 'Livewaves');
            $m->to($user->email,$user->name)->subject('Password Reset!');
        }))
			return response()->json(["status"=>200,'message'=>'Otp is sent to your Email,Please check your email']);
		else
			return response()->json(["status"=>400,'message'=>'Ooops Error while sending otp']);
    }
	
    public function resetPassword(Request $request)
	{
		$rules=[
		'email' => ['required','max:255'],
		'otp' => ['required','numeric','min:11111','max:99999'],
		'password' => ['required', 'min:6', 'confirmed']
		];
		$this->validateInput($rules,$request);
		
		
		$token = DB::table('password_resets')->select('*')->where("email",$request->email)->first();
		 if(!$token)
			 return response()->json(["status"=>400,'message'=>'You are unauthorized to change password']);
		if(strtotime($token->created_at)<strtotime('-1 hour'))
			return response()->json(["status"=>400,'message'=>'OTP is Expired, You can only update password within an hour']);
		if($token->token!==$request->otp)
			return response()->json(["status"=>400,'message'=>'OTP is incorrect']);
			
		$user = User::where("email",$request->email)->first();
		$user->password=bcrypt($request->password);
		if($user->save())
		{
			DB::table('password_resets')->where('email', '=',$request->email)->delete();
			return response()->json(["status"=>200,'message'=>'Password Reset Successful']);
		}
		return response()->json(["status"=>400,'message'=>'Ooops Error While Resetting Password']);
	}
	
	
	public function updatePassword(Request $request)
	{
		$rules=[
		'old_password' => ['required', 'min:6'],
		'password' => ['required', 'min:6', 'confirmed']
		];
		
		$this->validateInput($rules,$request);
		$user=$this->authUser();
		$userData= DB::table('users')
            ->select('password')
			->where('id',$user->id)
            ->first();
		$oldPassword=$userData->password;

		if(!password_verify($request->old_password,$oldPassword))
			return response()->json(["status"=>400,'message'=>'Invalid Old Password']);
		
		$user->password=bcrypt($request->password);
		if($user->save())
			return response()->json(["status"=>200,'message'=>'Password Updated Successfully']);
		return response()->json(["status"=>400,'message'=>'Ooops Error While Updating Password']);
	}
	
}
