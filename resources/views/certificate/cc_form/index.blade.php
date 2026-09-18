@php
  $classType = Helper::classType();
  
@endphp
@extends('layout.app') 
@section('content')

<div class="content-wrapper">
    
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp;{{ __('Achievement Certificate') }} </h3>
                    <div class="card-tools">
                     <a href="{{url('cc/form/add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->add ? '' : 'd-none' }}" ><i class="fa fa-plus"></i>{{ __('common.Add') }}  </a>
                    <a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
                    </div>
                    </div>  
                    
        <form id="quickForm" action="{{url('cc/form/index')}}" method="post" >
            @csrf 
            <div class="row m-2">
                <div class="col-md-2">
                      <div class="form-group">
                        <label class="required">{{ __('certificate.Admission No.') }}</label>
                         <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('certificate.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                      </div>
                    </div>
                <div class="col-md-2">
            		<div class="form-group">
            			<label>{{ __('common.Class') }}</label>
            			<select class="select2  form-control" id="class_type_id" name="class_type_id" >
            			<option value="">{{ __('common.Select') }}</option>
                           @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_type_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>
            
                <div class="col-md-1 ">
                     <label for="">&nbsp;</label>
            	    <button type="submit" class="btn btn-primary "onclick="SearchValue()"style="margin-top:28px;">{{ __('common.Search') }}</button>
            	</div>
            			
            </div>
        </form>

    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline">
                  <thead class="">
                  <tr role="row">
                      <th> {{ __('common.SR.NO') }} </th>
                            
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Achievement For</th>
                            <th>Date</th>
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
                                <td>{{ $i++ }}</td>
                                
                                <td>{{ $item['first_name']  }} {{ $item['last_name']  }}</td>
                                <td>{{ $item['class_name']  }}</td>
                                <td>{{ $item['achievement_for']  }}</td>
                                <td>{{date('d-m-Y', strtotime($item['iessu_date'])) ?? '' }}</td>
                                <td>
                                    <a href="{{url('cc/form/edit',$item->id)}}" title="Edit" class="{{ Helper::permissioncheck(5)->edit ? '' : 'd-none' }}"><i class="fa fa-edit text-primary"></i></a>
                                    <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData {{ Helper::permissioncheck(5)->delete ? '' : 'd-none' }}" title="Delete"><i class="fa fa-trash-o text-danger"></i></a>
                                    <a href="{{url('cc_print',$item->id)}}" target="blank" title="Print" class="{{ Helper::permissioncheck(5)->print ? '' : 'd-none' }}"><i class="fa fa-print  text-success"></i></a>
                                </td>
                            </tr>
                      @endforeach
                @endif
            </tbody>
                  </table>
                </div>
              
          
           
        </div>
        
      </div>
      
    </section>
    
  </div>
    
</div>
</div>
</div>
</div>
</section>
</div>
   
   
     
        <script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>
        
        
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
 </script>
  <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"></script>-->

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <!-- Modal Header -->
      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <!-- Modal body -->
      <form action="{{ url('certificate_delete') }}" method="post">
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
  
@endsection