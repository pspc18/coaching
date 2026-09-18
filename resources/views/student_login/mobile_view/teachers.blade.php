@extends('layout.app') 
@section('content')
@php
//dd(Session::get('id'));
  $getPermission = Helper::getPermission();
$classType = Helper::classType();

@endphp

<div class="content-wrapper">

    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
				<div class="card-header bg-primary flex_items_toggel">
					<h3 class="card-title"><i class="fa fa-shirtsinbulk"></i> &nbsp;{{ __('staff.View Teachers') }}</h3>
					<div class="card-tools">
					    @if(Session::get('role_id') == 1)
					     <a href="{{url('teachers/add')}}" class="btn btn-primary  btn-sm" title="Add Teacher"><i class="fa fa-plus"></i> <span class="Display_none_mobile">{{ __('common.Add') }} </span> </a> 
			            @endif
			            @if(Session::get('role_id') !== 3)
			            <a href="{{url('staff_file')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile">{{ __('common.Back') }} </span></a> 
			            @endif
			            @if(Session::get('role_id') == 3)
			            <a href="{{url('dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> <span>{{ __('common.Back') }} </span></a> 
			            @endif
			        </div>
				</div>   
				
			@if(Session::get('role_id') == 1)
				 <form id="quickForm" action="{{ url('teachers/index') }}" method="post" >
                        @csrf
                        
                            <div class="row m-2">
                    
                    
            	<div class="col-md-6">
            			<div class="form-group">
            				<label>{{ __('common.Search By Keywords') }}</label>
            				<input type="text" class="form-control " id="name" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1">
                            <div class="Display_none_mobile">
                             <label class="text-white">Search</label>
                             </div>
                    	    <button type="submit" class="btn btn-primary" >{{ __('common.Search') }}</button>
                    	</div>
                    			
                    </div>
                        
                </form>
			@endif	
				
                <div class="card-body">
               <table id="example1" class="table table-bordered table-striped border dataTable dtr-inline ">
                  <thead class="bg-primary">
                  <tr role="row">
                      <th>{{ __('common.S.NO') }}</th>
                      <th class="text-center">Image</th>
                            <th>{{ __('staff.Teacher Name') }}</th>
                            @if(Session::get('role_id') != 3  && Session::get('role_id') != 2)
                            <th>{{ __('Class Teacher') }}</th>
                           
                            <th>{{ __('Teaching Subject') }}</th>
                           
                          
                            
                            <th>{{ __('common.Mobile') }}</th>
                            <th>{{ __('common.E-Mail') }}</th>
                            @endif
                            <!--<th>Joining Date</th>-->
                             @if(Session::get('role_id') == 3)
			              <th>{{ __('common.Action') }}</th>
			          @else
                        @if(($getPermission->download ?? '') == 1 || ($getPermission->edit ?? '') == 1 || ($getPermission->deletes ?? '') == 1)   
                          <th>{{ __('common.Action') }}</th>
                        @endif
                        @endif
                          
                    </tr>
                             
                      
                  </thead>
                  <tbody>
                      
                      @if(!empty($data))
                        @php
                    
                           $i=1;
                        @endphp
                        
                        @foreach ($data  as $key => $item)
                        @php
                            $chat='';
                            $complaint_id= '';
                            $viewStatus= null;
                         $chatData = DB::table('complaint')
                          ->where('session_id', '=',Session::get('session_id'))
                           ->where('branch_id', '=',Session::get('branch_id'))
                         ->where('admission_id', '=',Session::get('id'))->where('teacher_id_to_complaint','=',$item->id ?? '')->whereNull('deleted_at')->first();
                        
                        if(!empty($chatData)){
                              $chat = $chatData->chat ?? '';
                              $complaint_id = $chatData->id;
                              $viewStatus  = json_decode($chatData->view_status,true)[Session::get('id')] ?? 2;
                        }
                        @endphp
                  
                        <tr>
                                <td>{{ $i++ }}</td>
                                <td class="text-center">
                                    <img class="profileImg pointer" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$item['photo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" data-img="@if(!empty($item->photo)) {{ env('IMAGE_SHOW_PATH').'profile/'.$item['photo'] }} @endif" >
                                </td>
                                
                                @php
                                
                               
                                @endphp
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}  <span class='badge badge-primary'>@if(Session::get('role_id') != 1){{$item->class_type_id == Session::get('class_type_id') ? 'Class Teacher' : ''}}@endif</span></td>
                                
                                   @if(Session::get('role_id') != 3  && Session::get('role_id') != 2)
                            <td>
                                
                                @if(isset($item->class_type_id) && !empty($item->class_type_id))
                                    @php
                                        $classIds = unserialize($item->class_type_id);
                                        $classIds = is_array($classIds) ? array_map('intval', $classIds) : [];
                                        
                                        $classes = DB::table('class_types')
                                                    ->whereIn('id', $classIds)
                                                    ->whereNull('deleted_at')
                                                    ->pluck('name', 'id')
                                                    ->toArray();
                                
                                        // Ensure $classNames contains only the names for existing class type IDs
                                        $classNames = array_intersect_key($classes, array_flip($classIds));
                                    @endphp
                                    
                                    {{ implode(', ', $classNames) }}
                                @endif
                        
                      
                            </td>
                            @endif
                            
                            @if(Session::get('role_id') !=  3 && Session::get('role_id') !=  2 ) 
                                <td>
                                    @php
                                    $teachingSubjects = DB::table('teacher_subjects')
                                       ->leftJoin('subject', 'subject.id', '=','teacher_subjects.subject_id')
                                       ->leftJoin('class_types', 'class_types.id', '=','teacher_subjects.class_type_id')
                                    ->where('teacher_subjects.teacher_id',$item->id)->whereNull('teacher_subjects.deleted_at')
                                     ->select('teacher_subjects.*','subject.name as subject_name','class_types.name as class_name')
                                    ->get();
                                  
                                    @endphp
                                 
                                 @if(count($teachingSubjects) > 0)
                                 
                                 @foreach($teachingSubjects as $subjects)
                                 
                                 
                                 {{$subjects->subject_name ?? ''}}({{$subjects->class_name ?? ''}})<br>
                                 
                                 @endforeach
                                 @else
                                    <span class='text-danger' style='font-size:12px'> Timetable not scheduled </span> <br>
                                    <a href="{{url('teacher_subject_add')}}"class='text-primary'  style='font-size:12px;cursor:pointer' target='_blank'>Click here to assign subject</a>
                                 @endif
                                
                                
                                </td>
                                
                               
                                
                                
                                <td>{{ $item['mobile']  }}</td>
                                <td>{{ $item['email']  }}</td>
                                 @endif
                                <!--<td>{{ $item['joining_date']  }}</td>-->
                                  
                         
                                  
                                
                               
                                    
                                     @if(Session::get('role_id') != 3) 
                                      <td>
                                        <a class="btn btn-success btn-xs" data-toggle="dropdown" title="Show Option"><i class="fa fa-bars"></i></a>
                                     
                                        <ul class="dropdown-menu" style="">
                                    @if(Session::get('role_id') == 1)        <a href="{{url('joining_letter_print')}}/{{$item->id}}" target="blank"><li class="dropdown-item text-success" title="Joining print"><i class="fa fa-print text-success"></i>{{ __('staff.Joining print') }}</li></a>@endif
                                    @if(Session::get('role_id') == 1)        <a href="{{url('teachers_idCard')}}/{{$item->id}}" target="blank"><li class="dropdown-item text-success" title="Id Card"><i class="fa fa-print text-success"></i>{{ __('Id Print') }}</li></a>@endif
                                    @if( Session::get('role_id') == 1 && $item['teacher_update'] == 0)      <a href="{{url('teachers/edit',$item->id)}}"><li class="dropdown-item text-primary" title="Edit"><i class="fa fa-edit text-primary"></i> {{ __('common.Edit') }}</li></a>
                                    @else
                                    <a href="{{url('teachers/edit',$item->id)}}"><li class="dropdown-item text-primary" title="Edit"><i class="fa fa-edit text-primary"></i> {{ __('common.Edit') }}</li>
                                    @endif
                                    @if(Session::get('role_id') == 1)       <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"><li class="dropdown-item text-danger" title="Delete"><i class="fa fa-trash-o text-danger"></i> {{ __('common.Delete') }}</li></a>     @endif                                   
                                        </ul>
                                                                  <button type="button" class="btn btn-info btn-lg teachers_id" data-teachers_id='{{$item->id}}'  data-toggle="modal" data-target="#myModal">Open Modal</button>

                                         </td>
                                        @endif
                               
                           @if(Session::get('role_id') == 3) 
                           
                           
                         
                         
                                <td>
                                    <a class="btn btn-{{$viewStatus == "" ? 'info' : ($viewStatus == 0 ? 'danger' : 'primary')}} btn-xs modal_complaint" id='complaint_id_{{$item->id}}' data-complaint_id="{{$complaint_id}}"data-teacher_name="{{ $item['first_name']  ?? '' }}"data-teacher_id="{{$item->id ?? ''}}" data-chat="{{$chat}}"><i class="fa fa-exclamation-circle" aria-hidden="true" ></i> Start / View Conversation
                                    </a>
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
    </section>
</div>

<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Assign Subjects</h4>
                <button type="button" class="close btn-close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ url('teacher_subjects_multiple') }}" method="POST">
    @csrf
            <div class="modal-body">
                <div class="row">
                    <!-- Left Side: Class Types -->
                    <div class="col-md-4">
                        <div class="row">
                            <input type="hidden" name="teachers_id" id="teachers_id">
                            @if(!empty($classType))
                                @foreach($classType as $item)
                                    <div class="col-6 mb-2">
                                        <div class="form-group">
                                            <input type="checkbox" class="getData" name="class_type_id[]" value="{{ $item->id }}">
                                            <label>{{ $item->name }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <!-- Right Side: Subjects -->
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-12">
                                <div class="row bg-primary p-1 mb-1">
                                    <div class="col-6 d-flex ">
                                        <p class="title_check text-white">Select Subject</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-12">
                                <div class="row subjectContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>

            </div>
</form>
        </div>

    </div>
</div>

        
        <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        
        
<script>

$('.teachers_id').click(function() {
	var teachers_id = $(this).data('teachers_id');
	$('#teachers_id').val(teachers_id);
});


$(document).on("click", ".getData", function() {
    var basurl = "{{ url('/') }}";
    var class_type_id = $(this).val();
    var teachers_id = $('#teachers_id').val();

    if ($(this).prop('checked') == true) {
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
        });

        $.ajax({
            type: "POST",
            url: basurl + "/getDataClassSubject",
            data: { class_type_id: class_type_id,teachers_id:teachers_id },
            dataType: "json",
            success: function (response) {
                // ✅ हर class का block अलग append
                $(".subjectContainer").append(response.html);
            }
        });
    } else {
        // ✅ Uncheck करने पर group remove
        $(".subjectContainer").find(".class_type_id" + class_type_id).remove();
    }
});





  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
  
<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }} </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('delete_staff') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
         </div>
       </form>

    </div>
  </div>
</div>

        
<div id="profileImgModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">

    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <img id="profileImg" src="" width="100%" height="100%">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-bs-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div> 


<div class="modal fade" id="modal_complaint_modal">
<div class="modal-dialog modal-dialog-centered">
  <div class="modal-content">
    <div class="modal-body">
        <div class="col-md-12">
            <div class="centered_flex">
                <i class="fa fa-exclamation-circle" aria-hidden="true"></i>
                <p>Chat Panel</p>
            </div>
        </div>
        
          <div class="modal-body">
        <div class="chat-container">
   <div class="messages-container">
       
        </div>
          <div class="input-group mb-3 mt-2">
            <input type="text" class="form-control" id="message"placeholder="Type your message..." aria-label="Type your message" aria-describedby="send-button">
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" id="send-button">Send</button>
            </div>
          </div>
        </div>
      </div>
        
        <!--<div class="col-md-12 text-right">-->
        <!--    <button class="modal_btn bg-white change_status" data-action="Discard" data-bs-dismiss="modal">Discard</button>-->
        <!--    <button class="modal_btn bg-warning change_status" data-action="Confirm" data-bs-dismiss="modal">Send</button>-->
        <!--</div>-->
   
    </div>
  </div>
</div>
</div>

<style>
  /* Style for Messages Container */
  .messages-container {
    height: 300px;
    overflow-y: scroll;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }

  /* Style for Received Messages */
  .message.received {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 10px;
  }

  .received-message {
    background-color: #f0f0f0;
    padding: 8px 12px;
    border-radius: 10px;
    max-width: 70%;
    word-wrap: break-word;
  }

  .received-label {
    font-size: 12px;
    color: #888;
    margin-bottom: 4px;
  }

  /* Style for Sent Messages */
  .message.sent {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    margin-bottom: 10px;
  }

  .sent-message {
    background-color: #007bff;
    color: #fff;
    padding: 8px 12px;
    border-radius: 10px;
    max-width: 70%;
    word-wrap: break-word;
  }

  .sent-label {
    font-size: 12px;
    color: #888;
    margin-bottom: 4px;
  }

  /* Style for New Message Form */
  #messageForm {
    margin-top: 10px;
  }

    .profileImg {
        width:50px;
        height:50px;
        border-radius:10%;
        margin:5px;
    }
</style>


<script>
$(document).ready(function(){
  
var teacher_id = '';
  var student_id = '';
  var complaint_id = '';

$('.modal_complaint').click(function(){
    var $messagesContainer = $('.messages-container');
    $messagesContainer.html('');
    teacher_id = $(this).attr('data-teacher_id');
    complaint_id = $(this).attr('data-complaint_id');
    var teacher_name = $(this).attr('data-teacher_name');
     student_id = "{{Session::get('id')}}";
    var student_name = "{{Session::get('first_name')}}";
    var jsonString = $(this).attr('data-chat');
   
   
   if(jsonString != '')
   {
var messages = JSON.parse(jsonString);


$.each(messages, function(index, messageObject) {
  var userId = Object.keys(messageObject)[0];
  var messageContent = messageObject[userId];
 var messageType = '';
var user_name = '';
    if(userId === student_id )
    {
        messageType ='sent';
        user_name = 'Me';
    }
    else
    {
           messageType ='received';
    
        if(userId === '1')
        {
            user_name='Admin';
        }
    else{
        
    user_name ='Teacher';
    }
        
    }
 

  var messageHTML = `
    <div class="message ${messageType}">
      <div class="${messageType}-label">${user_name}</div>
      <div class="${messageType}-message">${messageContent}</div>
    </div>
  `;

  $messagesContainer.append(messageHTML);
});
 
}
    $('#modal_complaint_modal').modal('show');
    setTimeout(function() {
        var scrollHeight = $messagesContainer.innerHeight();
        $messagesContainer.animate({scrollTop: $messagesContainer[0].scrollHeight}, 'slow');
    }, 800);
});


$('#send-button').click(function() {
        var message = $('#message').val();
        

        var baseUrl = "{{ url('/') }}";

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: baseUrl + '/sendConversation',
            data: {
                complaint_id: complaint_id,
                message: message,
                user_id:student_id,
                teacher_id:teacher_id,
                admin_id:1,
                teacher_id:teacher_id,
                student_id:student_id,
                admin_status:0,
                teacher_status:0,
                student_status:1,
                
            },
            success: function(data) {
               
               $('#complaint_id_'+ teacher_id).attr('data-chat',data.data);
             
                $('#complaint_id_'+ teacher_id).removeAttr('class'); 
                $('#complaint_id_'+ teacher_id).attr('class', "btn btn-primary btn-xs modal_complaint"); 
                 var anchorElement = document.getElementById('complaint_id_'+teacher_id);
                  anchorElement.click();
                     $('#message').val(''); 
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
                alert('Failed to send message');
            }
        });
    });
    
$('.profileImg').click(function(){
    var profileImgUrl = $(this).data('img');
    if(profileImgUrl != ''){
        $('#profileImgModal').modal('toggle');
        $('#profileImg').attr('src',profileImgUrl);
    }
});
});
</script>
@endsection 