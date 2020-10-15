<?php

namespace App\Http\Controllers\Api;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	 public function __construct()
    {
		auth()->setDefaultDriver('api',['except'=>['login','register','sendResetPasswordEmail','resetPassword']]);
		$this->middleware('jwt',['except'=>['login','register','sendResetPasswordEmail','resetPassword']]);
    }
	
	public function authUser(){
		try {
			$user = auth()->userOrFail();
		} catch (\Tymon\JWTAuth\Exceptions\UserNotDefinedException $e) {
			return response()->json(['status'=>403,'message'=>'Token Missing'],403);
		}
		return $user;
	}
	
	public function validateInput($rules,$request){
		 $validator = Validator::make($request->input(),$rules);
         if ($validator->fails()) {
             $error=' Some Error Ocuured';
			foreach ($validator->errors()->getMessages() as $key => $value) {
			    if(is_array($value))
				{
					$res= array( 'status'=>400,'message'=>$value[0]);
					 print_r(json_encode($res));
					 die;
				}
			}	
        }
	}
}
