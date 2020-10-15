<?php

namespace App\Http\Controllers\Api;

require_once base_path() . '/vendor/braintree/braintree_php/lib/Braintree.php';
require_once base_path() . '/vendor/braintree/braintree_php/lib/Braintree/Configuration.php';
require_once base_path() . '/vendor/braintree/braintree_php/lib/Braintree/Gateway.php';

use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use App\Http\Controllers\Api\PaymentController;
use FCM;
use App\User;
use Braintree\Configuration;
use Braintree\Gateway;
class PaymentController extends Controller
{

	public function makeTransaction($amount,$nonce){
	$env=env("BRAINTREE_ENV", "sandbox"); 
	$merchantId=env("BRAINTREE_MERCHANT_ID", "sandbox"); 
	$publicKey=env("BRAINTREE_PUBLIC_KEY", "sandbox"); 
	$privateKey=env("BRAINTREE_PRIVATE_KEY", "sandbox"); 
	$config=new Configuration([
		'environment' => $env,
		'merchantId' => $merchantId,
		'publicKey' => $publicKey,
		'privateKey' => $privateKey
	]);	
	$gateway = new Gateway($config);
	
	$result = $gateway->transaction()->sale([
    'amount' => $amount,
    'paymentMethodNonce' => 'fake-valid-nonce',
    'options' => [ 'submitForSettlement' => true ]
	]);
	
	if ($result->success) {
		return $result->transaction->id;
	} else if ($result->transaction) {
		return false;
		print_r("Error processing transaction:");
		print_r("\n  code: " . $result->transaction->processorResponseCode);
		print_r("\n  text: " . $result->transaction->processorResponseText);
	} else {
		return false;
		print_r("Validation errors: \n");
		print_r($result->errors->deepAll());
	}
	
	}
    
}
