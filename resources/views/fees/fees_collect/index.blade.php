@php
  $classType = Helper::classType();
  //$getSection = Helper::getSection();
  $getCountry = Helper::getCountry();
  $getAllUsers = Helper::getAllUsers();
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
        <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp;{{ __('  Daily Collection  ') }}</h3>
        <div class="card-tools">
        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
        </div>
        
        </div>  
        
        
        <form id="quickForm" action="{{ url('fees/index') }}" method="post" >
                @csrf 
                    <div class="row m-2">
                    @if(Session::get('role_id') == 1)
                    <div class="col-md-1 ">
                        <div class="form-group">
                            <label>{{ __('Users') }}</label>
                            <select class="select2 form-control" id="user_id" name="user_id">
                            <option value='' >All Users </option>
                            @if(!empty($getAllUsers))
                            @foreach($getAllUsers as $getCoun)
                            <option value="{{ $getCoun->id ?? ''  }}" {{ ($getCoun->id == $serach['user_id']) ? 'selected' : '' }}>{{$getCoun->first_name ?? ''}} {{$getCoun->last_name ?? ''}}</option>
                            @endforeach
                            @endif
                            </select>
                        </div>
                    </div>   
                     @endif
                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('fees.From Date') }}</label>
                                    <input type="date" class="form-control " id="starting" name="starting" value="{{ $serach['starting'] ?? '' }}">                 	    
                            </div>
                    	</div>
                    	<div class="col-md-2">
                            <div class="form-group ">
                                <label>{{ __('fees.To Date') }}</label>
                                    <input type="date" class="form-control " id="ending" name="ending" value="{{ $serach['ending'] ?? '' }}">
                			</div> 
                        </div>
                    	
                                	
                        <div class="col-md-1 ">
                             <label for="" style="color: white;">Search</label>
                    	    <button type="submit" class="btn btn-primary" srtle="">{{ __('messages.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form>   
          

        
    	<div class="row m-2">
    	    <div class="col-md-12 head_table text-center"></div>
    	    </div>
    	<div class="row">
		     <div class="col-12" id="downloadLeaflet">	
		<div class="table-responsive">
        <table id="example11" class="table table-bordered table-striped dataTable dtr-inline padding_table">
          <thead>
          <tr role="row">
            <th>{{ __('messages.Sr.No.') }}</th>
            <th>Collect By</th>
            <th>Receipt No.</th>
            <th>Offline Receipt No.</th>
            <th>Status</th>
            <th>Admission No.</th>
              <th>Class</th>
                <th>{{ __('Student Name') }}</th>
            <th>{{ __('messages.Fathers Name') }}</th>
            <th>{{ __('Payment date') }}</th>
            <th>{{ __('Payment Modes') }}</th>
            <th>{{ __('Discount') }}</th>
            <th>{{ __('Fine') }}</th>
            <th>{{ __('Amount') }}</th>
            <th>{{ __('Total Amount') }}</th>
         
          </thead>
         <tbody>
             
             @if(!empty($data))
             @php
            $cash = 0;
            $cheque = 0;
            $net_banking = 0;
            $upi = 0;
           
             @endphp
             @foreach($data as $key => $receipt)
            
             
             
                @php
             
                   if($receipt->payment_mode_id == 1){
                      $cash += $receipt->amount+$receipt->total_fine;
                   }elseif($receipt->payment_mode_id == 2){
                      $cheque += $receipt->amount+$receipt->total_fine;
                   }elseif($receipt->payment_mode_id == 3){
                     $net_banking += $receipt->amount+$receipt->total_fine;
                   }else{
                     $upi += $receipt->amount;
                   }
                   
                  // dd($receipt);
                   
                @endphp

             
             <tr>
                 
                 <td>{{$key+1}}</td>
                 <td>{{$receipt->users_first_name ?? ''}} {{$receipt->users_last_name ?? ''}}</td>
                <td class='d-flex'>
                     <form action={{url('printFeesInvoice')}} method="post" target="_blank">
                         @csrf
                         <button class="text-primary" type="submit" id="fees_details_invoice_id" style="border: none; background: transparent; border-bottom: 2px solid #1f2d3d;" name="fees_details_invoice_id" value="{{$receipt->id ?? ''}}">{{$receipt->invoice_no ?? ''}}</button>
                     </form>
                 </td>
                 <td>{{ $receipt->offline_receipt_no ?? '' }}</td>
                 <td>
                    @if($receipt->status == 0)
                        <span style="color: green;">Received</span>
                    @elseif($receipt->status == 1)
                        <span style="color: red;">Pending</span>
                    @elseif($receipt->status == 2)
                       <span style="color: red;">Cancelled</span>
                    @endif
                </td>
           
                 
                 <td>{{$receipt->admissionNo ?? ''}}</td>
                 <td>{{$receipt->class_name ?? ''}}</td>
                 <td>{{$receipt->first_name ?? ''}} {{$receipt->last_name ?? ''}}</td>
                
                 <td>{{$receipt->father_name ?? ''}}</td>
                 <td>{{date('d-m-Y', strtotime($receipt->payment_date ?? ''))}}</td>
                 <td>{{$receipt->payment_mode ?? ''}}</td>
                 <td>{{$receipt->discount ?? ''}}</td>
                    <td>{{$receipt->total_fine ?? ''}}</td>
                 <td>
            
                    @if($receipt->status == 0  )
                        <span style="color: green;">{{$receipt->amount ?? 0}}</span>
                    @elseif($receipt->status == 1)
                        <span style="color: #fd7e14;">{{$receipt->amount ?? 0}}</span>
                    @elseif($receipt->status == 2)
                       <span style="color: red;">{{$receipt->amount ?? 0}}</span>
                    @endif

                 </td>
                 <td>
            
                    @if($receipt->status == 0  )
                        <span style="color: green;">{{$receipt->amount+$receipt->total_fine ?? 0}}</span>
                    @elseif($receipt->status == 1)
                        <span style="color: #fd7e14;">{{$receipt->amount+$receipt->total_fine ?? 0}}</span>
                    @elseif($receipt->status == 2)
                       <span style="color: red;">{{$receipt->amount+$receipt->total_fine ?? 0}}</span>
                    @endif

                 </td>
              
             </tr>
             
             @endforeach
              
             @endif
         </tbody>
         <tfoot style="font-weight: bold;">
             <tr>
                 <td colspan="13" style="text-align: right;"> Modes</td>
                 <td>Total Amt </td>
             </tr>

             <tr>
                 
                 <td colspan="13" style="text-align: right;"> Cash </td>
               
                 <td> {{$cash ??  ''}} </td>
              
             </tr>
             <tr>
                 
                 <td colspan="13" style="text-align: right;"> Cheque</td>
               
                 <td> {{$cheque ??  ''}} </td>
              
             </tr>
             <tr>
                 
                 <td colspan="13" style="text-align: right;">Net Banking</td>
               
                 <td> {{$net_banking ??  ''}} </td>
              
             </tr>
             <tr>
                 
                 <td colspan="13" style="text-align: right;">UPI</td>
               
                 <td> {{$upi ??  ''}} </td>
              
             </tr>
             <tr>
                  
                 <td colspan="13" style="text-align: right;">Total Amount</td>
               
                 <td> {{$upi+$net_banking+$cheque+$cash ??  ''}} </td>
              
             </tr></tfoot>
        </table>
        </div>
        <div style="text-align:center;">
                 <button class="btn btn-sm btn-success my-2" id="printFile" style="width:170px;"><i class="fa-fa-print"aria-hidden="true"></i> Print</button>
            </div>
        </div>
        </div>
    </div>

    </div>
  </div>
</div>
</section>
</div>

<style>
    /* Table header style */
.padding_table thead tr {
    background: #002c54;
    color: white;
}

/* Table padding */
.padding_table th, 
.padding_table td {
    padding: 5px;
    font-size: 14px;
    white-space: nowrap; /* prevent text breaking */
}

/* Wrapper to enable responsive scroll */
.table-responsive {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    border-radius: 6px;
}

/* Search form spacing */
#quickForm .form-group label {
    font-size: 13px;
    font-weight: 600;
}

/* Button fix */
#quickForm button {
    margin-top: 28px;
}

/* Print button alignment */
#printFile {
    width: 170px;
}

/* --- Responsive Breakpoints --- */
@media (max-width: 992px) {
    .padding_table th, .padding_table td {
        font-size: 13px;
    }
    #quickForm .col-md-1, 
    #quickForm .col-md-2 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 768px) {
    .padding_table th, .padding_table td {
        font-size: 12px;
        padding: 4px;
    }
    #quickForm .col-md-1, 
    #quickForm .col-md-2 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    #quickForm button {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .padding_table th, .padding_table td {
        font-size: 11px;
        padding: 3px;
    }
    .card-header h3 {
        font-size: 16px;
    }
    .card-header .btn {
        font-size: 12px;
        padding: 4px 8px;
    }
}

</style>


<script>
$(document).ready(function() {
    $("#printFile").click(function() {
        printContent();
    });
});

function printContent() {
    var styles = 'Daily Collection  ';

    $(document).ready(function() {
        $('style, link[rel="stylesheet"]').each(function() {
            styles += $(this).prop('outerHTML');
        });
        var content = $("#downloadLeaflet").html();
        var printWindow = window.open('', '_blank');
        printWindow.document.write('<html><head><title>Student Data</title>' + styles + '</head><body style="margin-left:10px;">');
        printWindow.document.write(content);
        printWindow.document.write('</body></html>');
            
        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    });
}

</script>
     

@endsection 