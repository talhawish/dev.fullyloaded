<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use Illuminate\Support\Facades\DB;
use FCM;
use App\Notification;
use App\User;

class NotificationController extends Controller
{
	
	public function index()
	{
		$user=$this->authUser();
		//$notifications=Notification::where('user_id',1)->take(50)->latest()->get();
		$notifications = DB::table('notifications')
            ->leftJoin('users', 'users.id', '=', 'notifications.sender_id')
            ->select('notifications.*','users.photo')
            ->where("notifications.user_id",$user->id)
            ->take(50)->latest()->get();
		return response()->json(["status"=>200,'message'=>'Notifications Data','data'=>$notifications]);
	}
	
	public function test(Request $request)
	{
	    $rules=[
			 'subject' => ['required','min:2','max:30'],
			 'body' => ['required','min:2'],
			 'token' => ['min:32'],
            ];
		$this->validateInput($rules,$request);
		
	     $user=$this->authUser();
	     if($request->fcm_token)
	     	$sent=$this->sendToSingle($request->fcm_token,$request->subjecct,$request->body);
	     else
		    $sent=$this->sendToSingle($user->id,$request->subjecct,$request->body);
		if($sent)
		    return response()->json(["status"=>200,'message'=>'Notifications Sent']);
		return response()->json(["status"=>200,'message'=>'Notifications Could Not be sent']);
	}
	
	
	
	public function sendToSingle($id,$subject,$body)
    {
        if(is_numeric($id))
        {
		    $user=User::find($id);
		    if(empty($user->fcm_token))
			    return false;
			$token =$user->fcm_token;
        }
		else
		    $token =$id;
		    
		  
		    
		    
        /*$url = "https://fcm.googleapis.com/fcm/send";
        $serverKey = env("FCM_SERVER_KEY", "default");
        $notification = array('title' =>$subject , 'text' => $body, 'sound' => 'default', 'badge' => '1');
        $arrayToSend = array('to' => $token, 'notification' => $notification,'priority'=>'high');
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key='. $serverKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,
        
        "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        //Send the request
        $response = curl_exec($ch);
        print_r($response); die;
        //Close request
        if ($response === FALSE) {
        die('FCM Send Error: ' . curl_error($ch));
        }
        curl_close($ch);*/


	
		
        $optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);
							
		$notificationBuilder = new PayloadNotificationBuilder();
        $notificationBuilder->setTitle($subject)
                    		->setBody($body)
                    		->setSound('default')
                    		->setBadge('1');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['title' => $subject,'body' => $body,'sound'=>'default','priority'=>'high','badge'=>'1']);
		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();
		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
		
			print_r($downstreamResponse);  die;
		if($downstreamResponse->numberSuccess())
			return true;
		elseif ($downstreamResponse->numberFailure())
		return false;
			$downstreamResponse->numberModification();
		return false;

    }
}
