@php

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
							<h3 class="card-title"><i class="fa fa-users"></i> &nbsp; {{ __('hostel.Hostel Room Unassigned') }}</h3>
							<div class="card-tools"> 
							    <!--<a href="{{url('meter_unit')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>View </a> -->
							    <a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                             
                            <div class="row m-2">
                                <div class="col-md-2">
                        			<label>{{ __('hostel.Select Student') }}<font style="color:red"><b>*</b></font></label>
                        <form id="studentDetailsForm" method="post" action="{{ url('hostel_unassign') }}">
                           @csrf
                           <select name="student_details" id="student_details" class="form-control select2 ">
                              <option value="">{{__('common.Select') }}</option>

                              @if(!empty($allstudents))
                              @foreach($allstudents as $value)
                              <option value="{{ $value->id }}" {{ ( $value->id == $search['student_details'] ?? '' ) ? 'selected' : '' }}>{{ $value->first_name ?? ''}} {{ $value->last_name ?? ''}}</option>
                              @endforeach
                              @endif
                           </select>
                        </form>
                            	</div>    
                               
                               
                                
                            </div> 
                       
                      
                      
                              <div class="row m-2">

                                <div class="col-md-12">
                                    
                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead class="bg-primary">
                                            <tr role="row">
                                                <th>{{ __('common.SR.NO') }}</th> 
                                                <th>{{ __('common.Name') }}</th> 
                                                <th>{{ __('common.Mobile') }}</th> 
                                               
                                                <th>{{ __('hostel.Hostel') }}</th>
                                                <th>{{ __('hostel.Building') }}</th>
                                                <th>{{ __('hostel.Floor') }}</th>
                                                <th>{{ __('hostel.Room') }}</th>                                           
                                                <th>{{ __('hostel.Bed') }}</th>                                           
                                                <th>{{ __('common.Fathers Name') }}</th>                                           
                                                <th>{{ __('hostel.Assign Date') }}</th>                                           
                                                <th>{{ __('common.Action') }}</th>                                           
                                               
                                            </tr>
                                        </thead>
                                        <tbody id="student_list_show">
                            
                                            @if(!empty($data))
                                            @foreach($data as $key =>$item)
                                            <tr>
                                                 <td>{{$key+1 }}</td>  
                                                <td>{{$item->first_name ?? ''}} {{$item->last_name ?? ''}}</td>
                                                <td>{{$item->mobile ?? ''}}</td>
                                                <td>{{$item->hostel_name ?? ''}}</td>
                                                <td>{{$item->building_name ?? ''}}</td>
                                                <td>{{$item->floor_name ?? ''}}</td>
                                                <td>{{$item->room_name ?? ''}}</td>
                                                <td>{{$item->bad_name ?? ''}}</td>
                                                <td>{{$item->father_name ?? ''}}</td>
                                                <td>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</td>
                                                <td>
                                                    
                                                    <form action="{{url('change_assign_status')}}" method="post">
                                                        @csrf
                                                        <input type="hidden" value="{{$item->bed_status ?? ''}}" name="status" />
                                                    <input type="hidden" value="{{$item->first_name ?? ''}}" name="first_name" />
                                                        <input type="hidden" value="{{$item->id ?? ''}}" name="hostel_assign_id" />
                                                        <button type="submit" class="btn btn-{{ $item->bed_status == 1 ? 'primary' : 'success'}}" >{{ $item->bed_status == 1 ? 'Unassign' : 'Assign'}}</button>
                                                    </form>
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
			</div>
		</div>
	</section>
</div>



<script>

 

       $(document).ready(function() {

      $('#student_details').change(function() {
         var student_details = $(this).val();
         if (student_details != '') {
            $('#studentDetailsForm').trigger('submit');
         } else {
            window.location.href = 'fees';
         }
      })
   })

    </script>
                        

@endsection      