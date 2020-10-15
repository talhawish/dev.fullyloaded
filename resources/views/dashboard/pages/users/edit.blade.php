
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
	@include('/includes.messages')
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- general form elements -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Edit User Form</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
			 <form method="post" action="{{url('dashboard/user/update')}}" accept-charset="UTF-8">
			  <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
			  <input name="user_id" type="hidden" value="{{$user->id}}"/>
                <div class="card-body">
				 <div class="form-group">
                    <label for="exampleInputEmail1">Full Name</label>
                    <input type="text" name="name" class="form-control" value="{{$user->name}}" id="exampleInputName1" placeholder="Enter Name" required>
                  </div>
                  <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" class="form-control" value="{{$user->email}}" id="exampleInputEmail1" placeholder="Enter email" required>
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Phone Number</label>
                    <input type="tel"  name="phone" class="form-control" value="{{$user->phone}}" id="exampleInputPhone1" placeholder="Enter Phonenumber" >
                  </div>
				  <?php 
					if($user->status!=2)
					{ ?>
				    <div class="form-group">
                    <label for="exampleInputEmail1">Type</label>
					
                    <select  class="form-control" name="status" required> 
						<option value="1" <?=$user->status==1?"selected":""?> > User</option>
						<option value="3" <?=$user->status==3?"selected":""?> > Admin</option>
					</select>
                  </div>
					<?php
					}
					else
					{ ?>
					<input type="hidden" value="2" name="status">
				<?php } 
				  ?>
				  
				   <div class="form-group">
                    <label for="exampleInputEmail1">Password</label>
                    <input type="password" minlength="6" maxlength="32" name="password" class="form-control"  id="exampleInputPass1" placeholder="Enter Password" >
                  </div>
				  <div class="form-group">
                    <label for="exampleInputEmail1">Confirm Password</label>
                    <input type="password"  minlength="6"  maxlength="32" name="password_confirmation" class="form-control"  id="exampleInputPassC1" placeholder="Confirm Password" >
                  </div>
                  
                  <!--
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
                  <button type="submit" class="btn btn-primary">Update</button>
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