<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    //
	
	protected $fillable = [
        'category_id', 'status',
    ];


    public function categories()
    {
        return $this->belongsTo('App\Category');
    }
	
}
