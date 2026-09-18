@php
   $classType = Helper::examPanelClassType();
  $getsubject = Helper::getSubject();
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
            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('homework.Edit Homework') }} </h3>
            <div class="card-tools">
            <a href="{{url('hourly/hw/view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>
           <!-- <a href="{{url('homework/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a>-->
            </div>
            
            </div>        
            
             <div class="card-body">
                <form id="form-submit-edit" action="{{ url('homework/edit') }}/{{$data['id'] ?? ''}}" method="post" enctype="multipart/form-data">
                      @csrf
                <div class="row"> 
                
                <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('messages.Class') }}:*</label>
            				<select class="form-control select2 @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id">
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['class_type_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                            
            		    </div>
            		</div>
                    <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('messages.Subject') }}:*</label>
            				<select class="form-control select2 @error('subject') is-invalid @enderror" id="subject" name="subject">
                             @if(!empty($getsubject)) 
                                  @foreach($getsubject as $type)
                                     <option value="{{ $type->id ?? ''  }}" {{ ( $type->id == $data['subject'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                             
            		    </div>
            		</div>            	
            		
            		
                <div class="col-md-3">
					<div class="form-group">
								<label style="color: red;">{{ __('homework.Homework Title') }}*</label>
								<input class="form-control  @error('title') is-invalid @enderror" type="text" id="title" name="title" placeholder="Homework Title" value="{{ $data->title ??  '' }}"> 
                               								    
						    </div>
						</div>
                    	 
            		<div class="col-md-3">
        			    <div class="form-group">
        				<label style="color:red;">{{ __('Homework Issue Date') }}:*</label>
        				
        					<input type="date" class="form-control @error('homework_issue_date') is-invalid @enderror" id="homework_issue_date" name="homework_issue_date"value="{{ $data['homework_issue_date'] ?? '' }}">
        				   
                        
        		        </div>
        		    </div>
        		    
            		<div class="col-md-3">
        			    <div class="form-group">
        				<label style="color:red;">{{ __('homework.Submission Date') }}:*</label>
        					<input type="date" class="form-control @error('submission_date') is-invalid @enderror" id="submission_date" name="submission_date"value="{{ $data['submission_date'] ?? '' }}">
                        
        		        </div>
        		    </div> 
        		    
                             		    
        		    
        		    		   <div class="col-md-12">
        			   <div class="form-group">
        				<label style="color:red;">{{ __('messages.Description') }}:*</label>
        					<textarea type="text" class="form-control @error('description') is-invalid @enderror" id="compose-textarea" name="description" placeholder="Please submit before last date." >{{ $data['description'] ?? '' }}</textarea>
                         
        		        </div>
        		    </div>
        	<!--	   <div class="col-md-12">
        			   <div class="form-group">
        				<label style="color:red;">Description:*</label>
        					<textarea type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description" placeholder="Please submit before last date.">{{ $data['description'] ?? '' }}</textarea>
                         @error('description')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				 @enderror
        		        </div>
        		    </div>-->
              </div>
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-submit">{{ __('messages.Update') }}</button>
                    </div>
                </div>
                </form>
                </div>                 
            </div> 
            </div>
</div>
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
    
    .card-block{
                height:240px;
            }
    </style>
    
</section>
        </div>
        
        
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