@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Add Leave')
@section('page_title', 'ADD LEAVE')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">
     <div class="leave-form-box p-3">              
                <form id="quickForm" action="{{ url('applyLeaveStudent') }}" method="post">
  
                @csrf
                
                        
                    <div class="form-group">
                			<label style="color:red;">{{ __('messages.Subject') }}*</label>
            			<input class="leave-input @error('subject') is-invalid @enderror" type="input" id="subject" name="subject" placeholder="Subject">
                             @error('subject')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div> 
                        <div class="form-group">
                			<label style="color:red;">{{ __('messages.From Date') }}*</label>
            				<input class="leave-input @error('from_date') is-invalid @enderror" type="date" id="from_date" name="from_date" value="{{date('Y-m-d')}}">
                             @error('from_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="form-group">
                			<label style="color:red;">{{ __('messages.To Date') }}*</label>
            				<input class="leave-input @error('to_date') is-invalid @enderror" type="date" id="to_date" name="to_date" value="{{date('Y-m-d')}}">
                             @error('to_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="form-group">
                    			<label style="color:red;">{{ __('messages.Reason') }}*</label>
                    			<textarea class="leave-input @error('reason') is-invalid @enderror" type="text" name="reason" id="reason" placeholder="Reason"></textarea>
                             @error('reason')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror 
                    	</div>                      	
              
                <button type="submit" class="btn-leave-send">SUBMIT</button>
                </form>
            </div> 
            
            
             <h6 class="leave-title">LEAVE DETAILS</h6>
 <div class="common-box m-2 border-0">
                          <table  class="common-table w-100">
                          <thead>
                          <tr>
                              <th>{{ __('messages.Sr.No.') }}</th>
                              <th>{{ __('messages.Status') }}</th>
                              <th>{{ __('messages.Subject') }}</th>
                              <th>{{ __('Date') }}</th>
                              
                              <th>{{ __('messages.Reason') }}</th>
                              <th>{{ __('messages.Action') }}</th>
                              </tr>
                              
                              
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1;
                                 
                                @endphp
                                @foreach ($dataview  as $item)
                             
                               @if(Session::get('id')==$item['admission_id'])
                               
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    @if($item['status']== "1")
                                        <td>
                                            
                                            <a style="user-select:none;font-size: 12px;"class="btn btn-xs btn-success reminder_status w-100" >Approved</a>
                                            <!--<a data-id="{{ $item['admission_id'] ?? '' }}" style="{{  $item['status'] == 1 ? 'display:none'   : ''  }}" data-status="1" class="btn btn-xs btn-danger reminder_status" data-text="Deactivate">Deactive </a>                                                               -->
                                        </td>
                                        @endif
                                
                                    @if($item['status']== "0")
                                        <td>
                                        <a style="user-select:none;font-size: 12px;"class="btn btn-xs btn-danger reminder_status w-100" >Denied</a>                                                              
                                        </td>
                                        @endif
                                        
                                         @if($item['status']== "2")
                                        <td>
                                        <a style="user-select:none;font-size: 12px;"class="btn btn-xs btn-warning reminder_status w-100" >Pending</a>                                                              
                                        </td>
                                        @endif
                                        <td>{{ $item['subject'] ?? '' }}</td>
                                        <td>{{date('d-m', strtotime($item['from_date'])) ?? '' }}/{{date('d-m-Y', strtotime($item['to_date'])) ?? '' }}</td>
                                        
                                        <td>{{ $item['reason'] ?? '' }}</td>
                                        
                                        <td>
                                                 @if($item['status']== "2")
                                              <a href="{{ url('updateLeaveStudent') }}/{{$item['id'] ?? '' }}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
                                            <a href="javascript:;" 
                                                   data-id="{{ $item->id }}"  
                                                   class="btn btn-danger btn-xs ml-1 deleteData"
                                                   data-bs-toggle="modal" data-bs-target="#Modal_id">
                                                   <i class="fa fa-trash"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                           @endforeach
                        @endif
                          </tbody>
                          </table>
                        
					</div>
        </div>
        
    </div>  

</div>

<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('deleteLeaveStudent') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id >
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
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  $('#delete_id').val(delete_id); 
  } );
</script>




@endsection      