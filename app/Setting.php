<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
	
	protected $fillable = [
        'logo', 'terms_privacy', 'icon','about','app_title','fb_url','transaction_share','twitter_url','email','phone'
    ];
}
