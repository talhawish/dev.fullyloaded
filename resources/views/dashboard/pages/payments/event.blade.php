@extends('layouts.master') 
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Event Payments</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Payments/(event/checkin)</li>
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
			  <!--<a href="/dashboard/category/add" class="btn btn-primary">Add New Category</a>-->
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>amount</th>
				  <th>charges</th>
				  <th>eventID</th>
				  <th>transactionID</th>
				  <th>ticketID</th>
				  <th>type</th>
				  <th>Paid At</th>
                </tr>
                </thead>
                <tbody>
				@foreach($payments as $payment)
                <tr id="payment{{$payment->id}}">
				 <td>{{$payment->id}}</td>
				 <td>{{$payment->name}}</td>
                 <td>${{$payment->amount}}</td>
				 <td>${{$payment->amount-(($payment->amount*0.9))+1}}</td>
				 <td>{{$payment->event_id}}</td>
				 <td>{{$payment->transactionID}}</td>
				 <td>{{$payment->ticketID}}</td>
                 <td>{{$payment->status}}</td>
				 <td>{{$payment->created_at}}</td>
                </tr>
				@endforeach
                
                </tbody>
                <tfoot>
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>amount</th>
				  <th>charges</th>
				  <th>eventID</th>
				  <th>transactionID</th>
				  <th>ticketID</th>
				  <th>type</th>
				  <th>paid At</th>
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
<script>
       $(document).ready(function () {
            $('#example2').DataTable({
                    "pageLength": 25,
					 "order": []
                }
            );
        });
</script>
@stop