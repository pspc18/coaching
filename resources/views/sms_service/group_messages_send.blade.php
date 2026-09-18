@php
$getRole = Helper::roleType();
$classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')

                                                                    
<div class="content-wrapper">
   <form id="sendSms" action="{{ url('group_messages_send') }}" method="post" enctype='multipart/form-data'>   
            @csrf
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">
    <div class="card card-outline card-orange">
        	<div class="card-header bg-primary">
                        @if(Session::get('') == 3)
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('smsService.Send SMS E-Mail Whatsapp') }}</h3>
                        @else						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{ __('smsService.Send SMS E-Mail Whatsapp') }}</h3>
						@endif
							<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('send_message_terminal')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            @endif

                                
                            </div>
						</div>
						
                 
        <section class="content">
            <div class="container-fluid">
                <div class="row m-2">
                    <div class=" col-md-12 title"><h5 class="text-danger">{{ __('smsService.Select Candidates') }} :-</h5></div>
            
                       	
                    <div class="col-md-3 class_type_id" >
                		<div class="form-group">
                			<label>Class</label>
                			<select class="form-control @error('class_type_id') is-invalid @enderror select2" id="class_type_id" name="class_type_id" >
                			<option value="">Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             @error('class_type_id')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
                	    </div>
                	</div>                        	
                    <div class="col-md-3 group_name" >
                		<div class="form-group">
                			<label>Group Name</label>
                			<select class="form-control @error('group_name') is-invalid @enderror select2" id="group_name" name="group_name" >
                			<option value="">Select</option>
                             
                                     <option value="">Select</option>
                                 
                            </select>
                             @error('group_name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
                	    </div>
                	</div>                        	
                
                 <!--   <div class="col-md-1">
                         <label class="text-white">{{ __('messages.Search') }}</label>
                	    <button class="btn btn-primary" >{{ __('smsService.Search') }}</button>
                	</div>-->
                </div>
      
        </section>
        <section>
      
    
                <div class="row m-2">
                    <div class="col-md-12" id="student_list_show" style="height: 110px; overflow-y: scroll;">

                    </div>
                </div>
         </section>
            <div id="chcekshow" class="d-none">
            <hr>
            <div class="row m-2">
                <div class=" col-md-12 title"><h5 class="text-danger">{{ __('smsService.Message Details') }}:-</h5></div>
        
        		<div class="col-md-12">
        	    	<div class="form-group">
        				<label style="color:black;">{{ __('smsService.Message') }}</label>
        				<textarea id="message_box" name="message" class="form-control ">{{ old('message') ?? '' }}</textarea>
        				    
                        <div id="count">{{ __('smsService.Total Characters') }}: <span id="current_count">0</span></div>        				    
        		    </div>
        	    </div>
        		<div class="col-md-12">
        	    	<div class="form-group">
        				<label style="colo:black">{{ __('Attachment') }}</label></br>
        				<input id='attachment_box' type='file' name='file'  />   				    
        		    </div>
        	    </div>
        
        	</div>
        	

            <div class="row m-2">
                <div class="col-md-12 text-center pb-2">
                    <button type="submit"  class="btn btn-primary">{{ __('messages.Send Message') }}</button>
                </div>
                <!--<div class="col-md-12 text-right pb-2">-->
                <!--    <button type="submit" id="submit2" style="opacity:0"></button>-->
                <!--</div>-->
            </div>
            </div>
        
    </div>
</div>
</div>
</div>
</div>
</section>
</form>
</div>

<script src="https://demo.smart-school.in/backend/plugins/ckeditor/ckeditor.js"></script>

<script>
    var baseurl = "https://www.school.rukmanisoftware.com/";
    CKEDITOR.replace(ckeditor, {
      toolbar: 'Ques',    
      allowedContent : true,              
      extraPlugins: 'ckeditor_wiris',
      enterMode : CKEDITOR.ENTER_BR,
      shiftEnterMode: CKEDITOR.ENTER_P,
      customConfig: baseurl+'public/assets/school/js/ckeditor_config.js'
    });
</script>



<script type="text/javascript">
$('textarea').keyup(function() {    
    var characterCount = $(this).val().length,
        current_count = $('#current_count'),
        count = $('#count');    
        current_count.text(characterCount);        
});

    // function SearchValue() {
       
    //     var class_type_id = $('#class_type_id :selected').val();
    //     var role_id = $('#role :selected').val();
    //     if(class_type_id > 0 || role_id > 0){
    //     $.ajax({
    //              headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
    //         type:'post',
    //         url: '/sms_search_data',
    //         data: {class_type_id:class_type_id,role_id:role_id},
    //          //dataType: 'json',
    //         success: function (data) {
    //             $('#student_list_show').html(data);
    //             $('#chcekshow').removeClass('d-none');
    //         }
    //       });
    //     }else{
    //         toastr.error('Please put a value in column !');
    //     }               
    // };

</script>


<!-- Include jQuery library -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>



 <script>
        $('#class_type_id').on('change', function(e){
            
                var baseurl = "{{ url('/') }}";
            	var class_type_id = $(this).val();
                $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	  url: baseurl+'/getGroupData/'+class_type_id,
            	  success: function(data){
            			$("#group_name").html(data);
            		//	 $('#chcekshow').removeClass('d-none');
            	  }
            	});
            	
            });
        $('#group_name').on('change', function(e){
            
               
            			 $('#chcekshow').removeClass('d-none');
            
            	
            	
            });
            
            
            </script>
            
            <script>
    $(document).ready(function() {
     $("#sendSms").submit(function (e) {
      
     var attachment = $('#attachment_box').val();
     var message = $('#message_box').val();
     
     var count = 1;
     if(attachment == '' && message == '')
     {
         count = 0;
     }
     
     
     if(count == 0)
     {
         e.preventDefault();
         toastr.error('You have to select message or attachment')
     }

});
});
</script>


@endsection

