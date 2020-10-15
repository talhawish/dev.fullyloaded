<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Illuminate\Support\Facades\DB;
use App\Withdrawl;
use Validator;
class WithdrawlController extends Controller
{
    //

    public function index()
    {
		$user=$this->authUser();
		$data=DB::table('withdrawls')
            ->select('withdrawls.*','name','photo','balance','username','email')
			->join('users','users.id','=','withdrawls.user_id')
			->where("user_id",$user->id);
				
		if(isset($_GET['status']) && $_GET['status'])
		{
			$data->where('status',$eventIDS);
		}    
		$withdrawls=$data->latest()->take(100)->get();
        return response()->json(["status"=>200,'message'=>'withdrawls data','data'=>$withdrawls]);
    }
    
    
	
	public function request(Request $request){
		$rules=[
		'amount' => ['required','numeric','min:0','max:99999']
		];
		
		/*$rules=[
		'amount' => ['required','numeric','min:0','max:99999'],
		'account' => ['required','min:10'],
		'account_title' => ['required','min:2']
		];*/
		
		$this->validateInput($rules,$request);
		
		$user=$this->authUser();
		
		if($request->amount<50)
			return response()->json(["status"=>400,'message'=>'You can withdraw minimum of $50 amount']);
			
		if(!$user->withdrawl_account || empty($user->withdrawl_account))
		    	return response()->json(["status"=>400,'message'=>'Please add withdrawl account in profile setting before you add withdrawl request']);
		    	
		if($user->balance<$request->amount)
			return response()->json(["status"=>400,'message'=>'Balance available is less than withdrawl amount']);
		
		if(Withdrawl::where("user_id",$user->id)->where('status','!=',3)->first())
			return response()->json(["status"=>400,'message'=>'You already have withdrawl request pending']);
		
		$withdrawl =new Withdrawl();
		$withdrawl->user_id=$user->id;
		$withdrawl->amount=$request->amount;
		$withdrawl->account_title=$user->name;
		$withdrawl->account=$user->withdrawl_account;
		if($request->channel)
			$withdrawl->channel=$request->channel;
		else
			$withdrawl->channel="paypal";
		$withdrawl->status=1;
		
		if($withdrawl->save())
			return response()->json(["status"=>200,'message'=>'withdrawl requested Successfully','data'=>$withdrawl]);
		return response()->json(["status"=>400,'message'=>'oops withdrawl Cannot be requested']);
	
	}
	
	
	public function update(Request $request){
		$rules=[
		'withdrawl_id' => ['required','numeric','min:0','max:999999'],
		'amount' => ['required','numeric','min:0','max:99999'],
		'account' => ['required','min:10'],
		'account_title' => ['required','min:2']
		];
		
		$this->validateInput($rules,$request);
		
		$user=$this->authUser();
		$withdrawl =Withdrawl::find($request->withdrawl_id);
		if(!$withdrawl)
			return response()->json(["status"=>400,'message'=>'Withdrawl not found']);
		
		if($user->balance<$request->amount)
			return response()->json(["status"=>400,'message'=>'Balance available is less than withdrawl amount']);
		

		$withdrawl->amount=$request->amount;
		$withdrawl->account_title=$request->account_title;
		$withdrawl->account=$request->account;
		if($request->channel)
			$withdrawl->channel=$request->channel;
		$withdrawl->status=1;
		
		if($withdrawl->save())
			return response()->json(["status"=>200,'message'=>'withdrawl request updated Successfully','data'=>$withdrawl]);
		return response()->json(["status"=>400,'message'=>'oops withdrawl request Cannot be updated']);
	
	}
	
}
