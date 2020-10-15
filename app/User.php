<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','phone','fcm_token','fb_id','username','withdrawl_account'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
	
	public function getJWTIdentifier(){
		return $this->getKey();
	}
	
	public function getJWTCustomClaims(){
		return [];
    }
    
    public function followers()
    {
        return $this->hasMany('App\Follower');
    }
	
	public function reactions()
    {
        return $this->hasMany('App\Reaction');
    }
	
	public function replies()
    {
        return $this->hasMany('App\Reply');
    }
	
	public function comments()
    {
        return $this->hasMany('App\Comment');
    }
	
	public function events()
    {
        return $this->hasMany('App\Event');
    }
	
	public function streams()
    {
        return $this->hasMany('App\Stream');
    }
    
    public function blocks()
    {
        return $this->hasMany('App\Block');
    }
    
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
    
    public function notification()
    {
        return $this->hasMany('App\Notification');
    }
    
    public function checkins()
    {
        return $this->hasMany('App\Checkin');
    }
    
    public function withdrawls()
    {
        return $this->hasMany('App\Withdrawl');
    }
	
	public static function boot() {
        parent::boot();
        self::deleting(function($user) { // before delete() method call this
		
             $user->reactions()->each(function($reaction) {
                $reaction->delete(); // <-- direct deletion
             });
			 
             $user->replies()->each(function($reply) {
                $reply->delete(); // <-- direct deletion
             });
			 
			 $user->comments()->each(function($comment) {
                $comment->delete(); // <-- direct deletion
             });
			 
			 $user->followers()->each(function($follower) {
                $follower->delete(); // <-- direct deletion
             });
             
        });
    }
	

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
}
