<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    //
	
	 protected $fillable = [
        'tags', 'comment_id', 'reply_id'
    ];
	
	public function comments()
    {
        return $this->belongsTo('App\Comment');
    }
	
	public function replies()
    {
        return $this->belongsTo('App\Reply');
    }
}
