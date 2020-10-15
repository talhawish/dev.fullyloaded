<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reaction extends Model
{
    //
	protected $fillable = [
        'reaction', 'reactor_id','comment_id',
    ];
	
	public function comments()
    {
        return $this->belongsTo('App\Comment');
    }
}
