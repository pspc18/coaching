@php
  $classType = Helper::classType();
  //$getSection = Helper::getSection();
  $getCountry = Helper::getCountry();
  $role = Helper::roleType();
  $actionPermission = Helper::actionPermission();
  //dd($data);
@endphp

@extends('layout.app') 
@section('content')

<style>
    
    .padding_table thead tr{
    background: #002c54;
    color:white;
}
    
.padding_table th, .padding_table td{
     padding:5px;
     font-size:14px;
}
</style>

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp;{{ __('Fees Cheque') }}</h3>
        <div class="card-tools">
        <!--<a href="{{url('hostel/collect/fees')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i> Add</a>-->
        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
        </div>
        
        </div>  
        
        
    	<div class="row m-2">
    	    <div class="col-md-12 head_table text-center"></div>
    	    </div>
    	<div class="row m-2">
		    <div class="col-md-12  ">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
          <thead>
          <tr role="row">
            <th>{{ __('messages.Sr.No.') }}</th>
            <th>Receipt No.</th>
            <th>Status</th>
            <th>Admission No.</th>
              <th>Class</th>
                <th>{{ __('Student Name') }}</th>
            <th>{{ __('messages.Fathers Name') }}</th>
            <th>{{ __('messages.Mobile') }}</th>
            <th>{{ __('Payment date') }}</th>
            <th>{{ __('Payment Modes') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Action') }}</th>
         
          </thead>
         <tbody>
             
             @if(!empty($data))
             @php
             $amount = 0;
             @endphp
             @foreach($data as $key => $receipt)
              
             <tr>
                 
                 <td>{{$key+1}}</td>
               
                 <td class='d-flex'>
                  
                     <form action={{url('printFeesInvoice')}} method="post" target="_blank">
                         @csrf
                         <button class="text-primary" type="submit" id="fees_details_invoice_id" style="border: none; background: transparent; border-bottom: 2px solid #1f2d3d;" name="fees_details_invoice_id" value="{{$receipt->id ?? ''}}">{{$receipt->invoice_no ?? ''}}</button>
                     </form>
                    
                 </td>
                 <td>
                    @if($receipt->status == 0)
                        <span style="color: green;">Cheque Released</span>
                    @elseif($receipt->status == 1)
                        <span style="color: red;">Cheque Pending Realisation</span>
                    @elseif($receipt->status == 2)
                       <span style="color: red;">Cheque Dishonoured </span>
                    @endif
                </td>
                 <td>{{$receipt->admissionNo ?? ''}}</td>
                 <td>{{$receipt->class_name ?? ''}}</td>
                 <td>{{$receipt->first_name ?? ''}} {{$receipt->last_name ?? ''}}</td>
                 <td>{{$receipt->father_name ?? ''}}</td>
                 <td>{{$receipt->mobile ?? ''}}</td>
                 <td>{{date('d-m-Y', strtotime($receipt->payment_date ?? ''))}}</td>
                 <td>{{$receipt->payment_mode ?? ''}}</td>
                 <td>
                    {{$receipt->amount ?? ''}}
                    @php
                    $amount +=$receipt->amount;
                    @endphp
                 </td>
                 	<td> 
                        <select name="status" data-id="{{ $receipt['id'] ?? '' }}" class="form-control statusDrop ">
                              <option value="0" {{ $receipt->status == 0 ? 'selected' : '' }}>Cheque Realised </option>
                              <option value="1" {{ $receipt->status == 1 ? 'selected' : '' }}>Cheque Pending Realisation</option>
                              <option value="2" {{ $receipt->status == 2 ? 'selected' : '' }}> Cheque Dishonoured</option>
                     </select>
                        
					</td>
             </tr>
             
             @endforeach
              
             @endif
         </tbody>
         <tfoot>
 <tr>
                 
                 <td colspan="10" style="text-align: right;"> Total Amount</td>
               
                 <td colspan="2"> {{$amount ??  ''}} </td>
              
             </tr></tfoot>
        </table>
        </div>
        </div>
    </div>

    </div>
  </div>
</div>
</section>
</div>

<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
<script>
$('.profileImg').click(function(){
    var profileImgUrl = $(this).data('img');
    if(profileImgUrl != ''){
        $('#profileImgModal').modal('toggle');
        $('#profileImg').attr('src',profileImgUrl);
    }
});
</script>
 <div id="profileImgModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <div class="modal-content">
      <!--<div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
      </div>-->
      <div class="modal-body">
        <img id="profileImg" src="" width="100%" height="470px">
      </div>
     <!-- <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>-->
    </div>

  </div>
</div>       

<div class="modal fade" id="statusModal">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #ffffff;"> <!-- Changed background to white -->
      <div class="modal-header">
        <h4 class="modal-title text-dark">Change Status Confirmation</h4> <!-- Changed text color to dark -->
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
          <i class="fa fa-times" aria-hidden="true"></i>
        </button>
      </div>

      <form action="{{ url('fees_cheque') }}" method="post">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="status_id" name="status_id">
          <input type="hidden" id="id" name="id">
         
          <h5 class="text-dark" id='status_message'>Are you sure you want to change the status?</h5> <!-- Changed text color to dark -->
          
          <div class='form-group'>
              <label for="remark" class='form-label'>Remark</label> <!-- Added 'for' attribute for better accessibility -->
              <textarea id="remark" name="remark" class="form-control" rows="3" placeholder="Enter your remarks here..."></textarea> <!-- Added form-control for styling -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> <!-- Changed button color to secondary -->
          <button type="submit" class="btn btn-danger">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    $(document).ready(function(){
        
        $('.statusDrop').change(function(){
           var status = $(this).val(); 
            $('#status_id').val(status);
            $('#id').val($(this).data('id'));
           
           
           if(status == 2)
           {
            //   $('#status_message').html('This cheque has been dishonoured by the bank for.the.reason.…');
               $('#remark').html('This cheque has been dishonoured by the bank for.the.reason.…');
           }
           else if(status == 0)
           {
              // $('#status_message').html('Cheque has been realised');  
                     $('#remark').html('Cheque has been realised');
           }
           else
           {
              // $('#status_message').html('This cheque is pending.realisation');  
                     $('#remark').html('This cheque is pending realisation');
           }
           
           
            $('#statusModal').modal('show');
        });
        
        $('.deleteData').click(function() {
        	var delete_id = $(this).data('id');
        	$('#delete_id').val(delete_id);
        });
        
        $('.userStatus').click(function(){
            var status = $(this).data('status');
            $('#status_id').val(status);
            $('#id').val($(this).data('id'));
        });
    });
</script>
@endsection 