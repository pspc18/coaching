@php
   $classType = Helper::classType();
    $getAttendanceStatus= Helper::getAttendanceStatus();
  
@endphp
@extends('layout.app') 
@section('content')

<style>
    .paddingTable thead tr{
        background:#002c54;
        color:white;
    }
    
    .paddingTable thead tr th{
        padding:5px;
    }
</style>

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
 <div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
         <div class="card card-outline card-orange">
         <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-calendar-check-o"></i> &nbsp;{{ __('Select Ground') }}</h3>
        <div class="card-tools">
        <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('messages.Back') }}</span></a>
        </div>
        
        </div>         
        <form id="quickForm" action="{{url('student/promote_add')}}" method="post" >
            @csrf 
            <div class="row m-2">
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="State" class="required">Admission No.</label>
                     <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Admission No." value="{{ $search['admissionNo'] ?? '' }}">
                  </div>
                </div>
                <div class="col-md-2">
            		<div class="form-group">
            			<label>{{ __('messages.Class') }}</label>
            			<select class="form-control select2" id="class_type_id1" name="class_type_id" required>
            			<option value="">{{ __('messages.Select') }}</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{$search['class_type_id'] == $type->id ? 'selected' : ''}} >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
                <div class="col-md-4">
            		<div class="form-group">
            			<label>{{ __('messages.Search By Keywords') }}</label>
            			<input type="text" class="form-control"  name="name" placeholder="{{ __('messages.Ex. Student Name, Father/ Mother Name, Mobile etc.') }}" value="{{ $search['name'] ?? '' }}"> 
            	    </div>
            	</div> 
                <div class="col-md-1 ">
                   <div class="Display_none_mobile">
                        <label for="" class="text-white">Search</label>
                   </div>
            	    <button type="submit" class="btn btn-primary"  >{{ __('messages.Search') }}</button>
            	</div>
            </div>
        </form>        

    </div>
     @if(!empty($data)) 
    <div class="card card-default">
         <div class="card-header ">
        <h3 class="card-title">{{ __('The Next Session Was Transferred To The Students') }}</h3>
      
        
        </div>         
            <div class="col-md-12">
            	<div class=" alert-subl mb-lg m-2">
            		<strong>Instructions :</strong><br>
            		1. The Roll field shows the previous roll and you can manually add new roll for promoted session.<br>
            		2. All fields from the previous session will be transferred to the new session.<br>
            		3. Students with unchecked checkboxes will not be promoted.<br>
            		4. Failed students will  pormote in same  class in next session.<br>
            		5.<span style="color: red;">*</span>. Before promoting students to a new class, the fee structure (Fees Group, Fees Master) must be created.<br>
            	</div>
            </div>
        <form id="form-submit-promote" action="{{url('studentsPromoteAdd')}}" method="post" >
                @csrf 
                <div class="row m-2">
                	<div class="col-md-2">
                		<div class="form-group">
                			<label class="text-danger">{{ __('messages.Date') }}*</label>
                			<input class="form-control @error('date') is-invalid @enderror" type="date" id="date" name="date" value="{{date('Y-m-d')}}">
                              	@error('date')
            						<span class="invalid-feedback" role="alert">
            							<strong>{{ $message }}</strong>
            						</span>
            					@enderror            	    
                	    </div>
                	</div> 
                	<div class="col-md-2">
                	    <div class="form-group">
                	        <label class="text-danger">Promote To*</label>
                	        <select class="form-control select2" id="promote_class_type_id" name="promote_class_type_id"  >
                			<option value="">{{ __('messages.Select') }}</option>
                             
                             @if(!empty($search['class_type_id'])) 
                             @if(!empty($classType)) 
                             @php
                             $orderNumber =0;
                             $class_order = DB::table('class_types')->where('id',$search['class_type_id'])->whereNull('deleted_at')->first();
                             if(!empty($class_order))
                             {
                             $orderNumber = $class_order->orderBy ?? 0;
                             }
                             
                             @endphp
                                  @foreach($classType as $promoteClass)
                                     <option value="{{ $promoteClass->id ?? ''  }}" >{{ $promoteClass->name ?? ''  }}</option>
                                  @endforeach
                                  
                              @endif
                              @endif
                            </select>
                	    </div>
                	</div>
                	<div class="col-md-2">
                	    <div class="form-group">
                	        <label class="text-danger">In Session *</label>
                	       	<select class="form-control" id="new_session_id" name="session_id" required >
                                  @if(!empty($session)) 
                                      @foreach($session as $item)
                                         <option value="{{ $item->id ?? ''  }}" 
                                         @if(Session::get('session_id')+1 > $item->id)
                                       {{"disabled"}}
                                        
                                         @endif
                                         >{{ $item->from_year ?? ''  }}{{"-"}}{{ $item->to_year ?? ''  }}</option>
                                      @endforeach
                                  @endif  
                                </select>      
                	    </div>
                	</div>
                   
                	</div> 
                
           
                	<div class="col-md-12">
             
            	<div class="table-responsive">
            	    <table id="" class="table table-bordered table-striped border  dataTable dtr-inline paddingTable">
                  <thead>
                  <tr role="row">
                            <th><input type="checkbox" id="select_all" checked for="select_all"> <label for="select_all" >{{ __('Select All') }}</label></th>
                            <th>{{ __('student.Admission No.') }}</th>
                            <th>Name</th>
                            <th>{{ __('messages.Class') }}</th>
                            <th>{{ __('Roll No.') }}</th>
                            <th>{{ __('Promote Status') }}</th>
                        </tr>
        
                    </thead>
                    <input type="hidden" id="fees_master" name="fees_master">

                    <tbody >
          
                @if($data->count() > 0)
                        @php
                           $i=1;
                        @endphp
                        @foreach ($data  as $item)
                        <tr>
                                <input type="hidden" id="admission_id" name="admission_id[]" value="{{ $item['id'] ?? '' }}">
                               
                                
                            <td> {{ $i++ }} &nbsp; <input type="checkbox" class="admission_checkbox" id="admission_ids" name="admission_ids[]" value="{{ $item['id'] ?? '' }}" checked>
                            
                            </td>
                            <td>{{ $item['admissionNo'] ?? '' }}</td>
                            <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }}</td>
                            <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
                            <td>
                             <div class="form-group">
                                <input type="text" class="roll_no form-control" name="roll_no[{{ $item['id'] }}]" id="roll_no_{{ $item['id'] }}" placeholder="Enter Roll No." value="{{ $item['roll_no'] ?? '' }}">
                                <span class="text-danger error-message" style="display: none; font-size: 75%;">Roll No. already assigned!</span>
                            </div>
                                
                            </td>
                             <td> 
                                 <input type="radio" id="promote_{{ $item['id'] }}" name="promote_status[{{ $item['id'] }}]" value="1" {{ empty($item['promote_status']) || $item['promote_status'] == 1 ? 'checked' : '' }}>
                                 <label for="promote_{{ $item['id'] }}">{{ __('Promote') }}</label> 
                              
                                 <input type="radio" id="running_{{ $item['id'] }}" name="promote_status[{{ $item['id'] }}]" value="2"{{ isset($item['promote_status']) && $item['promote_status'] == 2 ? 'checked' : '' }}>
                                 <label for="running_{{ $item['id'] }}">Running <span style="font-size:10px;">(fail)</span></label>
                            </td>
                           
                        </tr>
                   @endforeach
               
                  
              @endif    
                 
                    </tbody>
                </table>
            	</div>
                </div>
                 @if(!empty($data))
                <div class="row m-2">
                    <div class="col-md-12 text-center"><button type="submit"  class="btn btn-primary btn-submit" >{{ __('messages.Submit') }}</button></div>
                </div>
                @endif
                </form>  
                </div>
                 @endif    
                            
    </div>
</div>
</div>
</div>
</section>
        
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Select the fees that should be assigned to all students.</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p></p><table class="table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Session</th>
                            <th>Class</th>
                            <th>Fees Group</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody id='fees_group_show'>
                        
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id='save_changes'>Save Changes</button>
                <!-- Additional buttons can be added here -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="error_modal">
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content">
    <div class="modal-body">
        <div class="col-md-12">
            <div class="centered_flex">
                <i class="fa fa-exclamation-triangle text-warning"></i>
                <p>Fees Not Found</p>
            </div>
        </div>
        
        <div class="col-md-12">
            <p id="whatsapp_error_message" class="error_message_whatsapp">
                First create fees group for this class and session.
            </p>
        </div>
        
        <div class="col-md-12 text-right">
            <button class="modal_btn bg-white" data-bs-dismiss="modal">Discard</button>
            <button class="modal_btn bg-warning" data-bs-dismiss="modal">
                <a href="{{ url('fee_dashboard') }}" target="_blank">Click Here</a>
            </button>
        </div>
    </div>
  </div>
</div>
</div>
<style>
    .alert-subl {
    color: #31708f;
    border-color: #ddd;
}

</style>
<script>
$( document ).ready(function() {
$('#promote_class_type_id').change(function(){
    var newClass = $(this).val(); 
    var newSession = $('#new_session_id').val(); 
    $('#group_ids').val(""); 	
    if(newClass != ""){
        var URL = "{{ url('/') }}"; 
        $.ajax({
            headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')}, 
            type: 'post',
            url: URL + '/getFeesGroup', 
            data: {
                class_type_id: newClass,
                session_id: newSession
            },
            success: function (data) {
            var data1 = data.data;
                if(data1.length != 0){
                    var container = $('#fees_group_show');
                        container.html('');
                       data1.forEach(function(item) {
                        var newData = $('<tr class="new-data">' +
                                        '<td><input type="checkbox" class="fees_group_checked" data-id="' + item.id + '"></td>' +
                                        '<td>' + (item.from_year ?? '') + '-' + (item.to_year ?? '') + '</td>' +
                                        '<td>' + (item.class_name ?? '') + '</td>' +
                                        '<td>' + (item.fees_group_name ?? '') + '</td>' +
                                        '<td>' + (item.amount ?? '') + '</td>' +
                                    '</tr>');
                        container.append(newData);
                       $("#myModal").modal('show');
                    });  
                }else{
                    $('#error_modal').modal('show');
                }
                 
               
               
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText); 
            }
     });
    }
});
 
$('#save_changes').click(function(){
    var fees_masters = [];

    $( ".fees_group_checked" ).each(function( index ) {

    if ($(this).is(':checked')) {
fees_masters.push($(this).attr('data-id'))

    }

});

    
$('#fees_master').val(fees_masters);    
$('#myModal').modal('hide');
});
});





    
$( document ).ready(function() {
    var session_id = $('#session_id').val();
    if(session_id != 1){
        var today = new Date().toISOString().split('T')[0];
        document.getElementsByName("date")[0].setAttribute('min', today);        
    }
}); 
</script>  
<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

  })

</script>

<script>
    $(document).ready(function() {
    // Select All Checkbox Functionality
    $("#select_all").on("change", function() {
        $(".admission_checkbox").prop("checked", $(this).prop("checked"));
    });

    // Individual Checkbox Functionality
    $(".admission_checkbox").on("change", function() {
        if ($(".admission_checkbox:checked").length === $(".admission_checkbox").length) {
            $("#select_all").prop("checked", true);
        } else {
            $("#select_all").prop("checked", false);
        }
    });
});
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        let typingTimer;
        const doneTypingInterval = 500;

        function checkForDuplicates(input) {
            let currentValue = input.val().trim();
            let isDuplicate = false;

            if (currentValue === "") {
                input.removeClass("is-invalid");
                input.siblings(".error-message").hide();
                return;
            }

            $(".roll_no").each(function () {
                if (this !== input[0]) {
                    let val = $(this).val().trim();
                    if (val !== "" && val === currentValue) {
                        isDuplicate = true;
                        return false; // break
                    }
                }
            });

            let errorMessage = input.siblings(".error-message");

            if (isDuplicate) {
                input.val(""); // Clear
                input.addClass("is-invalid");
                errorMessage.show();
            } else {
                input.removeClass("is-invalid");
                errorMessage.hide();
            }
        }

        $(document).on("input", ".roll_no", function () {
            let input = $(this);
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function () {
                checkForDuplicates(input);
            }, doneTypingInterval);
        });

        // Also check when input loses focus
        $(document).on("blur", ".roll_no", function () {
            checkForDuplicates($(this));
        });
    });
</script>


@endsection 