<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    //
	 protected $fillable = [
        'user_id', 'event_id','status','ticketID',
    ];


    public function users()
    {
        return $this->belongsTo('App\User');
    }
	
	public function events()
    {
        return $this->belongsTo('App\Event');
    }
}
