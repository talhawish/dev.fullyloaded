<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    //
	
	protected $fillable = [
        'user_id', 'subject','body',
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
}
