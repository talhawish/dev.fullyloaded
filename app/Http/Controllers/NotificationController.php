<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use Illuminate\Support\Facades\DB;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\User;
use App\Notification;

class NotificationController extends Controller
{
    public function sendToSingle($id,$subject,$body,$senderID=null,$contentID=null,$type=null)
    {
        if($id==$senderID)
			return false;
			
		$user=User::find($id);
		
		if(empty($user->fcm_token))
			return false;
		
        $optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($subject);
		$notificationBuilder->setBody($body)
							->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['subject'=>$subject,'body'=>$body,'type'=>$type,'senderID'=>$senderID,'contentID'=>$contentID]);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();
		
		$token =$user->fcm_token;
		
		$downstreamResponse = FCM::sendTo($token, $option, $notification, $data);
		// Save Notification to Database
        if($type!="paid_checkin")
        {
    		$notification=new Notification();
    		$notification->user_id=$id;
    		$notification->sender_id=$senderID;
    		$notification->contentID=$contentID;
    		$notification->type=$type;
    		$notification->subject=$subject;
    		$notification->body=$body;
    		$notification->save();
        }
		
		
		$tokensToDelete=$downstreamResponse->tokensToDelete();
		// DELETE INVALID TOKENS FROM DATABASE
		
		foreach($tokensToDelete as $token)
		{
		    $tokens=json_decode($token); 
		    if($tokens)
		    {
    		    foreach($tokens as $token)
    		    {
    		         $user=User::where("fcm_token",$token)->first();
        		    if($user)
        		    {
            		    $user->fcm_token=null;
            		    $user->save();
        		    }
    		    }
		    }
		}
		
		
			//return $downstreamResponse;
		if($downstreamResponse->numberSuccess())
			return true;
		elseif ($downstreamResponse->numberFailure())
		return false;
			$downstreamResponse->numberModification();
		return false;
		// return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete();

		// return Array (key : oldToken, value : new token - you must change the token in your database)
		$downstreamResponse->tokensToModify();

		// return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:error) - in production you should remove from your database the tokens
		$downstreamResponse->tokensWithError();
    }
	
	
	public function sendToMultiple($ids,$subject,$body,$senderID=null,$contentID=null,$type=null)
    {
		if(!is_array($ids))
			return false;
	    
		$tokens = DB::table('users')->whereIn('id',$ids)->where("fcm_token",'!=','')->where("fcm_token",'!=',null)->pluck('fcm_token')->toArray();
	
		if(empty($tokens))
			return false;
	
        $optionBuilder = new OptionsBuilder();
		$optionBuilder->setTimeToLive(60*20);

		$notificationBuilder = new PayloadNotificationBuilder($subject);
		$notificationBuilder->setBody($body)
							->setSound('default');

		$dataBuilder = new PayloadDataBuilder();
		$dataBuilder->addData(['subject'=>$subject,'body'=>$body,'type'=>$type,'senderID'=>$senderID,'contentID'=>$contentID]);

		$option = $optionBuilder->build();
		$notification = $notificationBuilder->build();
		$data = $dataBuilder->build();
		$downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
		
		$tokensToDelete=$downstreamResponse->tokensToDelete();
		// DELETE INVALID TOKENS FROM DATABASE
		
		foreach($tokensToDelete as $token)
		{
		    $tokens=json_decode($token); 
		    if($tokens)
		    {
    		    foreach($tokens as $token)
    		    {
    		         $user=User::where("fcm_token",$token)->first();
        		    if($user)
        		    {
            		    $user->fcm_token=null;
            		    $user->save();
        		    }
    		    }
		    }
		}
		
		// DELETE INVALID TOKENS FROM DATABASE
		$invalidTokens=$downstreamResponse->tokensWithError();
		
		$tokensToModify=$downstreamResponse->tokensToModify();
		
		//print_r($downstreamResponse); die;
		
		
		if($downstreamResponse->numberSuccess())
			return true;
		elseif ($downstreamResponse->numberFailure())
		return false;
			$downstreamResponse->numberModification();
		return false;
		// return Array - you must remove all this tokens in your database
		$downstreamResponse->tokensToDelete();

		// return Array (key : oldToken, value : new token - you must change the token in your database)
		//$downstreamResponse->tokensToModify();

		// return Array - you should try to resend the message to the tokens in the array
		$downstreamResponse->tokensToRetry();

		// return Array (key:token, value:error) - in production you should remove from your database the tokens
		$downstreamResponse->tokensWithError();
    }
    
}
