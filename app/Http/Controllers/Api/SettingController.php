<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\User;
use App\Setting;

class SettingController extends Controller
{
 
	 
	 public function index()
    {
		$setting=Setting::first();
        return response()->json(["status"=>200,'message'=>'Settings Data','data'=>$setting]);
    }
	
	
}
