@php
  $classType = Helper::classType();
  $getsubject = Helper::getSubject();
  $getallStudent = Helper::getallStudent();

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
            <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp; {{ __('messages.Add Hourly Homework') }}</h3>
            <input type="hidden" id="role_id" value="{{ Session::get('role_id') ?? '' }}"> 
            <div class="card-tools">
                         <a href="{{url('hourly/hw/view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{ __('messages.Hourly Homework') }}</a>
<!--        <a href="{{url('master_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a>
-->                
   
           
            </div>
            
            </div>   
             <div class="card-body">
                 <form id="form-submit" action="{{ url('hourly/hw/add') }}" method="post" enctype="multipart/form-data">
                      @csrf
                <div class="row"> 
                     <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('messages.Class') }}*</label>
            				<select class="form-control select2 @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id">
                            <option value="" >{{ __('messages.Select') }}</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ($type->id == Session::get('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                            
            		    </div>
            		</div>

                    <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('messages.Subject') }}*</label>
            				<select class="form-control select2 @error('subject') is-invalid @enderror" id="subject" name="subject">
            				    <option value="">Select</option>
                             @if(!empty($getsubject)) 
                                  @foreach($getsubject as $type)
                                     <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                            
            		    </div>
            		</div>            		 
            		<div class="col-md-2">
        			    <div class="form-group">
        				<label style="color:red;"></label>{{ __('messages.Homework Date') }}
        				
        					<input type="date" class="form-control @error('homework_date') is-invalid @enderror" id="homework_date" name="homework_date"value="{{date('Y-m-d')}}">
        				 
        		        </div>
        		    </div>
                <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('Select Student') }}*</label>
            			<select name="admission_id" id="admission_id" class="form-control select2">
            			    <option value="">Select</option>
                                         @if(!empty($getallStudent)) 
                                          @foreach($getallStudent as $type)
                                             <option value="{{ $type->id ?? ''  }}">{{ $type->first_name ?? ''  }} {{ $type->last_name ?? ''  }}</option>
                                          @endforeach
                                         @endif

                                            </select>  
                            
            		    </div>
            		</div>
            		
                <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('Time') }}*</label>
                            <input type="time" name="times" id="times" class="form-control @error('times') is-invalid @enderror">
                           
            		    </div>
            	</div>
                
                                    
                <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('Content File') }}*</label>
                             <input class="form-control @error('content_file') is-invalid @enderror" type="file" name="content_file" id="content_file" value="{{ old('content_file') ?? '' }}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
                           
            		    </div>
            		</div>
                       <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('Title') }}*</label>
            		            <textarea type="title" name="title" class="form-control @error('title') is-invalid @enderror" placeholder="If have any note write here..."></textarea>
                           
            		    </div>
            		</div>
             
                             
              </div>
              <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-submit">{{ __('messages.Submit') }}</button>
                    </div>
                </div>
                </form>
                </div>                 
            </div> 
            </div> 
            </div> 
            </div> 
            </section>
        </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#content_file').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
    </script>
    
    <style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    </style>
    
<script>
$("#class_type_id").change(function(){
    
    var class_type_id = $(this).val();
        $.ajax({
             headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        type:'post',
        url: '/find/student',
        data: {class_type_id:class_type_id},
	    success: function(data){
	     if(data != ''){
	         	$("#admission_id").html(data);
	     }else{
	         	$("#admission_id").html(data);
	            toastr.error('User Not Found !');
	     }
	    }
        }); 
 
});  

$(document).ready(function() {
  
    count=0;
      $( ".removeprodtxtbx" ).eq( 0 ).css( "display", "none" );
    $(document).on("click", "#clonebtn", function() {
       count++;
        //we select the box clone it and insert it after the box
        $('#box2').addClass('rowTr')
        $('#box2').clone().appendTo('#table_body')
        $('.rowTr').last().addClass('rowTr1')

         $( ".removeprodtxtbx" ).eq( count ).css( "display", "block" );
         $( ".addmoreprodtxtbx" ).eq( count ).css( "display", "none" );
         $( ".pay_amt" ).eq( count ).val("");
          
    });
    
    $(document).on("click", "#removerow", function() {
        $(this).parents("#box2").remove();
        $('#removerow').focus();
        count--;
    });
});
</script>        
<style>
._table {
    width: 100%;
    border-collapse: collapse;
}

._table :is(th, td) {
    padding: 0px 10px;
}
.success {
    background-color: #24b96f !important;
}
.danger {
    background-color: #ff5722 !important;
}
.action_container>* {
    border: none;
    outline: none;
    color: #fff;
    text-decoration: none;
    display: inline-block;
    padding: 8px 14px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
}
textarea {
    height: calc(2.25rem) !important;
}

 .addmoreprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/list_add.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 16px;
  margin-top:4px;
  width: 16px;
}

.removeprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/delete2.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 15px;
 
   margin:4px 0 0 0 !important;
  width: 16px;
 
}
</style>    
<script>
/*$( document ).ready(function() {
    var role_id = $('#role_id').val();
   
   if( role_id == 2 ) { 
        $("#class_type_id").attr('disabled', 'disabled');
        $("#section").attr('disabled', 'disabled');
   }else{
   }     
});*/
</script>
@endsection                