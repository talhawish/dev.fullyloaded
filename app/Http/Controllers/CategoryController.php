<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Subcategory;
use Illuminate\Support\Facades\DB;
class CategoryController extends Controller
{
    //
	
	public function index()
    {
		$categories = Category::with("subcategories")->where('deleted','0')->get();	
		return view('dashboard.pages.categories.index')->with('categories',$categories);
    }
	
	public function create()
    {
		return view('dashboard.pages.categories.add');
    }
	
		public function store(Request $request)
    {
		$this->validate($request,[
			'title'=>'required|min:2',
		]);
		
		$category=Category::where('title',$request->input('title'))->first();
		if($category)
		    $category->deleted='0';
		else
		{
    		$category=new Category;
    		$category->title=$request->input('title');
		}
		if($category->save())
			return redirect('/dashboard/categories')->with('success','Category Added');
		return redirect('/dashboard/categories')->with('error','Category Could Not be Added');
    }
	
	public function show($id)
    {
		$category=Category::find($id);
		return view('dashboard.pages.categories.edit')->with('category',$category);
    }
	
	public function update(Request $request)
    {
		$this->validate($request,[
			'title'=>'required|min:2',
			'category_id'=>'required'
		]);
		
		$id=$request->input('category_id');
		$category=Category::find($id);
		$category->title=$request->input('title');
		$category->save();
		return redirect('/dashboard/categories')->with('success','Category Udated');
    }
	
	
	public function subcategories()
    {
		$categories = DB::table('categories')
            ->join('subcategories', 'categories.id', '=', 'subcategories.category_id')
            ->select('categories.*', 'subcategories.title as sub_title','subcategories.status as sub_status',
			'subcategories.id as sub_id','subcategories.created_at as sub_created_at')
			->where("categories.status",1)->where("subcategories.status",1)->where("subcategories.deleted",'0')->where("categories.deleted",'0')
            ->orderByDesc('subcategories.id')->get();
		//$categories = Subcategory::all();
		return view('dashboard.pages.subcategories.index')->with('categories',$categories);
    }
	
	public function subCreate(){
		$categories=Category::where("status",1)->get();
		return view('dashboard.pages.subcategories.add')->with('categories',$categories);
	}
	
	public function subStore(Request $request){
		$this->validate($request,[
			'title'=>'required|min:2',
			'category_id'=>'required',
		]);
		$subcategory=Subcategory::where('title',$request->input('title'))->first();
		if($subcategory)
		    $subcategory->deleted='0';
		else
		{
    		$subcategory=new Subcategory;
    		$subcategory->title=$request->input('title');
		}
		$subcategory->category_id=$request->input('category_id');
		if($subcategory->save())
			return redirect('/dashboard/subcategories')->with('success','Sub-Category Added');
		return redirect('/dashboard/subcategories')->with('error','Sub-Category Could Not be Added');
	}
	
	public function subShow($id)
    {
		$category = DB::table('categories')
            ->join('subcategories', 'categories.id', '=', 'subcategories.category_id')
            ->select('categories.*', 'subcategories.title as sub_title','subcategories.status as sub_status',
			'subcategories.id as sub_id','subcategories.created_at as sub_created_at')
			->where("categories.status",1)->where("subcategories.status",1)->where('subcategories.id',$id)
            ->orderByDesc('subcategories.id')->get();
           
			$category[0]->categories=Category::where("status",1)->get();
			
		return view('dashboard.pages.subcategories.edit')->with('category',$category[0]);
    }
	
	public function subUpdate(Request $request)
    {
		$this->validate($request,[
			'title'=>'required|min:2|unique:subcategories',
			'category_id'=>'required',
			'subcategory_id'=>'required'
		]);
		
		$id=$request->input('subcategory_id');
		$subcategory=Subcategory::find($id);
		$subcategory->title=$request->input('title');
		$subcategory->category_id=$request->input('category_id');
		$subcategory->save();
		return redirect('/dashboard/subcategories')->with('success','Sub-category Udated');
    }
	
	
	public function destroy($id)
    {
		$category=Category::find($id);
		$category->deleted='1';
		if($category->save())
			die("success");
		die("unsuccessful");
    }
    
    public function deleteSubCategory($id){
        $subcategory=Subcategory::find($id);
        $subcategory->deleted='1';
        if($subcategory->save())
            	die("success");
		die("unsuccessful");
    }
}
