
@extends('layouts.master') 
@section('content')
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Update Password</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Update Password</li>
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
              <!--<div class="card-header">
                <h3 class="card-title">Update Password Form</h3>
              </div>-->
              <!-- /.card-header -->
              <!-- form start -->
			 <form method="post" action="{{url('dashboard/setting/updatePassword')}}" accept-charset="UTF-8">
			  <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <div class="card-body">
				
				 <div class="form-group">
                    <label for="exampleInputName1">New Password</label>
                    <input type="password" name="password" class="form-control" minlength="6" placeholder="Enter New Password" required>
                  </div>
                
				
				 <div class="form-group">
                    <label for="exampleInputName1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="form-control"  minlength="6"  placeholder="Confirm New Password" required>
                </div>
                
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
@stop