<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    //
    protected $fillable = [
        'comment_id', 'reply',
    ];


    public function comments()
    {
        return $this->belongsTo('App\Comment');
    }
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
	
	public function tags()
    {
        return $this->hasOne('App\Tag');
    }
}
