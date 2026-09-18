
@php
  $classType = Helper::classType();
  $homeworkReview = Helper::homeworkReview();
  
@endphp
@extends('layout.app') 
@section('content')

<input type="hidden" id="session_id" value="{{ Session::get('role_id') ?? '' }}">
 <div class="content-wrapper">
    
     <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">   
    <div class="card card-outline card-orange">
         <div class="card-header bg-primary">
             
        @if(Session::get('role_id') == 3)    
        <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp;My Hourly Homework </h3>
        @else
        <h3 class="card-title"><i class="fa fa-flask"></i> &nbsp;View Hourly Homework </h3>
        @endif
            
            <div class="card-tools">
        @if(Session::get('role_id') !== 3)
          <a href="{{url('hourly/hw/add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i> Hourly Homework </a>
        @else
            <a href="{{url('homework/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a>
        @endif
          </div>
            
            </div>        
            
        
        @if(Session::get('role_id') == 1)
         <form id="quickForm" action="{{ url('hourly/hw/view') }}" method="post" >
                        @csrf 
                    <div class="row m-2">
                    
                    	           
                        <div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('messages.Class') }}</label>
                    			<select class="form-control select2" id="class_type_id" name="class_type_id" >
                    			<option value="">{{ __('messages.Select') }}</option>
                                 @if(!empty($classType)) 
                                      @foreach($classType as $type)
                                         <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                    	    </div>
                    	</div>

                    	<div class="col-md-2">
                    		<div class="form-group">
                    			<label>{{ __('homework.Admission No') }}</label>
                    				<input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('homework.Admission No') }}" value="{{ $search['admissionNo'] ?? '' }}">
  
        		        </div>
        		    </div>    

            		<div class="col-md-4">
            			<div class="form-group">
            				<label>{{ __('messages.Search By Keywords') }}</label>
            				<input type="text" class="form-control" id="name" name="name" placeholder="{{ __('messages.Ex. Name, Mobile, Email, Aadhaar, Father/ Mother Name etc.') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label class="text-white">Search</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('messages.Search') }}</button>
                    	</div>
                   			
                    </div>
                </form>
        
             @endif
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	

        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead>
          <tr role="row">
              <th>{{ __('messages.Sr.No.') }}</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('messages.Class') }}</th>
                    <th>{{ __('messages.Subject') }}</th>
                    <th>{{ __('homework.Homework Date') }}</th>
                    <!--<th>{{ __('homework.Submission Date') }}</th>-->
                    <th>{{ __('messages.Action') }}</th>
          </thead>
              @if(!empty($data))
                @php
                   $i=1
                @endphp
               
                @foreach ($data  as $item)
                
                <tr class="{{ ( 1 == $item['view_status'] ) ? 'bg-warning' : '' }}"> 
                    <td>{{ $i++ }}</td>
                    <td>{{ $item['first_name'] ??'' }} {{ $item['last_name'] ??'' }} </td>
                    <td>{{ $item['ClassType']['name'] ??'' }} </td>
                    <td>{{ $item['Subject']['name'] ?? '' }}</td>
                    <td>{{date('d-m-Y', strtotime($item['homework_date'])) ?? '' }}</td>
                    <!--<td>{{ $item['submission_date'] ?? '' }}</td>-->
                    <td>
                        <button data-id='{{$item->id}}' data-homework_date='{{$item->homework_date}}' data-submission_date='{{$item->submission_date}}' 
                        data-title='{{$item->title}}' data-description='{{$item->description}}' data-content_file='{{ env('IMAGE_SHOW_PATH').'homework/'.$item['content_file'] }}'
                        data-class='{{ $item['ClassType']['name'] ??'' }}' data-subject='{{ $item['Subject']['name'] ?? '' }}'
                        data-create_teacher='{{ $item['Teacher']['first_name'] ?? '' }} {{ $item['Teacher']['last_name'] ?? '' }}'
                        class="btn btn-secondary viewHomework btn-xs tooltip1" title1="View Homework" ><i class="fa fa-eye"></i></button>
                        
                        <a href="{{ url('hourly/homework/details') }}/{{$item->id}}" class="btn btn-primary btn-xs ml-3 tooltip1" title1=" Assignments" ><i class="fa fa-reorder"></i></a>    
                        @if(Session::get('role_id') == 3)
                            <a href="javascript:;" class="btn btn-success btn-xs ml-3 homeworkId tooltip1" id="homeworkId" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#uploadModal" title1="Upload Assignment" ><i class="fa fa-upload"></i></a>
                        @endif
                        @if(Session::get('role_id') == 3)
                            @if(!empty($item['content_file']))
                            <!--<img src="{{ env('IMAGE_SHOW_PATH').'homework/'.$item['content_file'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}'" width="60px" height="60px">-->
                            <a href="{{ url('download_hourly_homework') }}/{{$item['id'] ?? '' }}" class="btn btn-danger  btn-xs ml-3 tooltip1" title1="Download Homework" ><i class="fa fa-download"></i></a>
                            @endif
                        @endif                        
                        @if(Session::get('role_id') !== 3)
                            
                            <a href="{{url('homework/edit',$item->id)}}" class="btn btn-success  btn-xs ml-3 tooltip1" title1="Edit Homework" ><i class="fa fa-edit"></i></a>
                            <a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 tooltip1" title1="Delete Homework"><i class="fa fa-trash-o"></i></a>
                        @endif
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

            <!-- The Modal -->
            <div class="modal" id="uploadModal">
              <div class="modal-dialog modal-lg">
                <div class="modal-content">
            
                  <div class="modal-header">
                    <h4 class="modal-title">{{ __('homework.Homework Assignments') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>
                <form action="{{ url('upload/hourly/homework') }}" method="post" enctype="multipart/form-data">
                    @csrf
                  <div class="modal-body">
                      <input type="hidden" id="homework_id" name="homework_id" value="">
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">{{ __('messages.Message') }}*</label>
									<textarea class="form-control" id="message" name="message" placeholder="Type Message"></textarea>
							    </div>
						</div>
                    	<div class="col-md-12">
								<div class="form-group">
									<label style="color: red;">{{ __('messages.Attach Document') }}*</label>
									<input class="form-control" type="file" id="student_upload_file" name="student_upload_file" > 
							    </div>
						</div>                       
                  </div>
                  <div class="modal-footer">
                                <button type="submit" class="btn btn-primary waves-effect waves-light uploadHomework">{{ __('messages.submit') }}</button>
                     </div>
                    </form>
                </div>
              </div>
            </div>

            <!-- The Modal -->
            <div class="modal" id="Modal_id">
              <div class="modal-dialog">
                <div class="modal-content" style="background: #555b5beb;">
            
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                  </div>
            
                  <!-- Modal body -->
                  <form action="{{ url('homework/delete') }}" method="post">
                          	 @csrf
                  <div class="modal-body">
                          <input type=hidden id="delete_id" name=delete_id>
                          <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }}  ?</h5>
                  </div>
                  <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                                <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
                     </div>
                   </form>
            
                </div>
              </div>
            </div>


          <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" >
              <div class="modal-content">
                <div class="modal-header">
                        <h5>{{ __('homework.Homework Details') }}</h5>
                  <button type="button" class="close" data-bs-dismiss="modal" aria-hidden="true">x</button>
                </div>
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-7">
                                <p><b>{{ __('messages.Title') }} :</b> <span id="title"></span></p>
                                <p><b>Description :</b> <span id="description"></span></p> 
                                <img id="hw_file" src="" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" width="100%" >
                        </div>
                        <div class="col-md-5 pl-3" style="background-color: whitesmoke;margin-top: -2%; margin-bottom: -2%;">
                           <h4>{{ __('homework.Summary') }}</h4>
                           <hr>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>{{ __('homework.Homework Date') }}:</b> <span id="homework_date"></span></p>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>{{ __('homework.Submission Date') }}:</b> <span id="submission_date"></span></p>
                            <p><i class="fa fa-calendar-plus-o"></i> <b>{{ __('homework.Evaluation Date') }}:</b> 12/05/2021</p>
                            <p><b>{{ __('homework.Created By') }}:</b> <span id="create_teacher"></span></p>
                            <p><b>{{ __('homework.Evaluated By') }}:</b> Shyam Sir</p>
                            <p><b>{{ __('messages.Class') }}:</b> <span id="classes"></span></p>
                            <p><b>{{ __('messages.Subject') }}:</b> <span id="subject"></span></p>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                </div>
              </div>
            </div>
          </div>
          
          
</div>
</div>
</div>
</section>
  
<script>
  $('.homeworkId').click(function() {
  var homework_id = $(this).data('id'); 
  
  $('#homework_id').val(homework_id); 
  } );


$(document).on('click', ".uploadHomework", function () {
    if( !$('#message').val() ) { 
        $("#message").attr('required','true');
        toastr.error('The Message field is required!'); 
    }     
    if( !$('#content_file').val() ) { 
        $("#content_file").attr('required','true');
        toastr.error('The Document field is required!'); 
    } 
});

$(document).on('click', ".viewHomework", function() {

    var session_id = $('#session_id').val();
        $('#myModal').modal('toggle');      
        
    var homework_date = $(this).data('homework_date');
    var submission_date = $(this).data('submission_date');
    var title = $(this).data('title');
    var description = $(this).data('description');
    var classes = $(this).data('class');
    var subject = $(this).data('subject');
    var content_file = $(this).data('content_file');
    var create_teacher = $(this).data('create_teacher');

    $('#homework_date').html(homework_date);
    $('#submission_date').html(submission_date);
    $('#title').html(title);
    $('#description').html(description);
    $('#classes').html(classes);
    $('#subject').html(subject);
    $('#hw_file').attr('src',content_file);
        if(create_teacher !== "") { 
            $('#create_teacher').html(create_teacher); 
        }else{
            $('#create_teacher').html('Admin');
        }     

}); 


  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>  

@endsection 