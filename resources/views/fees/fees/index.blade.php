@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp; Fees Details</h3>
        <div class="card-tools">
        <a href="{{url('Fees/add')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i> Add</a>
        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
        </div>
        
        </div>     
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>Sr. No.</th>
                    <th>Student Name</th>
                    <th>Father's Name</th>
                    <th>Class</th>
                    <!--<th>Total Amount</th>
                    <th>Discount</th>-->
                    <th>Paid Amount</th>
                    <th>Pay Mode</th>
                     <th>Action</th>
          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1
                @endphp
                @foreach ($data  as $item)
                <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $item['Student']['first_name'] ?? ''  }} {{ $item['Student']['last_name'] ?? ''  }}</td>
                        <td>{{ $item['Student']['father_name'] ?? ''  }}</td>
                        <td>{{ $item['ClassTypes']['name'] ?? ''  }} ({{ $item['Section']['name'] ?? ''  }})</td>
                        <!--<td>{{ $item['net_amount']  }}</td>
                        <td>{{ $item['total_dis']  }}</td>-->
                        <td>{{ $item['pay_amt']  }}</td>
                        <td>{{ $item['PaymentMode']['name']  }}</td>
                        <td>
                            <a href="{{url('print_payement',$item->id)}}" target="blank" class="btn btn-success  btn-xs tooltip1" title1="Fees Print"><i class="fa fa-print"></i></a>
                            <a href="{{url('#',$item->id)}}" class="btn btn-primary  btn-xs ml-3 tooltip1" title1="Edit Fees"><i class="fa fa-edit"></i></a>
                            <a href="{{url('#',$item->id)}}" class="btn btn-danger  btn-xs ml-3 tooltip1" title1="Revert Fees"><i class="fa fa-undo"></i></a>
                        </td>
              </tr>
              @endforeach
            @endif
            </tbody>
        </table>
        </div>
        </div>
    </div>
    </div>
  </div>
</div>
</section>
</div>
        
        

        
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('delete_marksheet') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
         </div>
       </form>

    </div>
  </div>
</div>

@endsection 