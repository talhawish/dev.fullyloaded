<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
	protected $fillable = [
        'user_id', 'stream_id','event_id','amount','transactionID','ticketID','status',
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
	
	public function streams()
    {
        return $this->belongsTo('App\Stream');
    }
	
	public function events()
    {
        return $this->belongsTo('App\Event');
    }
    
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
}
