@php
   $getstudents = Helper::getstudents();
   $classType = Helper::classType();
   $getState = Helper::getState();
   $getCity = Helper::getCity();
   $getCountry = Helper::getCountry();
  $getSetting=Helper::getSetting();
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
							<h3 class="card-title"><i class="fa fa-certificate"></i> &nbsp;{{ __('certificate.View Tc Certificate') }}</h3>
							<div class="card-tools"> <a href="{{url('tc/certificate/add')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->add ? '' : 'd-none' }}"><i class="fa fa-plus"></i>{{ __('common.Add') }} </a>
							<a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a> </div>
						</div>
						
						<form id="quickForm" action="{{url('tc/certificate/index')}}" method="post" >
                        @csrf 
            <div class="row m-2">
                <div class="col-md-2">
                      <div class="form-group">
                        <label for="State" class="required">{{ __('certificate.Admission No.') }}</label>
                         <input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="{{ __('certificate.Admission No.') }}" value="{{ $search['admissionNo'] ?? '' }}">
                      </div>
                    </div>
                <div class="col-md-2">
            		<div class="form-group">
            			<label>{{ __('common.Class') }}</label>
            			<select class="select2  form-control" id="student_class" name="student_class" >
            			<option value="">{{ __('common.Select') }}</option>
                         <!--@if(!empty($classType)) -->
                         <!--     @foreach($classType as $type)-->
                         <!--        <option value="{{ $type->id ?? ''  }}" {{($type->id) == old('class_type_id') ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>-->
                         <!--     @endforeach-->
                         <!-- @endif-->
                          
                           @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['student_class']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
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
				
    	<div class="row mb-2 m-2">
		    <div class="col-md-12">	
        <table id="example1" class="table table-bordered table-striped dataTable dtr-inline table-responsive ">
          <thead class="bg-primary">
          <tr role="row">
 

                      <th>{{ __('common.SR.NO') }}</th>
                            
                            <th>{{ __('certificate.Student Name') }}  </th>
                            <th>{{ __('common.Class') }}</th>
                            <th>{{ __('common.Fathers Name') }}</th>
                            <th>{{ __('common.Mothers Name') }}</th>
                            <th>{{ __('common.Date Of  Birth') }}</th>
                            <th>{{ __('certificate.Students Admission No.') }}</th>
                            <th>{{ __('certificate.Admission Date') }}</th>
                           
                            <th>{{ __('common.Action') }}</th>
                             
                      
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
                             
                                <td>{{ $item['student_class'] ?? '' }}</td>
                               <td>{{ $item['father_names']  }}</td>
                                <td>{{ $item['mother_names']  }}</td>
                                <td>{{date('d-m-Y', strtotime($item['dob'])) ?? '' }}</td>
                                <td>{{ $item['admissionNo']  }}</td>
                                <td>{{date('d-m-Y', strtotime($item['admission_date'])) ?? '' }}</td>
                              
                               <td>
                                
                                   <a class="btn btn-primary btn-xs" data-toggle="dropdown" title="Show Option"><i class="fa fa-bars"></i></a>
                                       
                                        <ul class="dropdown-menu p-3" style="">
                                         <a href="{{url('tc/certificate/edit',$item->id)}}" class="{{ Helper::permissioncheck(5)->edit ? '' : 'd-none' }}"><i class="fa fa-edit text-primary" title="Edit"></i> Edit</a><br>
                                      <a href="javascript:;"  data-id='{{$item->id}}' data-bs-toggle="modal" data-bs-target="#Modal_id"  class="deleteData text-danger {{ Helper::permissioncheck(5)->delete ? '' : 'd-none' }}" ><i class="fa fa-trash-o text-danger"></i> Delete</a><br>
                                      <a href="{{url('tc_print',$item->id)}}" target="blank" class="text-warning {{ Helper::permissioncheck(5)->print ? '' : 'd-none' }}"><i class="fa fa-print text-warning"></i>Print1</a><br>
                                      <!--<a href="{{url('tc_print_formate')}}" target="blank" class="text-success"><i class="fa fa-print text-success"></i>Print2</a><br>-->
                                       <!--<a href="{{url('noc_print')}}/{{$item->id}}" target="blank" class="text-primary"><i class="fa fa-print text-primary"></i> NOC</a>-->
                                        </ul>
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
      
    </section>
    
  </div>
    
</div>
   
 
<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 

  $('#delete_id').val(delete_id); 
  } );
 </script>

 The Modal 
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

       Modal Header 
      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

       Modal body 
      <form action="{{ url('tc_delete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              
            
            
              <input type="hidden" id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('common.Are you sure you want to delete') }} ?</h5>
           
      </div>

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
  
@endsection
   
   