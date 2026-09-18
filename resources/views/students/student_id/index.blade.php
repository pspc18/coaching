@php
  $classType = Helper::classType();
  $getState = Helper::getState();
  $getCity = Helper::getCity();
  $getCountry = Helper::getCountry();
@endphp
@extends('layout.app') 
@section('content')


<div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
           
       <div class="col-md-12 pl-0">
            <div class="card card-outline card-orange ml-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-user"></i> &nbsp; {{__('student.View Students Id') }}</h3>
            <div class="card-tools">
            <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i><span class="Display_none_mobile">{{ __('common.Back') }}</span></a>
            </div>
            <div class="card-tools">
        </div>
            
            </div>
            <form id="searchForm" action="{{url('student/id/index')}}" method="post" >
            @csrf 
            <div class="row m-2">
				<div class="col-md-2">
					<div class="form-group">
						<label>{{ __('student.Admission No') }}</label>
						<input type="text" class="form-control"  id="admission_id"name="admission_id" placeholder="{{ __('Admission No') }}" value="{{ $search['admission_id'] ?? '' }}">
					</div>
				</div>
				<div class="col-md-2">
					<div class="form-group">
						<label>{{ __('common.Class') }}</label>
						<select class="form-control select2 " id="class_type_id" name="class_search_id">
							<option value="">{{ __('common.Select') }}</option>
							@if(!empty($classType))
							@foreach($classType as $type)
							<option value="{{ $type->id ?? ''  }}" {{ ($type->id == $search['class_search_id']) ? 'selected' : ''  }} >{{ $type->name ?? ''  }}</option>
							@endforeach
							@endif
						</select>
					</div>
				</div>

				<div class="col-md-2">
				    <lable>&nbsp;</lable><br>
				    <button class="btn btn-primary btn-xl">{{ __('common.Search') }}</button>
				</div>
            </div>
            </form>
            <form id="quickForm" action="{{url('student_id_print_multiple')}}" target="_blank" method="post" >
            @csrf 
                <div class="row ">
                    <div class="col-md-12">
                	</div>
                    <div class="col-md-12">
                       <table id="" class="table table-bordered table-striped dataTable dtr-inline ">
                          <thead class="bg-primary">
                          <tr role="row">
                      <th> <input type="checkbox" id="view1">{{ __('common.SR.NO') }}</th>
                       <th>{{ __('common.Name') }}</th>
                       <th>{{ __('Class') }}</th>
                      <th>{{ __('common.DOB') }}</th>
                      <th>{{ __('student.Ad. No') }}</th>
                    </tr>
                  </thead>
                  <tbody id="product_list_show">
                  
                  @if(!empty($data))
                        @php
                           $i=1;
                         
                        @endphp
                        @foreach ($data  as $item)
                       
                        <tr>
                            
                                <td>								    
                                <input type="checkbox"  data-value="view" name="checkbox[]" class="viewcheck @error('checkbox[]') is-invalid @enderror" value="{{$item->id ?? ''}}">
                                {{ $i++ }}</td>
                                <td>{{ $item['first_name']  ?? ''}} {{ $item['last_name'] ?? '' }}</td>
                                <td>{{ $item['ClassTypes']['name'] ?? '' }}</td>
                                <td>{{date('d-m-Y', strtotime($item['dob'])) ?? '' }}</td>
                                <td>{{ $item['admissionNo']  }}</td>
                                
                        </tr>
                   @endforeach
                @endif
                  </tbody>
            </table>
                	</div>
                </div>
                 @error('checkbox')
                   <span class="text-danger">
                       {{ $message }}
                   </span>
                 @enderror
                 @if(!empty($data))
                <div class="col-12 text-center mb-3 mt-2">
                    <button class="btn btn-primary" target="_blank">{{ __('student.Generate Ids') }}</button>
                </div>
                @endif
             </form>
            </div>  
            
        </div>
        
       
    </div>
</div>
</section>
</div>



     <!--<script src="{{URL::asset('public/assets/school/js/jquery.min.js')}}"></script>-->

<script>

    
        $("#view1").click(function(){
            if ($(this).is(':checked')) {
                $(".viewcheck").attr('checked', false);
                $(".viewcheck").attr('checked', true);
            }else{
                $(".viewcheck").attr('checked', false);
            }
        });
        function SearchValue() {
            var admission_no = $('#admission_no').val();
            var class_type_id = $('#class_type_id :selected').val();
            var country_id = $('#country_id :selected').val();
            var state_id = $('#state_id :selected').val();
            var city_id = $('#city_id :selected').val();
            if(class_type_id > 0 || country_id > 0 || state_id > 0 || city_id > 0 || admission_no != ''){
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/students_id_data',
                data: {class_type_id:class_type_id,country_id:country_id,state_id:state_id,city_id:city_id,admission_no:admission_no},
                 //dataType: 'json',
                success: function (data) {
                   
                  
                    $('#product_list_show').html(data);
                   
                }
              });
            }else{
                alert('Please put a value in minimum one column !');
            }              
        };

</script>

 @endsection      