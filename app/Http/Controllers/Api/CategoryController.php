<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\FollowerController;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Category;
use App\Subcategory;
use Validator;
class CategoryController extends Controller
{
    //

    public function categories()
    {
		$categories=Category::with("subcategories")->where('status',1)->get();
        return response()->json(["status"=>200,'message'=>'Categories Data','data'=>$categories],200);
    }

}
