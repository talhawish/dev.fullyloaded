<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stream extends Model
{
    //
	 protected $fillable = [
        'user_id', 'category_id', 'paid','latitude','longitude','amount','location','platformID','title','status'
    ];
	
	public function user()
    {
        return $this->belongsTo('App\User');
    }
	
	public function categories()
    {
        return $this->belongsTo('App\Category');
    }
    
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
}
