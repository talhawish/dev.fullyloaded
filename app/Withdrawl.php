<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Withdrawl extends Model
{
    //
	protected $fillable = [
        'user_id', 'amount','account','status','channel','name',
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
}
