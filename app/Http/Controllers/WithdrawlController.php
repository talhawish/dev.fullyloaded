<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Withdrawl;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Controllers\NotificationController;
class WithdrawlController extends Controller
{
    //
	
	  public function __construct()
    {
        $this->middleware('auth');
    }
	
	public function index()
    {
		//$withdrawls=Withdrawl::all();
		$withdrawls=DB::table('withdrawls')
            ->select('withdrawls.*','name','photo','balance','username','email')
			->join('users','users.id','=','withdrawls.user_id')
			->latest()->take(1500)->get();
		
		return view('dashboard.pages.withdrawls.index')->with('withdrawls',$withdrawls);
    }
	
	
	public function requests()
    {
		$withdrawls=DB::table('withdrawls')
            ->select('withdrawls.*','name','photo','balance','username','email')
			->join('users','users.id','=','withdrawls.user_id')
			->latest()->take(1500)->get();
		
		return view('dashboard.pages.withdrawls.index')->with('withdrawls',$withdrawls);
    }
	
	public function incorrect()
    {
		$this->validate($request,[
			'withdrawl_id'=>'required|min:1',
		]);
		$withdrawl=Withdrawl::find($request->input('withdrawl_id'));
		$withdrawl->status=2;
		if($withdrawl->save())
		{
		}
		return redirect('/dashboard/withdrawls')->with('success','provided details marked incorrect successfully');
    }
	
	
	public function paid(Request $request)
    {
		$this->validate($request,[
			'withdrawl_id'=>'required|min:1',
		]);
	
		$withdrawl=Withdrawl::find($request->input('withdrawl_id'));
		$withdrawl->status=3;
		if($withdrawl->save())
		{
			$user=User::find($withdrawl->user_id);
			$user->balance=$user->balance-$withdrawl->amount;
			$user->save();
		}
		return redirect('/dashboard/withdrawls')->with('success','Withdrawl request paid successfully');
    }
}
