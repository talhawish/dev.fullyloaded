<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
	
	 protected $fillable = [
        'title', 'status'
    ];
	
	public function subcategories()
    {
        return $this->hasMany('App\Subcategory');
    }
	
	public function events()
    {
        return $this->hasMany('App\Event');
    }
	
	public function streams()
    {
        return $this->hasMany('App\Stream');
    }
	
	public static function boot() {
        parent::boot();
        self::deleting(function($category) { // before delete() method call this
		
             $category->subcategories()->each(function($subcategory) {
                $subcategory->delete(); // <-- direct deletion
             });
        });
    }

}
