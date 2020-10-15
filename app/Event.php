<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    //
	
	protected $fillable = [
        'user_id', 'title','category_id','starts_at','ends_at','attachment','paid','status','limited'
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
	
	public function categories()
    {
        return $this->belongsTo('App\Category');
    }
	
	public function payments()
    {
        return $this->hasMany('App\Payment');
    }
	
	public function checkins()
    {
        return $this->hasMany('App\Checkin');
    }
	
}
