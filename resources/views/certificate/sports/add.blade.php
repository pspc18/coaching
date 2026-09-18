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
							<h3 class="card-title"><i class="fa fa-user"></i> &nbsp; Sport Certificate</h3>
							<div class="card-tools"><a href="{{url('sport/certificate/index')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->view ? '' : 'd-none' }}" ><i class="fa fa-eye"></i>{{ __('common.View') }}  </a>
							     <a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a> </div>
						</div>


                    <form id="quickForm" action="#" method="post" >
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
            			<select class="select2  form-control" id="class_type_id" name="class_type_id" >
            			<option value="">{{ __('common.Select') }}</option>
                         @if(!empty($classType)) 
                              @foreach($classType as $type)
                                 <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                              @endforeach
                          @endif
                        </select>
            	    </div>
            	</div>

                <div class="col-md-1 ">
                     <label for="">&nbsp;</label><br>
            	    <button type="button" class="btn btn-primary "onclick="SearchValue()">{{ __('common.Search') }}</button>
            	</div>
            			
            </div>
        </form>
    	    </form>
				</div>
			</div>
		</div>
	
        <div class="evente_list_show"></div>
        <div class="card m-2">
            <div class="card-body">
               <form id="quickForm" action="{{ url('sport/certificate/add') }}" method="post" >
                     @csrf
                <div class="row">
	                    <input type="hidden" name="class_type_id" id="class_type_id1" class="form-control"  value="{{ old('event_type') }}">
	                    <input type="hidden" name="father_mobile" id="father_mobile" class="form-control ">
                    <div class="col-md-2">
            			<div class="form-group">
            				<label style="color:red;">{{ __('certificate.Admission No.') }}*</label>
            				<input type="hidden" name="admission_id" id="admission_id"  class="form-control" placeholder="{{ __('certificate.Admission No.') }}" readonly="readonly" value="{{ old('admission_id') }}">
            				<input type="text" name="registrationNo" id="registration_no"  class="form-control" placeholder="{{ __('certificate.Admission No.') }}" readonly="readonly" value="{{ old('registrationNo') }}">
            		    </div>
            		</div>
            		
                    <div class="col-md-2">
			<div class="form-group">
				<label style="color:red;">{{ __('certificate.Student Name') }} *</label>
				<input type="text" name="name" id="first_name" class="form-control @error('name') is-invalid @enderror" readonly="readonly" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('certificate.Student Name') }}"  value="{{ old('name') }}">
                	@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
                    <div class="col-md-2">
			<div class="form-group">
				<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
				<input type="text" name="father_name" id="father_name" class="form-control @error('father_name') is-invalid @enderror" readonly="readonly" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('common.Fathers Name') }}"  value="{{ old('father_name') }}">
                	@error('father_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>

					
		<div class="col-md-2">
			<div class="form-group">
				<label>{{ __('certificate.Event Type') }} </label>
				<input type="text" name="event_type" id="event_type" class="form-control @error('event_type') is-invalid @enderror" placeholder="{{ __('certificate.Event Type') }}"  value="{{ old('event_type') }}">
                	@error('event_type')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
		<div class="col-md-2">
			<div class="form-group">
				<label style="color:red;">{{ __('Held On') }} *</label>
				<input type="date" name="organized_date" id="organized_date" class="form-control @error('organized_date') is-invalid @enderror"  value="{{ old('organized_date') }}">
                	@error('organized_date')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
             
        <div class="col-md-2">
			<div class="form-group">
				<label>{{ __('certificate.Rank') }} </label>
				<input type="text" name="rank" id="rank" class="form-control @error('rank') is-invalid @enderror" placeholder="{{ __('certificate.Rank') }}"  value="{{ old('rank') }}">
                	@error('rank')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
		
	
            </div>
        </div>
        <div class="row m-2">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">{{ __('common.Submit') }}</button>
            </div>
        </div>
        </form>
    </div>
</div>
</section>
</div>
    </section>
    </div>
<script>
        function SearchValue() {
            var class_type_id = $('#class_type_id :selected').val();
            var admission_id = $('#admission_id :selected').val();
            var country_id = $('#country_id :selected').val();
            var state_id = $('#state_id :selected').val();
            var city_id = $('#city_id :selected').val();
            var admissionNo = $('#admissionNo').val();
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/search_sport',
                data: {class_type_id:class_type_id,country_id:country_id,state_id:state_id,city_id:city_id,admissionNo:admissionNo},
                 //dataType: 'json',
                success: function (data) {
                    $('.evente_list_show').html(data);
                   
                }
              });
        };
    
         function showData(student_id) {
            alert("hello");
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: '/sport_add_click',
                data: {student_id : student_id},
                 dataType: 'json',
                success: function (data) {
                 alert(data);
                 if(data){
                $('#name').val(data.name);
                $('#organized_date').val(data.dob);
                $('#student_roll_no').val(data.roll_no);
                }else{
                     alert('No more records found');
                 }
                    
                }
              });
        };  

        </script>
@endsection