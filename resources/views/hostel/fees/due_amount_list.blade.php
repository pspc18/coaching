@php
  $getPermission = Helper::getPermission();  
  $setting = DB::table('settings')->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->first();
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;Payment Due</h3>
        <div class="card-tools">
                <a href="{{url('/')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  
        
               

        
    	<div class="row mb-2 mt-1">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
                    <th>Transaction ID</th>
                    <th>Name</th>
                    <th>Moblie</th>
                    <th>Admission Number</th>
                    <th>Invoice Amount (₹)</th>
                    
                    <th>Paid Amount (₹)</th>
                    <th>Discount (₹)</th>
                    <th>Dues (₹)</th>
                    <th>Paid for</th>
                    <th>Payment Date</th>
                    @if($getPermission->download == 1)
                     <th>{{ __('common.Action') }}</th>
                    @endif
          </thead>
          <tbody id="fees_list_show">
              
              @if(!empty($data))
                @php
              
                   $i=1;
                @endphp

                @foreach ($data  as $item)
                <tr>
                <td>{{ $item['invoice_no'] ?? '-'  }}</td>
                <td>{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}</td>
                <td>{{ $item['mobile'] ?? '-'  }}</td>
                <td>{{ $item['admissionNo'] ?? ''  }}</td>
                
                <td>{{ $item['total_amount'] ?? '-' }}</td>
                
                <td><span class="btn-success p-1">{{ $item['paid_amount'] ?? '' }}</span> </td>
                <td>{{ $item['discount'] ?? '-' }}</td>
                <td> <span class="btn-danger p-1">{{ $item['due_amount'] ?? '0' }}</span> </td>
                
                
                
                <td>
                   {{ date("d-M-Y", strtotime($item['hostel_renewal_date']))  ?? '' }} 
                </td>
                <td>{{ date("d-M-Y", strtotime($item['created_at']))  ?? '' }}</td>
                <td>
                    <a href="{{ url('hostel_invoice') }}/{{ $item['invoice_no'] ?? '' }}/{{ $item['admission_id'] ?? '' }}" class="btn btn-xs btn-primary"><i class="fa fa-file"></i></a>
                    <a href="{{ url('hostel_due_amount_pay') }}/{{ $item['id'] ?? '' }}" class="btn btn-xs btn-danger ">Pay Dues</a>
                    <button class="btn btn-primary btn-xs send_sms" data-admission_id="{{ $item['admission_id'] ?? '' }}" data-name="{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}"
                            data-due_fees="{{ $item['due_amount'] ?? '' }}" data-mobile="{{ $item->mobile ?? '' }}"><i class="fa fa-whatsapp"></i></button>
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

<div class="modal fade" id="send_sms">
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content">
    <div class="modal-header">
      <h4 class="modal-title">Send Message</h4>
      <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
    </div>
    <form action="{{ url('hostel_due_amount_reminder') }}" method="post">
        @csrf
        <div class="modal-body">
            <div class="col-md-12">
                <div class="flex_centered">
                    <p class="mb-0"><b>Name : <span id="name"></span></b></p>
                    <p class="mb-0"><b>Mobile : <span id="mobile"></span></b></p>
                </div>
            </div>
            
            <input type="hidden" id="admission_id" name="admission_id">
            
            <hr>
    
            <div class="col-md-12">
                <p><b>Reminder Message:</b></p>
                <textarea type="text" class="form-control" rows="4" id="reminder_message" name="reminder_message" placeholder="Reminder Message"></textarea>
            </div>
        </div>
    
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Send Message</button>
        </div>
    </form>
  </div>
</div>
</div>
  
  

<script>
    $(document).ready(function(){
        var branch_name = "{{ $setting->name ?? '' }}";
        $('.send_sms').click(function(){
            var name = $(this).data('name');
            var mobile = $(this).data('mobile');
            var fees = $(this).data('due_fees');
            var admission_id = $(this).data('admission_id');
            
            $('#admission_id').val(admission_id);
            $('#name').html(name);
            $('#mobile').html(mobile);
            var code = "Dear " + name + ", this is the reminder that your payment is due against your membership (due Rs. " + fees + "). Requesting you to pay at the earliest."
               + "Regards " + branch_name;
            $('#reminder_message').val(code);
            $('#send_sms').modal('show');
            
        }); 
        
    });
</script>
        
        

<style>
    table{
        font-size: 14px;
    }
    
    .flex_centered{
        display:flex;
        align-items:center;
        justify-content: space-between;
    }
</style>

@endsection 