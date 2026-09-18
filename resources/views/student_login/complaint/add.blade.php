@php
$getUser = Helper::getUser();
@endphp
@extends('student_login.layout.app')
@section('title', 'Add Complaint')
@section('page_title', 'ADD COMPLAINT')
@section('page_sub', Session::get('first_name') . '-' . $getUser['ClassTypes']['name'])
@section('content')
<section class="common-page">
 <div class="common-box m-2 border-0">
     <div class="leave-form-box p-3">
   <form id="quickForm" action="{{ url('complaint_add') }}" method="post" enctype="multipart/form-data"> 
						    @csrf						
						
									<div class="form-group">
										<label style="color: red;">{{ __('common.Subject') }}*</label>
										<input class="leave-input @error('subject') is-invalid @enderror" type="text" id="subject" name="subject" placeholder="{{ __('common.Subject') }}"> 
                                        @error('subject')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
							
									<div class="form-group">
										<label style="color: red;">{{ __('dashboard.Description') }}*</label>
										<textarea class="leave-input  @error('description') is-invalid @enderror" type="text" id="description" name="description" placeholder="{{ __('dashboard.Description') }}"></textarea> 
                                        @error('description')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror								    
								    </div>
							
							
							
							
                       
                           <button type="submit" class="btn-leave-send">SUBMIT</button>
                        
					    </form>
                          </div>
						
                            <h6 class="leave-title">COMPLAINT DETAILS</h6>
 <div class="common-box m-2 border-0">
                            <table  class="common-table w-100">
                          <thead>
                          <tr>
                              
                              <th>{{ __('common.SR.NO') }} </th>
                               
                                    <th>{{ __('common.Subject') }} </th>
                                    <th>{{ __('dashboard.Description') }} </th>
                                    <th>{{ __('common.Date') }} </th>
                                    <th>Admin Action </th>
                                    <th>{{ __('common.Action') }}</th>
                          </thead>
                          <tbody>
                              
                              @if(!empty($data))
                                @php
                                   $i=1;
                                @endphp
                                @foreach ($data  as $item)
                                <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['subject'] ?? '' }}</td>
                                        <td style='word-wrap: break-word;'>{{ $item['description'] ?? '' }}</td>
                                         
                                        <td>{{ date('d-m-Y', strtotime($item->date)) ?? '' }}</td>
                                         <td>
                                                {{ $item['admin_action'] ?? '' }}
                                         </td>
                                      
                                                   
                                                     <td>
                                                          @if($item['admin_action'] == '')
                                                    <a href="{{url('complaintEditStudent') }}/{{$item->id}}" class="btn btn-primary  btn-xs ml-3 " title="Edit Complaint"><i class="fa fa-edit"></i></a> 
                                                  
                									<a href="javascript:;" data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger  btn-xs ml-3 " title="Delete Book"><i class="fa fa-trash-o"></i></a> 
                                                  @endif 
                                                  </td>
                                                   
                                            
                                            
                              </tr>
                              @endforeach
                            @endif
                            </tbody>
                        </table>
                        
					</div>
			</div>
			<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('complaintDeleteStudent') }}" method="post"> 
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
	</section>
	
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
     $('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});
</script>
@endsection