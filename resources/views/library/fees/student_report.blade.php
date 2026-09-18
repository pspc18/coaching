@php
  $classType = Helper::classType();
  $getCountry = Helper::getCountry();
  $getPermission = Helper::getPermission();

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
        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Library Fees Ladger') }}</h3>
        <div class="card-tools">
                <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
        </div>
        
        </div>  
     
         <form id="quickForm" action="{{ url('library/student/report') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                        <div class="col-md-12 p-0">
                        <div class="col-md-4">
    						<div class="form-group">
    							<label>Student's</label>
    							<select class="form-control select2" name="library_assign_id" id="library_assign_id" onchange="this.form.submit()">
    								<option value="">{{ __('common.Select') }}</option>
    								@if(!empty($all_student))
    								@foreach($all_student as $item)
    								<option value="{{ $item->id ?? ''  }}" {{ ($item->id == $search['library_assign_id']) ? 'selected' : '' }}>{{ $item->admissionNo." --". $item->first_name ?? ''  }}</option>
    								@endforeach
    								@endif
    							</select>
    						</div>
    					</div>
					</div>
                    </div>
                </form>        

    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>{{ __('Invoice No.') }}</th>
                     <th>Admission No</th>
                    <th>{{ __('library.Student Name') }}</th>
                    <th>Mobile</th>
                    <th>Library Amount</th>
                    <th>Locker Amount</th>
                    <th>Paid Amount</th>
                    <th>Total Amount</th>
                    <th>Total Discount</th>
                    @if($getPermission->download == 1)
                     <th>{{ __('common.Action') }}</th>
                    @endif
          </thead>
          <tbody id="fees_list_show">
              @if(!empty($data))
                @php
                   $i=1
                @endphp

                @foreach ($data  as $item)
                
             @php
             
             @endphp
                <tr>
                        <td>{{ $item['invoice_no'] ?? '' }}</td>
                        <td>{{$item['admissionNo'] ?? ''}}</td>
                        <td>{{ $item['first_name'] ?? ''  }} {{ $item['last_name'] ?? ''  }}</td>
                        <td>{{ $item['mobile'] ?? '-'  }}</td>
                        <td>{{ $item['total_amount'] - $item['library_locker_amount'] }}</td>
                        <td>{{ $item['library_locker_amount'] != 0 ? $item['library_locker_amount'] : "-"  }}</td>
                        <td>{{ $item['paid_amount'] ?? '0'  }}</td>
                        <td>{{ $item['total_amount'] ?? '0'  }}</td>
                        <td>{{ $item['discount'] != 0 ? $item['discount'] : "-"}}</td>
                       
                        @if($getPermission->download == 1)
                        <td>
                            <a href="{{ url('invoice') }}/{{ $item['invoice_no'] ?? '' }}/{{ $item['admission_id'] ?? '' }}" class="btn btn-xs btn-primary"><i class="fa fa-print"></i></a>
                        </td>
                        @endif
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
        
        


<div class="modal" id="myModal">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content">
        <div class="modal-header">
            
            <h4 class="text-center" style="width:100%;">{{ __('common.Class') }}: <span id="class_type_id1"></span> (<span id="section_id1"></span>) &nbsp; &nbsp; {{ __('common.Name') }}: <span id="first_name"></span></h4>   
                  <button type="button" id="closeModal"class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
        </div>
        <form action="{{ url('editFeesDetails') }}" method="post">
            @csrf
              <input type="hidden" id="admission_id" name="admission_id">
            <div class="modal-body">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                  <thead>
                  <tr role="row">
                      <th>{{ __('common.SR.NO') }}</th>
                            <th>{{ __('library.Fees Type') }}</th>
                            <th>{{ __('library.Paid Amount') }}</th>
                             <th>{{ __('common.Action') }}</th>
                  </thead>
                  <tbody  id="tbody">
                      
                  
                    </tbody>
                </table>

            </div>
        
            <div class="modal-footer">
              <button type="submit" class="btn btn-danger" id="closeModal"class="close" data-dismiss="modal">{{ __('common.Submit') }}</button>
            </div>
        </form>
      </div>
    </div>
</div>


 
<script>
    
             $('.feesDetail').click(function() {
                 count=2;
	var first_name = $(this).data('first_name');
	var class_type_id = $(this).data('class_type_id');
	var section_id = $(this).data('section_id');
	var admission_id = $(this).data('admission_id');

// 	$('#first_name').html(first_name);
// 	$('#class_type_id1').html(class_type_id);
// 	$('#section_id1').html(section_id);
// 	$('#admission_id').val(admission_id);
   
            $.ajax({
                 type: "post",
                 headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
        	    url: 'getFeesDetail',
        	     data:{admission_id:admission_id},
        	    success: function(result){
        	     
        	  jQuery('#tbody').html(result)
        	   $('#myModal').modal('toggle');
        	         	$('#first_name').html(first_name);
                        $('#class_type_id1').html(class_type_id);
                    	$('#section_id1').html(section_id);
 	                    $('#admission_id').val(admission_id);
 	        	 
 	        	   $(".editable").each(function () {
        //Reference the Label.
        var label = $(this);
 
        //Add a TextBox next to the Label.
        label.after("<input type = 'text' style = 'display:none;width:100px;' />");
 
        //Reference the TextBox.
        var textbox = $(this).next();
 
        //Set the name attribute of the TextBox.
        
        textbox[0].name = this.id.replace("n"+count, "amount[]");
    count++;
        //Assign the value of Label to TextBox.
        textbox.val(label.html());
 
        //When Label is clicked, hide Label and show TextBox.
        label.click(function () {
            $(this).hide();
            $(this).next().show();
        });
 
        //When focus is lost from TextBox, hide TextBox and show Label.
        textbox.focusout(function () {
            $(this).hide();
            $(this).prev().html($(this).val());
            $(this).prev().show();
        });
    });
 	       	  }
        	});
        
             });
</script>
@endsection 