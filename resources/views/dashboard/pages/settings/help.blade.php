
@extends('layouts.master') 
@section('content')
   <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Help Form</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home/Setting</a></li>
              <li class="breadcrumb-item active">Help</li>
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
                <h3 class="card-title">Edit Help Details</h3>
              </div>
              <!-- /.card-header -->
              <!-- form start -->
			 <form method="post" action="{{url('dashboard/setting/help')}}" accept-charset="UTF-8">
			  <input name="_token" type="hidden" value="{{ csrf_token() }}"/>
                <div class="card-body">
				 <div class="form-group">
                    <label for="exampleInputName1">Help</label>
                    <!--<input type="text" name="title" class="form-control" minlength="2" id="exampleInputName1" placeholder="Enter Category Title" required>-->
					<textarea id="summernote" name="help" required>{{$help}}</textarea>
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
<!-- include summernote css/js -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
$(document).ready(function() {
  $('#summernote').summernote();
});
</script>
@stop