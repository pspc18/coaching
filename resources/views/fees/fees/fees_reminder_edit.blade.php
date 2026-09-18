@php
  $feesType = Helper::feesType();
  $classType = Helper::classType();
  $actionPermission = Helper::actionPermission();
@endphp
 
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
   <!-- <div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Fees Reminder</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>-->
    
 <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">   
  <div class="col-md-12">
            <div class="card card-outline card-orange mr-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Edit Fees Reminder') }} </h3>
            <div class="card-tools">
            <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
            <a href="{{url('fees_reminder')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> Back</a>
            </div>
            
            </div>   
            
        
                  <!--  <div class="row m-2">
                    
                    	           
                        <div class="col-md-3">
            		<div class="form-group">
            			<label>{{ __('messages.Class') }}</label>
            			<select class="form-control select2" id="class_search_id " name="class_search_id" >
            			<option value="">{{ __('messages.Select') }}</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
            

      

            		<div class="col-md-4">
    			<div class="form-group">
    				<label>{{ __('messages.Search By Keywords') }}</label>
    				<input type="text" class="form-control" id="searchName" name="name" placeholder="{{ __('messages.Ex. Name, Mobile, Email, Aadhaar, Father/Mother Name etc.') }}" value="{{ $search['name'] ?? '' }}">
    		    </div>
    		</div>                      	
                        <div class="col-md-1 ">
                             <label class="text-white">Search</label>
                    	    <button type="button" class="btn btn-primary" onclick="SearchValue()" >{{ __('messages.Search') }}</button>
                    	</div>
                   			
                    </div>
                      -->
           <form id="quickForm" action="{{ url('fees_reminder_edit') }}/{{$data->id}}" method="post">
  
                @csrf
                          <div class="row m-2">
                    <div class="col-md-12">
                	</div>
                    <div class="col-md-12">
                       <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead>
                          <tr role="row">
                              <th>{{ __('Sr.No.') }}</th>
                              <th>{{ __('Addmission.No') }}</th>
                              <th>{{ __('Name') }}</th>
                              <th>{{ __('Mobile') }}</th>
                              <th>{{ __('Email') }}</th>
                              <th>{{ __('F. Name') }}</th>
                              <th>{{ __('M. Name') }}</th>
                             
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($admission))
                                @php
                                   $i=1
                                @endphp
                                @foreach ($admission  as $item)
                                @php  
                             
                                 $admission_id = array();
                                if($data['admission_id'] > 0){ 
                                $val = $data['admission_id'];
                                $admission_id = explode(',', $val);
                                }
                                
                            @endphp
                                <tr>
                                        <td><input class="ml-3" type="checkbox" id="checkbox" name="checkbox[]" value="{{ $item['id'] ?? ''  }}"  {{ in_array($item->id, $admission_id)  ? 'checked' : '' }} > </td>
                                        <td>{{ $item['admissionNo'] ?? '' }}</td>
                                        <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                                        <td>{{ $item['mobile'] ?? '' }}</td>
                                        <td>{{ $item['email'] ?? '' }}</td>
                                        <td>{{ $item['father_name'] ?? '' }}</td>
                                        <td>{{ $item['mother_name'] ?? '' }}</td>
                                       
                                    </tr>
                           @endforeach
                        @endif
                          </tbody>
                          </table>
                	</div>
                </div>
            
                
                 <hr>
                 
                <div class="row m-2">
                        <div class="col-md-4">
                			<label style="color:red;">{{ __('fees.Reminder Name') }}*</label>
                			<input class="form-control @error('name') is-invalid @enderror" type="text" name="name" id="name" value="{{$data->name ?? ''}}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                    			
                    	</div>
                        
                        <div class="col-md-4">
                			<label style="color:red;">{{ __('fees.Reminder Date') }}*</label>
            				<input class="form-control @error('reminder_date') is-invalid @enderror" type="date" id="reminder_date" name="reminder_date" value="{{$data->reminder_date ?? ''}}">
                             @error('reminder_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>
                        
                        <div class="col-md-4">
                			<label style="color:red;">{{ __('fees.Due Date') }}*</label>
            				<input class="form-control @error('due_date') is-invalid @enderror" type="date" id="due_date" name="due_date" placeholder="Due Date" value="{{$data->due_date ?? ''}}">
                             @error('due_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                     	
                                   	
                     	
                        <div class="col-md-6">
                    			<label style="color:red;">{{ __('fees.Reminder Message') }}*</label>
                    			<textarea class="form-control @error('message') is-invalid @enderror mb-3" type="text" name="message" id="message" placeholder="{{ __('fees.Reminder Message') }}">{{$data->due_date ?? ''}}</textarea>
                             @error('message')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror 
                    	</div>                      	
                </div>
 
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }} </button>
                    </div>
                </div>
                </form>
            </div>          
        </div>
        
     
    </div>  

</div>
</section>
</div>
<script>
    $(document).on('click', ".reminder_status", function () {
var id = $(this).data("id");
var basurl = "{{ url('/') }}";
    var status = $(this).data("status");
    if(confirm('Are you sure ?')){
      
        $.ajax({
            url: basurl+'fees_reminder_status',
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: { status: status, id: id },
            success: function (response) {

                /*if (response == 0) {
                    alert("Internal Server Error");
                }else if (response == 1) {
                    alert("Internal Server Error");
                }
                else {
                    alert("Internal Servasaser Error");
                }*/
            },
        });
    }

});

</script>

<script>
    
        function SearchValue() {
            var basurl = "{{ url('/') }}";
            var name = $('#searchName').val();
            var registration_no = $('#registration_no').val();
            var class_search_id = $('#class_search_id :selected').val();
            if(class_search_id > 0 || registration_no != '' || name != ''){
               $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl+'/fees_reminder_search',
                data: {class_search_id:class_search_id,name:name,registration_no:registration_no},
                 //dataType: 'json',
                success: function (data) {

                    $('.student_list_show').html(data);
                   
                }
              }); 
            }else{
                toastr.error('Please put a value in one column !');
            }
        };
        
        function showData(student_id) {
            var basurl = "{{ url('/') }}";
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl+'/student_add_click',
                data: {student_id : student_id},
                 dataType: 'json',
                success: function (data) {

                 if(data){
                $('#first_name').val(data.first_name);
                $('#last_name').val(data.last_name);
                $('#aadhaar').val(data.aadhaar);
                $('#student_id').val(data.name);
                $('#gender_id').val(data.gender_id);
                $('#class_type_id').val(data.class_type_id);
                $('#dob').val(data.dob);
                $('#mobile').val(data.mobile);
                $('#email').val(data.email);
                $('#father_name').val(data.father_name);
                $('#mother_name').val(data.mother_name);
                $('#father_mobile').val(data.father_mobile);
                $('#admission_type_id').val(data.admission_type_id);
               
                $('#sms_contact_no').val(data.sms_contact_no);
                $('#village_city').val(data.village_city);
                $('#address').val(data.address);
                $('#pincode').val(data.pincode);
                $('#remark_1').val(data.remark_1);
                $('#country_id1').val(data.country_id);
                $('.stateId').val(data.state_id);
                $('.cityId').val(data.city_id);
               
                $('#student_img').val(data.student_img);
                $('#father_img').val(data.father_img);
                $('#mother_img').val(data.mother_img);
                $('#student_roll_no').val(data.roll_no);
                
                $('#school_name').val(data.school_name);
                $('#date_of_admission').val(data.dob);
                 }else{
                     alert('No more records found');
                 }
                    
                }
              });
        };
        
</script>

<script>
    $('#fees_type_id').on('change', function(e){
    
	var fees_type_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: basurl+'/duedate/'+fees_type_id,
	  success: function(data){
      $("#due_date").val(data);
	  }
	});	
});
</script>

<script>
    $('.Status').click(function() {
        var basurl = "{{ url('/') }}";
	var status = $(this).data('id');
	$('#status').val(status);
});
$(document).on('click', ".change_status", function() {
	var id = $(this).data("id");
	var status = $(this).data("status");
	if(confirm('Are you sure you want to Change Status ?')) {
		$.ajax({
			url: basurl+'fees_reminder_status',
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			data: {
				status: status,
				id: id
			},
			success: function(response) {
				if(response == 0) {
					location.reload(true);
				} else if(response == 1) {
					location.reload(true);
				} else {
					alert("Internal Servasaser Error");
				}
			},
		});
	}
});
</script>

@endsection      