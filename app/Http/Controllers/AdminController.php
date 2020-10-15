<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Comment;
use App\Stream;
use App\Event;
use App\Payment;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	  public function __construct()
    {
        $this->middleware('auth');
    }


	 public function versionone()
    {
        return view('dashboard.v1');
    }

    public function index()
    {
        //return view('dashboard.v1');
		return $this->users();
    }

	 public function userAddForm()
    {
        return view('dashboard.pages.users.add');
    }

	public function users()
    {
		//$users=User::all();
		$users = User::where('deleted','!=','1')->where('id','!=',auth()->id())->take(1000)->orderBy("id",'DESC')->get();
		return view('dashboard.pages.users.index')->with('users',$users);
    }

	public function userEdit($id)
    {
		$user=User::find($id);
		return view('dashboard.pages.users.edit')->with('user',$user);
    }


	public function userDelete($id)
    {
		$user=User::find($id);

		if($user->status==2)
			die("superUser");
		
		// DELETE THESE RECORD OF USER
		DB::table('reactions')->where('reactor_id', $id)->delete();
		DB::table('notifications')->where('user_id', $id)->delete();
		DB::table('checkins')->where('user_id', $id)->delete();
		DB::table('reports')->where('reporting_id', $id)->orWhere('reported_id',$id)->delete();
		DB::table('blocks')->where('user_id', $id)->orWhere('blocked_id',$id)->delete();
		DB::table('followers')->where('following_id', $id)->orWhere('followed_id',$id)->delete();
		DB::table('replies')->where('replier_id', $id)->delete();

		// delete comment and if this has attachment(file) unlink the file
		$comments=Comment::where('commenter_id',$id)->orWhere('profile_id',$id)->get();
		foreach($comments as $comment)
		{
			if($comment->attachment && file_exists($comment->attachment))
				unlink($comment->attachment);
			$comment->delete();
		}
		// DELETE STREAM IF THIS HAS NO PATMENTS
		$streams=Stream::where('user_id',$id)->get();
		foreach($streams as $stream)
		{
			$payments=Payment::where('stream_id',$stream->id)->get();
			if(empty($payments))
				$stream->delete();
		}
		// DELETE EVENTS IF THIS HAS NO PATMENTS
		$events=Event::where('user_id',$id)->get();
		foreach($events as $event)
		{
			$payments=Payment::where('event_id',$event->id)->get();
			if(empty($payments))
				$event->delete();
		}

		$user->deleted='1';
		$pre=rand(11,99)."del-";
		if($user->email)
			$user->email=$pre.$user->email;
		if($user->phone)
			$user->phone=$pre.$user->phone;
		$user->fb_id=null;
		$user->twitter_id=null;
		$user->username=null;
		$user->remember_token=null;
		$user->fcm_token=null;
		if($user->save())
			die("success");
		die("unsuccessful");
		//return redirect('/dashboard/users/index')->with('success','User Removed');
    }

	public function userUpdate(Request $request)
    {
		$this->validate($request,[
			'name'=>'required',
			'email'=>'required',
			'status'=>'required',
			'password' => 'confirmed',
		]);

		if($request->password && (!$request->password_confirmation || $request->password_confirmation!=$request->password))
			return redirect('/dashboard/users')->with('error','Password and confirm password does not match');

		$id=$request->input('user_id');
		$user=User::find($id);
		$user->name=$request->input('name');
		$user->status=$request->input('status');
		if($user->status==3)
			$user->is_admin=1;
		else
			$user->is_admin=0;
		$user->email=$request->input('email');
		$user->phone=$request->input('phone');
		if($user->cover_photo && file_exists($user->cover_photo))
			unlink($user->cover_photo);
		if($request->password)
			$user->password=bcrypt($request->password);
		$user->save();
		return redirect('/dashboard/users')->with('success','User Udated');
    }

	public function userAdd(Request $request)
    {
		$this->validate($request,[
			'name'=>'required',
			'email'=>'required|unique:users',
			'phone'=>'required|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
		$user=new User;
		$user->name=$request->input('name');
		$user->email=$request->input('email');
		$user->phone=$request->input('phone');
		$user->password= bcrypt($request->input('password'));
		$user->save();
		return redirect('/dashboard/users\index')->with('success','User Added');
    }

	public function password(){
		return view('auth.updatePass');
	}

	public function updatePassword(Request $request){
		$this->validate($request,[
			'password' => 'required|confirmed|min:6',
		]);

		$user=auth()->user();

		$user->password=bcrypt($request->password);
		if($user->save())
		{
		        auth()->logout();
		    	return redirect('/')->with('success','Password Updated Successfully');
		}
		else
				return redirect('/dashboard/setting/password')->with('error','Password could not be updated');

	}

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
