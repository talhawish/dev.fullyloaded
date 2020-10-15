
@extends('layouts.master') 
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Users</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">users</li>
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
                <a href="/dashboard/user/add" class="btn btn-primary">Add New User</a>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="datatable" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>
				   <th>Created At</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
				@foreach($users as $user)
                <tr id="user_{{$user->id}}">
				 <td>{{$user->id}}</td>
                 <td>{{$user->name}}</td>
                 <td>{{$user->email}}</td>
                 <td><?php if($user->status==1) echo "User"; elseif($user->status==2) echo "Super Profile"; else echo "Admin";?></td>
				 <td>{{$user->created_at}}</td>
				 <td>
					<a href="/dashboard/user/edit/{{$user->id}}"><i class="fa fa-edit" style="font-size:18px;color:blue"></i></a>&nbsp&nbsp
					<a href="#" onClick="deleteWithAjax({{$user->id}},'user')"><i class="fa fa-trash-o" style="font-size:18px;color:red"></i></a>
				 </td>
                </tr>
				@endforeach
                
                </tbody>
                <tfoot>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
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
<script>
       $(document).ready(function () {
            $('#datatable').DataTable({
                    "pageLength": 25,
                    "order":[]
                }
            );
        });
</script>
@stop