<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

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
	$users = User::all()->except(auth()->id())->take(1000)->sortByDesc("id");
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
		
		if($user->delete())
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
