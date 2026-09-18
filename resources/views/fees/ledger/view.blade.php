@php
  $classType = Helper::classType();
  //$getSection = Helper::getSection();
  $getCountry = Helper::getCountry();
  $getSetting = Helper::getSetting();
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
        <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp;{{ __('Student Fees Ledger') }}</h3>
        <div class="card-tools">
        <!--<a href="{{url('hostel/collect/fees')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i> Add</a>-->
        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
        </div>
        
        </div>  
            <form id="quickForm" action="{{ url('fees/ledger') }}" method="post" >
                @csrf 
                    <div class="row m-2">

                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>Class</label>
                                    <select class="form-control" id="class_type_id" name="class_type_id">
                                        <option value="">All</option>
                                        @if(!empty($classType))
                                            @foreach($classType as $class)
                                                <option value="{{ $class->id ?? '' }}" {{ $class->id == $search['class_type_id'] ? 'selected' : '' }}>{{ $class->name ?? '' }}</option>
                                            @endforeach
                                        @endif
                                    </select>                 	    
                            </div>
                    	</div>
                    	
                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('fees.From Date') }}</label>
                                    <input type="date" class="form-control " id="starting" name="starting" value="{{ $_POST['starting'] ?? '' }}">                 	    
                            </div>
                    	</div>
                    	<div class="col-md-2">
                            <div class="form-group ">
                                <label>{{ __('fees.To Date') }}</label>
                                    <input type="date" class="form-control " id="ending" name="ending" value="{{ $_POST['ending'] ?? '' }}">
                			</div> 
                        </div>
                    	
            		<div class="col-md-4">
            			<div class="form-group"> 
            				<label>{{ __('messages.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('messages.Ex. Name, Father Name, Mobile, Email, etc.') }}" value="{{$search['name'] ?? ''}}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" style="color: white;">Search</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('messages.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form>        

        
    	<div class="row m-2">
    	    <div class="col-md-12 head_table text-center"></div>
    	    </div>
    	<div class="row m-2">
		    <div class="col-md-12  ">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
          <thead>
          <tr role="row">
                <th>{{ __('messages.Sr.No.') }}</th>
                <!--<th>{{ __('Counter') }}</th>-->
                <th>Admission No.</th>
                <th>Class</th>
                <th>{{ __('Student Name') }}</th>
                <th>{{ __('messages.Fathers Name') }}</th>
                <th>{{ __('messages.Mobile') }}</th>
                <th>{{ __('Total Fees') }}</th>
                <th>{{ __('Total Paid Fees') }}</th>
                <th>{{ __('Paid Fine') }}</th>
                <th>Discount</th>
                <th>{{ __('Pending Fees') }}</th>
                <th>{{ __('messages.Action') }}</th>
            </thead>
            <tbody>
              
            @if(!empty($data))
                @php
                   $i=1;
                   $total_assigned=0;
                   $total_collected=0;
                   $total_discount=0;
                   $total_pending=0;
                   $total_fine=0;
                   
                @endphp

                @foreach ($data  as $item)
    
                    @php
                        $assign_discount = $item['assign_discount'] ?? 0;

                        $paid = DB::table('fees_detail')
                                    ->where('admission_id', $item->id)
                                    ->whereIn('status',[0,1])
                                    ->whereNull('deleted_at')
                                    ->select(
                                        DB::raw('SUM(installment_fine) as total_installment_fine'),
                                        DB::raw('SUM(discount) as total_discount'),
                                        DB::raw('SUM(total_amount) as total_amount')
                                    )
                                    ->first();
                        $fees_counters = DB::table('fees_counters')->where('id', $item->fees_counter_id)->whereNull('deleted_at')->first();
                        //dd($paid);
                        $total_assigned += ($item['total_amount']-$assign_discount ?? 0);
                        $total_collected += ($paid->total_amount ?? 0);
                        $total_discount += ($paid->total_discount ?? 0);
                        $total_fine += $paid->total_installment_fine ?? 0;
                        $total_pending += (($item['total_amount']-$assign_discount ?? 0)-($paid->total_amount ?? 0))-($paid->total_discount ?? 0);
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <!--<td>{{ $fees_counters->name ?? ''  }}</td>-->
                        <td>{{ $item['admissionNo'] ?? ''  }}</td>
                        <td>{{ $item['className'] ?? '' }}</td>
                        <td>{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}</td>
                        <td>{{ $item['father_name'] ?? ''  }}</td>
                        <td>{{ $item['mobile']  }}</td>
                        <!--<td>{{ $item['slip_no']  }}</td>-->
                        <td>₹ {{ number_format($item['total_amount']-$assign_discount,2) ?? '' }}</td>
                        <td>₹ {{ number_format($paid->total_amount ,2) ?? '' }}</td>
                        <td>₹ {{ number_format($paid->total_installment_fine ,2) ?? '' }}</td>
                        <td>₹ {{ number_format($paid->total_discount ,2) ?? '' }}</td>
                        <td>₹ {{ number_format(($item['total_amount']-$assign_discount) - $paid->total_amount ,2)  }}</td>
                        <td>
                            <button type="button" class="btn btn-primary data {{ Helper::permissioncheck(11)->view ? '' : 'd-none' }}" data-id="{{ $item->id ?? '' }}" data-toggle="modal" data-target="#exampleModal" data-whatever="@mdo"><i class="fa fa-eye"></i></button>
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
    $(document).ready(function(){
        $(".data").click(function(){
            var id = $(this).data("id");
            var basurl = "{{ url('/') }}";
            $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl +'/fees_ledger_view',
                data: {admission_id:id},
                dataType: 'json',
                success: function (response) {
                    $(".response").html(response.html);
                }
            }); 
        });
                
              
        $(".btn-print").click(function(){
            var content = $(".response").html();  
         
            var leftLogo = "{{ env('IMAGE_SHOW_PATH') . '/setting/left_logo/' . $getSetting['left_logo'] }}"; 
            var name = "{{ $getSetting['name'] ?? '' }}";  
          
            var htmlcontent = '<table><tr><td width="150"><img width=100  src="' + leftLogo + '" /></td><td><h1 class="fontModal">' + name + '</h1></td></tr></table>';
          
            var printWindow = window.open('', '', 'height=600,width=900'); 
            printWindow.document.write('<style>');
            printWindow.document.write(' body {border: 1px solid black; padding: 8px; }');  // Add table borders
            printWindow.document.write('.fontModal { font-size: 59px; text-align: center;font-family: emoji; }');
            printWindow.document.write('@media print { .col-md { width: 100%; } }');
            printWindow.document.write('</style>');
            printWindow.document.write('<html><head><title>Ledger History</title>');
            printWindow.document.write('<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">');
            printWindow.document.write('<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">');
            printWindow.document.write('</head><body>');
            /* printWindow.document.write('<table><tr><td width="200"> <img src="env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo']"></td><td><h1 class="fontModal">{{$getSetting['name'] ?? ''}}</h1></td></tr></table>'); */ 
            printWindow.document.write(htmlcontent); 
            printWindow.document.write(content);  
            printWindow.document.write('</body></html>');
            printWindow.document.close();  // Close the document for the print window
            printWindow.print();  // Trigger the print dialog
        });
    });
</script>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document" >
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="exampleModalLabel">Ledger History</h5>&nbsp;&nbsp;&nbsp;&nbsp;
       
              <button class="btn btn-success btn-xs btn-print" title="Print Ledger History"><i class="fa fa-print"></i>&nbsp;&nbsp; Print </button>
     
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body response">
       
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
 $(document).ready(function(){

var  total_assigned =  "{{number_format($total_assigned ,2) }}";
var  total_collected =  "{{number_format($total_collected ,2) }}";
var  total_discount =  "{{number_format($total_discount ,2) }}";
var  total_pending =  "{{number_format($total_pending ,2) }}";
var  total_fine =  "{{number_format($total_fine ,2) }}";
                 
                 
                
$('.head_table').append('<table class="table table-bordered table-striped"><tr><td class="bg-primary">Total Fee</td><td>₹ '+total_assigned+'</td><td class="bg-primary">Total Discount</td><td >₹ '+total_discount+'</td><td class="bg-primary">Total Collected</td><td>₹ '+total_collected+'</td><td class="bg-primary">Total Pending</td><td>₹ '+total_pending+'</td><td class="bg-primary">Total Fine</td><td>₹ '+total_fine+'</td></tr><table>');
    });
</script>
       
<style>
    .label-success-custom {
    border: #47a447 1px solid;
    color: #47a447;
    
}
.btn-print{
        margin: 3px;
    margin-left: 17px;
    font-size: 13px;
}
.label-danger-custom {
    border: #d2322d 1px solid;
    color: #d2322d;
}
</style>
@endsection 