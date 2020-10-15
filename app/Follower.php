<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    //
    protected $fillable = [
        'following_id', 'followed_id',
    ];


    public function users()
    {
        return $this->belongsTo('App\User');
    }
}
