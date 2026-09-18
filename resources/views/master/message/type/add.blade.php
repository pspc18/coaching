@extends('layout.app')
@section('content')

@php
$getPermission = Helper::getPermission();
@endphp

     <div class="content-wrapper">
   
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">    
         
        <div class="col-md-4 pr-0 {{ ($getPermission->add == 1) ? '' : 'd-none'}}">
            <div class="card card-outline card-orange mr-1">
             <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-whatsapp"></i> &nbsp; {{__('master.Message Type') }}</h3>
                			<div class="card-tools">
           
            </div>
            
            </div>                                  

            <form id="quickForm" action="{{ url('messageType') }}" method="post" >
                @csrf
        		<div class="row m-2">
                    <div class="col-md-12">
        				<label class="text-danger">{{__('master.Message Type Name') }} *</label>
        				<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder="{{__('master.Message Type Name') }}" value="{{old('name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
        				@error('name')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				@enderror
        			</div>
                </div>
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">{{ __('common.submit') }}  </button>
                    </div>
                </div>
        
          </form>
            </div>          
        </div>
        
        <div class="{{ ($getPermission->add == 1) ? 'col-md-8 pl-0' : 'col-md-12 pl-0'}}">
            <div class="card card-outline card-orange ml-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-whatsapp"></i> &nbsp;{{ __('master.View Message Type') }}</h3>
            <div class="card-tools">
            <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
            <a href="{{url('messageDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
            </div>
            
            </div>                 
                <div class="row m-2">
                    <div class="col-md-12">
                	</div>
                    <div class="col-md-12">
                       <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead class="bg-primary">
                                          <tr role="row">
                              <th>{{ __('common.SR.NO') }}</th>
                                    <th>{{__('master.Message Type') }}</th>
                                    @if($getPermission->edit == 1)
                                    <th>{{__('master.Status') }} </th>
                                    @endif
                                    @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                     <th>{{ __('common.Action') }}</th>
                                    @endif
                          </thead>
                          <tbody>
                              
                              @if(!empty($data))
                                @php
                                   $i=1;
                                @endphp
                                @foreach ($data as $item)
                                <tr>
                                    <td>{{ $i++ }}</td>
                                    <td>{{ $item['name'] ?? '' }}</td>
                                    @if($getPermission->edit == 1)
                                    <td>
                                        @if($item->status == 0)
                                            <button type="button" class="btn btn-xs btn-primary changeStatus" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="1" data-id="{{ $item->id }}">Inactive</button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-primary changeStatus" data-bs-toggle="modal" data-bs-target="#statusModal" data-status="0" data-id="{{ $item->id }}">Active</button>
                                        @endif
                                    </td>
                                    @endif
                                    @if($getPermission->edit == 1 || $getPermission->deletes == 1)
                                       <td>
                                            @if($getPermission->edit == 1)
                                                <a href="{{ url('messageTypeEdit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs {{ Helper::permissioncheck(9)->edit ? '' : 'd-none' }}" title="Edit"><i class="fa fa-edit"></i></a> 
                                            @endif
                                            @if($getPermission->deletes == 1)   
                                                <a href="javascript:;" data-id='{{ $item->id }}' data-bs-toggle="modal" data-bs-target="#deleteModal" class="deleteData btn btn-primary btn-xs ml-3 {{ Helper::permissioncheck(9)->delete ? '' : 'd-none' }}"><i class="fa fa-trash-o"></i></a>
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
        </div>
    </div>
</div>
</section>
</div>
 
<script>
$('.deleteData').click(function() {
    var delete_id = $(this).data('id'); 

    $('#delete_id').val(delete_id); 
});

$('.changeStatus').click(function() {
    var status = $(this).data('status'); 
    var status_id = $(this).data('id'); 

    $('#status').val(status); 
    $('#status_id').val(status_id); 
});
 </script>
  
<!-- The Modal -->
<div class="modal" id="deleteModal">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">Delete Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('messageTypeDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type="hidden" id="delete_id" name="delete_id">
              <h5 class="text-white">Are you sure you want to delete  ?</h5>
           
      </div>

      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary waves-effect waves-light">Delete</button>
         </div>
       </form>

    </div>
  </div>
</div>
<!-- The Modal -->
<div class="modal" id="statusModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title ">Status Change Confirmation</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('messageTypeStatus') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type="hidden" id="status_id" name="status_id">
              <input type="hidden" id="status" name="status">
              <h5 class="">Are you sure you want to Change Status  ?</h5>
           
      </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary waves-effect waves-light">Yes</button>
            <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">Close</button>
                    
        </div>
       </form>

    </div>
  </div>
</div>

@endsection

