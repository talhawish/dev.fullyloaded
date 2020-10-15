<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //

    protected $fillable = [
        'comment', 'profile_id', 'commenter_id'
    ];

    public function replies()
    {
        return $this->hasMany('App\Reply');
    }
	
	
	public function reactions()
    {
        return $this->hasMany('App\Reaction');
    }
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
	
	public function tags()
    {
        return $this->hasOne('App\Tag');
    }
	
	public static function boot() {
        parent::boot();
        self::deleting(function($comment) { // before delete() method call this
             $comment->replies()->each(function($reply) {
                $reply->delete(); // <-- direct deletion
             });
             $comment->reactions()->each(function($reaction) {
                $reaction->delete(); // <-- raise another deleting event on Post to delete comments
             });
             // do the rest of the cleanup...
        });
    }

}
