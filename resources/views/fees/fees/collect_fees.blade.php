@php
    $all_class = Helper::getclass();
    $classType = Helper::classType();
    $feesDiscount =Helper::feesDiscount();
    $getPaymentMode =Helper::getPaymentMode();
@endphp
@extends('layout.app') 
@section('content')
    <iframe style="display:none;" src='../print_file.student_print.print_fees' id='myFrame' frameborder='0' style='border:0;' width='300' height='300'></iframe>
    
 <div class="content-wrapper">
    <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Collect Student Fees</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('Fees/add')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>
    
    <div class="card m-2">
        <div class"card-body">
                <div class="row m-2">
                    <div class="col-md-12"><h5>Student Details</h5><hr></div>
                    <div class="col-md-2 text-center">
                        @if(!empty($data['student_img']))
                        <img src="{{ env('IMAGE_SHOW_PATH').'student_image/'.$data['student_img'] }}" width="110" height="110" style="border:2px solid;">
                        @else
                        <img width="115" height="115" class="round5" src="https://demo.smart-school.in/uploads/student_images/no_image.png" alt="No Image">
                        @endif
                    </div>
                    <div class="col-md-10">
                        <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                                <tr role="row">
                                  <td><b>Name</b></td>
                                   <td>{{ $data['name'] ?? '' }}</td>
                                   <td><b>Class</b></td>
                                  <td>{{ $data['ClassTypes']['name'] ?? '' }} ({{ $data['Section']['name'] ?? '' }})</td>
                                </tr>
                                <tr role="row">
                                  <td><b>Father Name</b></td>
                                   <td>{{ $data['father_name'] ?? '' }}</td>
                                   <td><b>Admission No</b></td>
                                  <td>{{ $data['admissionNo'] ?? '' }}</td>
                                </tr>
                                <tr role="row">
                                  <td><b>Mobile Number</b></td>
                                   <td>{{ $data['mobile'] ?? '' }}</td>
                                   <td><b>Roll Number</b></td>
                                  <td>{{ $data['roll_no'] ?? '' }}</td>
                                </tr>  
                        </table>                        
                    </div>
                </div>
        </div>

        <div class="row ">
                <div class="card-body">
                    <div class="row m-2"><a href="{{url('#')}}" class="btn btn-warning  btn-xs"><i class="fa fa-money"></i> Collect Selected</a></div>
                <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th><input type="checkbox" id="select_all" name="" value="" class="checkbox"> &nbsp; Fees Group</th>
                      <th>Fees Type</th>
                       <th>Due Date</th>
                       <th>Status</th>
                      <th>Amount ₹</th>
                      <th>Mode</th>
                      <th>Date</th>
                      <th>Discount ₹</th>
                      <th>Paid ₹</th>
                      <th>Balance ₹</th>
                      <th>Action</th>
                      
                      
                      
                  </thead>
                  <tbody class="">
                    @if(!empty($dataview))
                        @foreach ($dataview  as $item)<!--fees_assign_students data-->
                            
                            @php
                                $fees_type = Helper::feestypeGet($item['class_type_id'],$item['fees_group_id']); 
                            @endphp
                            @foreach ($fees_type  as $itemview)<!-- fees_master data-->
                            @php
                                $StuFeesDetail = Helper::getStuFeesDetail($item['fees_group_id'],$itemview['fees_type_id'],$item['admission_id']); 
                            @endphp                     
                            <tr>
                                <input  type="hidden" class="form-control" id="std_id_{{$itemview->id}}" value="{{$item['admission_id']}}">
                                <input  type="hidden" class="form-control" id="fees_group_id_{{$itemview->id}}" value="{{$item['fees_group_id']}}">
                                <input  type="hidden" class="form-control" id="fees_type_id_{{$itemview->id}}" value="{{$itemview['fees_type_id']}}">
                                <input  type="hidden" class="form-control" id="amount_{{$itemview->id}}" value="{{$itemview['amount'] ?? ''}}">
                                <input  type="hidden" class="form-control" id="due_date_{{$itemview->id}}" value="{{$itemview['due_date'] ?? ''}}">
                                
                                <td><input type="checkbox" id="select_all" name="" value="" class="checkbox"> &nbsp; <span id="FeesGroup_name_{{$itemview->id}}">{{ $itemview['FeesGroup']['name'] ?? '' }}</span></td>
                                <td><span id="FeesType_name_{{$itemview->id}}">({{ $itemview['FeesType']['name'] ?? '' }})</span></td>
                                <td>{{ $itemview['due_date'] ?? '' }}</td>
                                <td>@if($StuFeesDetail['id'] == null)
                                    <span class="btn-danger  btn-xs">Unpaid</span>
                                    @else
                                    <span class="btn-success  btn-xs">&nbsp;&nbsp;&nbsp;Paid&nbsp;&nbsp;</span>
                                    @endif
                                </td>
                                <td>{{ $itemview['amount'] ?? '' }}</td>
                                <td>{{ $StuFeesDetail['PayMode']['name'] ?? '' }}</td>
                                <td>{{ $StuFeesDetail['collect_date'] ?? '' }}</td>
                                <td>{{ $StuFeesDetail['discount'] ?? '' }}</td>
                                <td>{{ $StuFeesDetail['pay_amt'] ?? '' }}</td>
                                <td>{{ $StuFeesDetail['mobile'] ?? '' }}</td>
                                <td>
                                  
                                 @if($StuFeesDetail['id'] == null) 
                                    <a href="javascript:;" data-id='{{$itemview->id}}'  class="btn btn-primary  btn-xs add_fees" title="Add Fees" data-toggle="modal" data-target="#myModal"><i class="fa fa-plus"></i></a>
                                 @else
                                    <a href="javascript:;" data-id='{{$StuFeesDetail->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-danger  btn-xs" title="Revert Fees"><i class="fa fa-undo"></i></a>
                                 @endif
                                    &nbsp; &nbsp; <a href="#" id="bt" class="btn btn-success  btn-xs" onclick="print()"><i class="fa fa-print"></i></a>
                                </td>
                            </tr>
                            @endforeach
                       @endforeach
                    @endif                  
                  </tbody>
                  </table>
              </div>
      </div>            
  

    </div>
</div>    


<script>

//select all checkboxes
    $("#select_all").change(function () {  
        $(".checkbox").prop('checked', $(this).prop("checked")); 
    });

//".checkbox" change 
    $('.checkbox').change(function () {
        if (false == $(this).prop("checked")) { 
            $("#select_all").prop('checked', false);
        }
        if ($('.checkbox:checked').length == $('.checkbox').length) {
            $("#select_all").prop('checked', true);
        }
    });

</script>

<script>
  $('.add_fees').click(function() {
    
  var add_fees_id = $(this).data('id');
  var std_id = $('#std_id_'+add_fees_id).val();
  var fees_group_id = $('#fees_group_id_'+add_fees_id).val();
  var fees_type_id = $('#fees_type_id_'+add_fees_id).val();
  var amount = $('#amount_'+add_fees_id).val();
  var due_date = $('#due_date_'+add_fees_id).val();
  var FeesGroup_name= $('#FeesGroup_name_'+add_fees_id).text();
  var FeesType_name= $('#FeesType_name_'+add_fees_id).text();

  $('#admission_id').val(std_id); 
  $('#fees_group_id').val(fees_group_id); 
  $('#fees_type_id').val(fees_type_id); 
  $('#amount').val(amount); 
  $('#due_date').val(due_date); 
  $('.FeesGroup_name').text(FeesGroup_name);
  $('.FeesType_name').text(FeesType_name); 
  } );
</script>
  <div class="modal fade mt-5" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
      <div class="modal-content">
        <div class="modal-header">
            
            <h4><span class="FeesGroup_name"></span> <span class="FeesType_name"></span></h4>   
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">x</button>
        </div>
        <form action="{{ url('fees_collect') }}" method="post">
            @csrf
            <div class="modal-body">
                <div class="row">
                     <input  type="hidden" class="form-control" id="admission_id" name="admission_id" value="" readonly="readonly"/>
                     <input  type="hidden" class="form-control" id="fees_group_id" name="fees_group_id" value="" readonly="readonly"/>
                     <input  type="hidden" class="form-control" id="fees_type_id" name="fees_type_id" value="" readonly="readonly"/>
                     <input  type="hidden" class="form-control" id="due_date" name="due_date" value="" readonly="readonly"/>

                    <div class="col-md-4">
                        <label style="color:red;">Date*</label>
                        <input class="form-control" type="date" id="date" name="date" value="{{date('Y-m-d')}}" required>
                    </div>
                    <div class="col-md-4">
                        <label style="color:red;">Amount*</label>
                        <input class="form-control" type="text" id="amount" name="amount" placeholder="Amount" required readonly>
                    </div>     
                    <div class="col-md-4">
                        <label>Discount Group</label>
                        <select class="form-control" id="dis_group_id" name="dis_group_id">
                            <option value="">Select</option>
                            @if(!empty($feesDiscount))
                                @foreach($feesDiscount as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>   
                    <div class="col-md-4">
                        <label>Discount</label>
                        <input type="text" class="form-control discount" id="discount" placeholder="Discount" name="discount" readonly>
                    </div>    
                    <div class="col-md-4">
                        <label>Pay Final</label>
                        <input type="text" class="form-control" id="pay_amt" placeholder="Pay Final" name="pay_amt" readonly>
                    </div>                      
                    <div class="col-md-4">
                        <label style="color:red;">Payment Mode*</label>
                        <select class="form-control" id="mode" name="mode" required>
                            @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                            @endif  
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label>Note</label>
                        <textarea class="form-control" type="text" id="description" name="description" placeholder="Any Description or Note..."></textarea>
                    </div>                
                </div>
            </div>
        
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary"><i class="fa fa-money"></i> Collect</button>
            </div>
        </form>
      </div>
    </div>
  </div>



<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
<div class="modal" id="Modal_id">
    <div class="modal-dialog">
        <div class="modal-content" style="background: #555b5beb;">
            <div class="modal-header">
                <h4 class="modal-title text-white">Delete Confirmation</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
            </div>

            <form action="{{ url('collect_fees_delete') }}" method="post">
              	@csrf
                <div class="modal-body">
                    <input type=hidden id="delete_id" name=delete_id>
                    <h5 class="text-white">Are you sure you want to delete  ?</h5>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#dis_group_id').on('change', function(e){
    var basurl = "{{ url('/') }}";
	var dis_group_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/discountdata/'+dis_group_id,
	  success: function(data){
      $("#discount").val(data);
	
      var amount = $('#amount').val(); 
        var discount =$('#discount').val();
       var payamt = amount - discount ;
       $("#pay_amt").val(payamt.toFixed(2));
	  }
	});
	
});

</script>
<script>
	let print = () => {
    	let objFra = document.getElementById('myFrame');
        objFra.contentWindow.focus();
        objFra.contentWindow.print();
    }
</script>
   @endsection    