<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Payment;
use Illuminate\Support\Facades\DB;
class PaymentController extends Controller
{
    //
	
	public function index()
    {
		$payments = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('payments.*', 'name','photo')
            ->orderByDesc('payments.id')->get();
		//$payments = Payment::all();
		return view('dashboard.pages.payments.index')->with('payments',$payments);
    }
	
	public function donations()
    {
		$payments = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('payments.*', 'name','photo')
			->where("payments.status","tip")
            ->orderByDesc('payments.id')->get();
		return view('dashboard.pages.payments.stream')->with('payments',$payments);
    }
	
	public function ppv()
    {
		$payments = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('payments.*', 'name','photo')
			->where("payments.status","ppv")
            ->orderByDesc('payments.id')->get();
		return view('dashboard.pages.payments.stream')->with('payments',$payments);
    }
    
    public function event()
    {
		$payments = DB::table('payments')
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->select('payments.*', 'name','photo')
			->where("payments.status","event")
            ->orderByDesc('payments.id')->get();
		return view('dashboard.pages.payments.event')->with('payments',$payments);
    }
}
