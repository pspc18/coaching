@extends('layout.app') 
@section('content')



<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-list-alt"></i> &nbsp;{{ __('dashboard.View Complaints') }}</h3>
							<div class="card-tools"> 
							
					        @if(Session::get('role_id') !== 1)		
							<a href="{{url('complaint_add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(28)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a> 
						    @endif	
						    @if(Session::get('role_id') !== 3)
							<a href="{{url('reception_file')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a> 
							@endif
							
                             @if(Session::get('role_id') == 3)
                                  <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                              @endif
                           
							</div>
						</div>
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
          <thead class="bg-primary">
          <tr role="row">
              
              <th>{{ __('common.SR.NO') }} </th>
                @if(Session::get('role_id') != 3) 
                    <th>{{ __('common.Name') }} </th>
                    <th>{{ __('common.Mobile No.') }} </th>
                 @endif
                    <th>{{ __('common.Subject') }} </th>
                    <th>{{ __('dashboard.Description') }} </th>
                    <th>{{ __('common.Date') }} </th>
                    <th>Admin Action </th>
                     @if(Session::get('role_id') == 1)	
                        <th>{{ __('common.Action') }}</th>
                    @endif
          </thead>
          <tbody>
              
              @if(!empty($data))
                @php
                   $i=1;
                @endphp
                @foreach ($data  as $item)
                <tr>
                        <td>{{ $i++ }}</td>
                         @if(Session::get('role_id') != 3) 
                    <td>{{ $item['Admission']['first_name'] ?? '' }} {{ $item['Admission']['last_name'] ?? '' }} ({{ $item['ClassType']['name'] ?? '' }})</td>
                        <td>{{ $item['Admission']['mobile'] ?? '' }}</td>
                 @endif
                        
                        <td>{{ $item['subject'] ?? '' }}</td>
                        <td style='word-wrap: break-word;'>{{ $item['description'] ?? '' }}</td>
                         
                        <td>{{ date('d-m-Y', strtotime($item->date)) ?? '' }}</td>
                         <td>
                            @if($item->teacher_id_to_complaint != '')
                                @php
                                    $viewStatus = json_decode($item->view_status,true)[Session::get('id')] ?? 0;
                                    $teacher_name = DB::table('teachers')->where('id',$item->teacher_id_to_complaint)->first();
                                    //dd($item->view_status);
                                @endphp
                                <a class="btn btn-{{$viewStatus == 0 ? 'danger' : 'primary'}} btn-xs modal_complaint" id='complaint_id_{{$item->id}}' data-complaint_id="{{$item->id}}" data-teacher_id="{{$item->teacher_id_to_complaint ?? ''}}" data-teacher_name="{{ $teacher_name->first_name ?? '' }} {{ $teacher_name->last_name ?? '' }}"
                                    data-student_name="{{$item['Admission']['first_name'] ?? '' }}{{' '}}{{ $item['Admission']['last_name'] ?? '' }}{{' '}}({{ $item['ClassType']['name'] ?? '' }})"data-student_id="{{$item->admission_id ?? ''}}"data-chat="{{$item->chat ?? ''}}"><i class="fa fa-exclamation-circle" aria-hidden="true" ></i> View Chat
                                </a>
                             @else
                                {{ $item['admin_action'] ?? '' }}
                             @endif
                         </td>
                      
                                                 
                           
                            @if(Session::get('role_id') == 3 )
                            
                                    @if($item['admin_action'] == '')
                                     <td>
                                    <a href="{{url('complaint_edit') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 {{ Helper::permissioncheck(28)->edit ? '' : 'd-none' }}" title="Edit Complaint"><i class="fa fa-edit"></i></a> 
                                  
									<a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 {{ Helper::permissioncheck(28)->delete ? '' : 'd-none' }}" title="Delete Book"><i class="fa fa-trash-o"></i></a> 
                                  </td>
                                    @endif
                                    @else
                                     <td>
                                       @if($item->teacher_id_to_complaint == '')
                                    @if(Session::get('role_id') != 3)
                                    
                                   <button class="btn btn-warning btn-xs ml-3 review {{ Helper::permissioncheck(28)->edit ? '' : 'd-none' }}" data-description="{{ $item['description'] }}" data-id="{{ $item['id'] }}" title="Edit c"><i class="fa fa-envelope"></i></button> 
                                    @endif                           
                                    @endif   
                                    @if((Session::get('role_id')) == 1)
									<a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 {{ Helper::permissioncheck(28)->delete ? '' : 'd-none' }}" title="Delete Book"><i class="fa fa-trash-o"></i></a> 
                                    	@endif
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


<script>
    $('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});

$(document).on('click', ".review", function() {
  $('#viewModal').modal('toggle');
  var complaint_id = $(this).data('id');
  $('#complaint_id').val(complaint_id);
  	var description = $(this).data('description');
		$('#admin_action').val(description);
});
</script>

<div class="modal" id="viewModal">
	<div class="modal-dialog">
		<div class="modal-content" >
			<div class="modal-header">
				<h4 class="modal-title ">{{ __('dashboard.Submit Review') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('complaint_action') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type="hidden" id="complaint_id" name="complaint_id">
					<label style="color: red;">{{ __('dashboard.Admin Action') }}*</label>
					<textarea class="form-control  @error('admin_action') is-invalid @enderror" type="text" id="admin_action" name="admin_action" placeholder="{{ __('dashboard.Admin Action') }}"></textarea> 
					
					 </div>
				<div class="modal-footer">
				<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.submit') }}</button>				    
				<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
	
				</div>
			</form>
		</div>
	</div>
		</div>

<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('delete_complaint') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }}  ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
		</div>
	</div>
		</div>
	</div>
	</section>
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
            <input type="text" class="form-control" id='message' placeholder="Type your message..." aria-label="Type your message" aria-describedby="send-button">
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
  
  </style>
<script>

var teacher_id = '';
  var student_id = '';
  var complaint_id = '';
    $('.modal_complaint').click(function(){
    var $messagesContainer = $('.messages-container');
    
    $messagesContainer.html('');
    teacher_id = $(this).attr('data-teacher_id');
    var teacher_name = $(this).attr('data-teacher_name');
    student_id = $(this).attr('data-student_id');
    complaint_id = $(this).attr('data-complaint_id');
    var student_name = $(this).attr('data-student_name');
    var jsonString = $(this).attr('data-chat');
   
   
   if(jsonString != '')
   {
var messages = JSON.parse(jsonString);


$.each(messages, function(index, messageObject) {
  var userId = Object.keys(messageObject)[0];
  var messageContent = messageObject[userId];
 var messageType = '';
var user_name = '';
    if(userId === '1' )
    {
        messageType ='sent';
        user_name = 'Admin';
    }
    else
    {
           messageType ='received';
    
        if(userId === teacher_id)
        {
            user_name= teacher_name + " (Teacher)";
        }
    else{
        
    user_name =student_name;
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
                user_id:1,
                admin_id:1,
                teacher_id:teacher_id,
                student_id:student_id,
                admin_status:1,
                teacher_status:0,
                student_status:0,
                
            },
            success: function(data) {
               
               $('#complaint_id_'+complaint_id).attr('data-chat',data.data);
               $('#complaint_id_'+ complaint_id).removeAttr('class'); 
                $('#complaint_id_'+ complaint_id).attr('class', "btn btn-primary btn-xs modal_complaint");
                $('#message').val(''); 
                
                
                
                var anchorElement = document.getElementById('complaint_id_'+complaint_id);
                anchorElement.click();
            },
            error: function(xhr, status, error) {
                console.error('Error sending message:', error);
                alert('Failed to send message');
            }
        });
    });

</script>
@endsection