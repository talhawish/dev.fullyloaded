@extends('layouts.master') 
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Withdrawls</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">withdrawls</li>
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
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>User</th>  
				  <th>Balance</th>  
                  <th>Amount</th>
				  <th>Account</th>
				  <th>Title</th>
				  <th>Status</th>
				  <th>Channel</th>
				  <th>Created At</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
				@foreach($withdrawls as $withdrawl)
                <tr id="withdrawl{{$withdrawl->id}}">
				 <td>{{$withdrawl->id}}</td>
                 <td>{{$withdrawl->name}}</td>
				 <td>{{$withdrawl->balance}}</td>
				 <td>{{$withdrawl->amount}}</td>
				 <td>{{$withdrawl->account}}</td>
				 <td>{{$withdrawl->account_title}}</td>
				 <td>{{$withdrawl->status}}</td>
				 <td>{{$withdrawl->channel}}</td>
				 <td>{{$withdrawl->created_at}}</td>
				 <td>
					<button class="btn btn-primary" onClick="changeWithdrawlStatus({{$withdrawl->id}},2)">Incorrect</button>
					<button href="#" class="btn btn-success" onClick="changeWithdrawlStatus({{$withdrawl->id}},3)">Paid</button>
				 </td>
                </tr>
				@endforeach
                
                </tbody>
                <tfoot>
                <tr>
                  <th>#</th>
                  <th>User</th>  
				  <th>Balance</th>  
                  <th>Amount</th>
				  <th>Account</th>
				  <th>Title</th>
				  <th>Status</th>
				  <th>Channel</th>
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
            $('#example2').DataTable({
                    "pageLength": 25,
					"order":[]
                }
            );
        });
</script>
@stop