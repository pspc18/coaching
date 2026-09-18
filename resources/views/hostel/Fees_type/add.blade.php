@php

@endphp

@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
            
            <div class="col-md-8 pl-0">
                <div class="card card-outline card-orange ml-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp;{{ __('View Fees Type') }}</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
               
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
                                        <th>{{ __('Fees Type Name') }}</th>
                                        <th>{{ __('messages.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="">
                                    @if(!empty($get_data))
                                    @php
                                       $i=1
                                       
                                    @endphp
                                   
                                    @foreach ($get_data  as  $item)
                                    
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name'] ??'' }}</td>
                                            <td>
                                                <a href="{{ url('fees_type_editt') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs" title="Edit Hostel" ><i class="fa fa-edit"></i></a> 
                                                <a href="javascript:;" data-id='{{$item['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3" title="Delete Hostel"><i class="fa fa-trash-o"></i></a>
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
            
            <div class="col-md-4 pr-0">
                <div class="card card-outline card-orange mr-1">
                 <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-hospital-o"></i> &nbsp;{{ __('Add Fees Type') }}</h3>
                <div class="card-tools">
                <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>
                <a href="{{url('students_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> Back</a>-->
                 <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }}</a>
                </div>
                
                </div>                 
                    <form id="quickForm" action="{{ url('fees_type_add') }}" method="post">
                    @csrf
                    <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('Fees Type Name') }}*</label>
                			<input class="form-control @error('fees_type') is-invalid @enderror" type="text" name="fees_type" id="fees_type" placeholder="{{ __('Fees Type Name') }}" value="{{ old('fees_type') }}">
                             @error('fees_type')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
                    	</div>
                    </div>
     
                    <div class="row m-2">
                        <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary">{{ __('messages.submit') }} </button>
                        </div>
                    </div>
                    </form>
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
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('fees_type_deleted') }}" method="post">
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
@endsection      