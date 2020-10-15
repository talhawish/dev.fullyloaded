@extends('layouts.master') 
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Categories</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Categories</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
		
    <!-- Main content -->
    <section class="content">
	@include('/includes.messages')
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <!--<h3 class="card-title">DataTable with minimal features & hover style</h3>-->
			  <a href="/dashboard/category/add" class="btn btn-primary">Add New Category</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>#Subcategories</th>
				  <th>Created At</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
				@foreach($categories as $category)
				<?php for($i=0;$i<sizeof($category->subcategories);$i++) {if($category->subcategories[$i]->deleted==1) unset($category->subcategories[$i]); } ?>
                <tr id="category_{{$category->id}}">
				 <td>{{$category->id}}</td>
                 <td>{{$category->title}}</td>
                 <td>{{sizeof($category->subcategories)}}</td>
				 <td>{{$category->created_at}}</td>
				 <td>
					<a href="/dashboard/category/edit/{{$category->id}}"><i class="fa fa-edit" style="font-size:18px;color:blue"></i></a>&nbsp&nbsp
					<a href="#" onClick="deleteWithAjax({{$category->id}},'category')"><i class="fa fa-trash-o" style="font-size:18px;color:red"></i></a>
				 </td>
                </tr>
				@endforeach
                
                </tbody>
                <tfoot>
                <tr>
                  <th>#</th>
                  <th>Title</th>
                  <th>Sub</th>
				  <th>Created At</th>
                  <th>Action</th>
                </tr>
                </tfoot>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
 
@section('javascript')
@include('includes.scripts')
@include('includes.ajax')
@stop