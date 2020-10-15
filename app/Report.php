<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
	protected $fillable = [
        'reporting_id', 'reported_id','status','message','attachment','title'
    ];
	
	public function users()
    {
        return $this->belongsTo('App\User');
    }
}
