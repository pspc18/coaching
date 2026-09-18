@php
  $feesType = Helper::feesType();
@endphp

@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">    
         

        <div class="col-md-4 pr-0">
            <div class="card card-outline card-orange mr-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-id-badge"></i> &nbsp; Edit Leave Application</h3>
            <div class="card-tools">
           
            </div>
            
            </div>                 
                <form id="quickForm" action="{{ url('leaveUpdate') }}/{{$data['id']}}" method="post">
                @csrf
                <div class="row m-2">
                       
                               <div class="col-md-12">
                    
                			<label style="color:red;">Subject*</label>
            			<input class="form-control @error('subject') is-invalid @enderror" type="input" id="subject" name="subject" value="{{$data['subject'] ?? ''}}">
                             @error('subject')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div> 
                        <div class="col-md-6">
                			<label style="color:red;">From Date*</label>
            				<input class="form-control @error('from_date') is-invalid @enderror" type="date" id="from_date" name="from_date" value="{{$data['from_date'] ?? ''}}">
                             @error('from_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="col-md-6">
                			<label style="color:red;">To Date*</label>
            				<input class="form-control @error('to_date') is-invalid @enderror" type="date" id="to_date" name="to_date" value="{{$data['to_date'] ?? ''}}">
                             @error('to_date')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror                  			
                    	</div>                     	
                        <div class="col-md-12">
                    			<label style="color:red;">Reason*</label>
                    			<textarea class="form-control @error('reason') is-invalid @enderror" type="text" name="reason" id="reason" placeholder="Reason" >{{$data['reason'] ?? ''}}</textarea>
                             @error('reason')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror 
                    	</div>                      	
                </div>
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-info">Update </button>
                    </div>
                </div>
                </form>
            </div>          
        </div>
        
        
     <div class="col-md-8 pl-0 d-none">
            <div class="card card-outline card-orange ml-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-id-badge"></i> &nbsp; {{ __('Applied leave list') }} </h3>
            <div class="card-tools">
            </div>
            
            </div>                 
                <div class="row m-2">
                    <div class="col-md-12">
                	</div>
                    <div class="col-md-12">
                       <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead>
                          <tr role="row">
                              <th>{{ __('messages.Sr.No.') }}</th>
                              <th>{{ __('messages.Status') }}</th>
                              <th>{{ __('messages.Subject') }}</th>
                              <th>{{ __('messages.From Date') }}</th>
                              <th>{{ __('messages.To Date') }}</th>
                              <th>{{ __('messages.Reason') }}</th>
                              <th>{{ __('messages.Action') }}</th>
                              </tr>
                              
                              
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1
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
                                            
                                            <!--<a data-id="{{ $item['admission_id'] ?? '' }}" style="{{  $item['status'] == 0 ? 'display:none'   : ''  }}" data-status="0" class="btn btn-xs btn-success reminder_status" data-text="Activate"> &nbsp; Approved &nbsp;</a>-->
                                            <a style="user-select:none;font-size: 12px;"class="btn btn-xs btn-danger reminder_status w-100" >Denied</a>                                                              
                                        </td>
                                        @endif
                                        
                                         @if($item['status']== "2")
                                        <td>
                                            
                                            <!--<a data-id="{{ $item['admission_id'] ?? '' }}" style="{{  $item['status'] == 0 ? 'display:none'   : ''  }}" data-status="0" class="btn btn-xs btn-success reminder_status" data-text="Activate"> &nbsp; Approved &nbsp;</a>-->
                                            <a style="user-select:none;font-size: 12px;"class="btn btn-xs btn-warning reminder_status w-100" >Pending</a>                                                              
                                        </td>
                                        @endif
                                        <td>{{ $item['subject'] ?? '' }}</td>
                                        <td>{{ $item['from_date'] ?? '' }}</td>
                                        <td>{{ $item['to_date'] ?? '' }}</td>
                                        <td>{{ $item['reason'] ?? '' }}</td>
                                        
                                        <td>
                                              <a href="{{ url('leaveUpdate') }}/{{$item['id'] ?? '' }}" class="btn btn-primary btn-xs"><i class="fa fa-edit"></i></a>
                                              <a href="javascript:;" data-id="{{$item->id}}"  class="btn btn-danger btn-xs ml-1"data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData"><i class="fa fa-trash"></i></a>
                                            
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
        </div>
    </div>  

</div>
</section>
</div>

<script>
    $('#fees_type_id').on('change', function(e){
    
	var fees_type_id = $(this).val();
    $.ajax({
         headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
	  url: '/duedate/'+fees_type_id,
	  success: function(data){
      $("#due_date").val(data);
	  }
	});	
});
</script>
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('leave_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
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
@endsection      