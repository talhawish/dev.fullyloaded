<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    //
	protected $fillable = [
        'user_id', 'blocked_id',
    ];
	
	public function categories()
    {
        return $this->belongsTo('App\Category');
    }
}
