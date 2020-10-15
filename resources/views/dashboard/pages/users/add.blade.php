
@extends('layouts.master') 
@section('content')
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>General Form</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Edit User</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Add User Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
			 <form method="post" action="{{url('dashboard/user/add')}}" accept-charset="UTF-8">
			  <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <div class="card-body">
				 <div class="form-group">
                    <label for="exampleInputName1">Name</label>
                    <input type="text" name="name" class="form-control" id="exampleInputName1" placeholder="Enter Name" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email" required>
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Phone Number</label>
                    <input type="tel"  name="phone" class="form-control"  id="exampleInputPhone1" placeholder="Enter Phonenumber" required>
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <input type="password" minlength="6"  name="password" class="form-control"  id="exampleInputPass1" placeholder="Enter Password" required>
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Confirm Password</label>
                    <input type="password"  minlength="6"  name="password_confirmation" class="form-control"  id="exampleInputPassC1" placeholder="Confirm Password" required>
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Type</label>
					
                    <select  class="form-control" name="status" required> 
						<option value="1" selected> User</option>
						<option value="3" > Admin</option>
					</select>
                  </div>
                  <!--<div class="form-group">
                    <label for="exampleInputPassword1">Password</label>
                    <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
                  </div>
                  <div class="form-group">
                    <label for="exampleInputFile">File input</label>
                    <div class="input-group">
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="exampleInputFile">
                        <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                      </div>
                      <div class="input-group-append">
                        <span class="input-group-text" id="">Upload</span>
                      </div>
                    </div>
                  </div>
                  <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="exampleCheck1">
                    <label class="form-check-label" for="exampleCheck1">Check me out</label>
                  </div>-->
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Add</button>
                </div>
             </form>
            </div>
            <!-- /.card -->

  

          </div>
          <!--/.col (left) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@endsection
 
@section('javascript')
@include('includes.scripts')
@include('includes.ajax')
@stop