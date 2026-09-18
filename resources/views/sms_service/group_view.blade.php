@php
$getRole = Helper::roleType();
$classType = Helper::classType();

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
                        @if(Session::get('') == 3)
                            <h3 class="card-title"><i class="fa fa-leanpub"></i> &nbsp; {{ __('Send Group Whatsapp Messages') }}</h3>
                        @else						    
							<h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{ __('Send Group Whatsapp Messages') }}</h3>
						@endif
							<div class="card-tools"> 
							@if(Session::get('role_id') !== 3)
							    <a href="{{url('group_add')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a> 
							    <a href="{{url('send_message_terminal')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
                            @endif

                                
                            </div>
						</div>
						
                 
        <section class="content">
           
            <div class="container-fluid">
                 <form action='{{url("group_view")}}' method='post'>
                     @csrf
                <div class="row m-2">
                    <div class=" col-md-12 title"><h5 class="text-danger">{{ __('smsService.Select Candidates') }} :-</h5></div>
                   
                       	
                    <div class="col-md-3 class_type_id">
                		<div class="form-group">
                			<label>Class</label>
                			<select class="form-control select2" id="class_type_id" name="class_type_id" >
                			<option value="">Select</option>
                             @if(!empty($classType)) 
                                  @foreach($classType as $type)
                                     <option value="{{ $type->id ?? ''  }}"  {{$search['class_type_id'] == $type->id ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                	    </div>
                	</div>                        	
                
                    <div class="col-md-1">
                         <label class="text-white">{{ __('messages.Search') }}</label>
                	    <button class="btn btn-primary" onclick="SearchValue()">{{ __('smsService.Search') }}</button>
                	</div>
                </div>
      
      </form>
      
         <div class="row m-2">
                    <div class="col-md-12" id="student_list_show" style="overflow-y: scroll;">
               <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                            <thead class="bg-primary">
                                <tr role="row">
                                    <th>
                                        Sr.No.
                                        <!--<input class="ml-3" type="checkbox" id="all_checkbox" checked></th>-->
                                    <th>Class</th>
                                    <th>Group Name</th>
                                    <th>Group Id</th>
                                    <th>Action</th>
                                  
                                </tr>
                            </thead>
                            <tbody>
 
                                @if(!empty($data))
                                @php
                                    $i=1
                                @endphp

                                @foreach ($data  as $item)
                                <tr>
                                    <td>
                                    {{ $i++ }}
                                     </td>
                                   <td>{{$item->class_name ?? ''}}</td>
                                   <td>{{$item->group_name ?? ''}}</td>
                                   <td>{{$item->group_id ?? ''}}</td>
                                   <td class='d-flex'>
                                       <a href="{{url('group_edit')}}/{{$item->id ?? ''}}" class="btn btn-info btn-xs m-1" > &nbsp;<i class="fa fa-edit"></i> </a>
                                        <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData btn btn-danger btn-xs m-1" title="Delete"> &nbsp;<i class="fa fa-trash"></i> </a>
                                       @if($item->status == 0) 
                                       
                                       <div>
                                   <form action='{{url("whatsapp_group_status")}}' method='post'>
                                       @csrf
                                       <input type='hidden' name='id' value='{{$item->id ?? ''}}'>
                                       <input type='hidden' name='class_type_id' value='{{$search["class_type_id"] ?? ''}}'>
                                         <button type='submit'class='btn btn-success btn-xs m-1'>Active</button>  
                                   </form>
                                   
                              </div>
                                   @else
                                   <div>
                                    <form action='{{url("whatsapp_group_status")}}' method='post'>
                                       @csrf
                                       <input type='hidden' name='id' value='{{$item->id ?? ''}}'>
                                       <input type='hidden' name='class_type_id' value='{{$search["class_type_id"] ?? ''}}'>
                                          <button type='submit'class='btn btn-danger btn-xs m-1'>Deactive</button>
                                   </form>
                          </div>
                                   @endif
                                   </td>
                                </tr>
                                @endforeach
                                     @endif
                             
                            </tbody>
                        </table>
                        
                    </div>
                       
                </div>
        </section>
        <section>
    </div>
</div>
</div>
</div>
</div>
</section>
</div>



<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('group_delete') }}" method="post">
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


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
@endsection

