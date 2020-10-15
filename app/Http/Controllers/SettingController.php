<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Setting;

class SettingController extends Controller
{
	  public function __construct()
    {
        $this->middleware('auth');
    }
	
	 
	 public function terms_privacy()
    {
		$setting=Setting::first();
        return view('dashboard.pages.settings.terms_privacy',$setting);
    }
	
	 public function contact_us()
    {
		$setting=Setting::first();
        return view('dashboard.pages.settings.contact_us',$setting);
    }
    
    public function help()
    {
		$setting=Setting::first();
        return view('dashboard.pages.settings.help',$setting);
    }
	
	
	 public function edit_terms_privacy(Request $request)
    {
		$setting=Setting::first();
		$setting->terms_privacy=$request->input('terms_privacy');
		if($setting->save())
			 return redirect('/dashboard/setting/terms_privacy')->with('success','Terms & Privacy Updated');
		return redirect('/dashboard/setting/terms_privacy')->with('error','Terms & Privacy Could Not be Updated');
    }
	
	
	 public function edit_contact_us(Request $request)
    {
		$setting=Setting::first();
		$setting->contact_us=$request->input('contact_us');
		if($setting->save())
			 return redirect('/dashboard/setting/contact_us')->with('success','Contact Us Details Updated');
		return redirect('/dashboard/setting/contact_us')->with('error','Contact Us Details Could Not be Updated');
    }
    
     public function edit_help(Request $request)
    {
		$setting=Setting::first();
		$setting->help=$request->input('help');
		if($setting->save())
			 return redirect('/dashboard/setting/help')->with('success','Help Us Details Updated');
		return redirect('/dashboard/setting/help')->with('error','Help Details Could Not be Updated');
    }
}
