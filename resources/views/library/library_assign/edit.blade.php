@php
$getgenders = Helper::getgender();
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
				<div class="col-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('library.Edit Library Student') }}</h3>
							<div class="card-tools">
								<a href="{{url('library_student_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
							</div>
						</div>
						<form id="quickForm" action="{{ url('library_student_edit') }}/{{ $admission['id'] ?? '' }}" method="post" enctype='multipart/form-data'>
							@csrf
							<div class="row m-2">
	                        <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Admission No.') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control" id="admission_no" name="admission_no" placeholder="{{ __('hostel.Admission No.') }}" readonly value="{{$admission['admissionNo'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2 ">
									<label style="color:red;">{{ __('common.First Name') }}*</label>
									<input type="text" class="form-control @error('first_name') is-invalid @enderror" placeholder="{{ __('common.First Name') }}" id="first_name" name="first_name" value="{{$admission['first_name'] ?? '' }}">
									@error('first_name')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								<div class="col-md-2">
									<label style="color:red;">{{ __('common.Gender') }}*</label>
									<select class="form-control" id="gender_id" name="gender_id">
										<option value="">{{ __('common.Select') }}</option>
										@if(!empty($getgenders))
										@foreach($getgenders as $value)
										<option value="{{ $value->id}}" {{ ($value->id == $admission['gender_id']) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									 @error('gender_id')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								<div class="col-md-2 ">
									<label style="color:red;">{{ __('common.Mobile No.') }}*</label>
									<input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" maxlength="10" onkeypress="javascript:return isNumber(event)" value="{{$admission['mobile'] ?? '' }}">
									@error('mobile')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								<div class="col-md-2 ">
									<label>{{ __('common.Email') }}</label>
									<input type="email" class="form-control" placeholder="{{ __('common.Email') }}" id="email" name="email" value="{{$admission['email'] ?? '' }}">
								
								</div>

								<div class="col-md-2 ">
									<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
									<input type="text" class="form-control @error('father_name') is-invalid @enderror" placeholder="{{ __('common.Fathers Name') }}" id="father_name" name="father_name" value="{{$admission['father_name'] ?? '' }}">
									@error('father_name')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
								
								<div class="col-md-2">
            						<div class="form-group">
            							<label>{{ __('common.Country') }}</label>
            							<select class="form-control" name="country" id="country_id">
            								<option value="">{{ __('common.Select') }}</option>
            								@if(!empty($getCountry))
            								@foreach($getCountry as $country)
            								<option value="{{ $country->id ?? ''  }}" {{ ($country->id == $admission['country_id']) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
            								@endforeach
            								@endif
            							</select>
            						</div>
            					</div>
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="State" class="required">{{ __('common.State') }}</label>
            							<select class="form-control stateId " id="state_id" name="state">
            								<option value="">{{ __('common.Select') }}</option>
            								@if(!empty($getState))
            								@foreach($getState as $state)
            								<option value="{{ $state->id ?? ''}}" {{ ($state->id == $admission['state_id'] ) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
            								@endforeach
            								@endif
            							</select>
            
            						</div>
            					</div>
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="City">{{ __('common.City') }}</label>
            							<select class="form-control cityId " name="city" id="city_id">
            								<option value="">{{ __('common.Select') }}</option>
            								@if(!empty($getCity))
            								@foreach($getCity as $cities)
            								<option value="{{ $cities->id ?? ''  }}" {{ ($cities->id == $admission['city_id'] ) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
            								@endforeach
            								@endif
            							</select>
            						</div>
            					</div> 
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="City">{{ __('Pincode') }}</label>
            							<input class="form-control" name="pincode" id="pincode" max-length="6" placeholder="pincode" value="{{$admission['pincode'] ?? '' }}">
            						</div>
            					</div> 
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="City" class="text-danger">Select ID Proof*</label>
            							<select class="form-control" id="id_proof" name="id_proof" autocomplete="off">
                                            <option value="">Select ID Proof</option>
                                            <option value="Aadhar Card" {{ ("Aadhar Card" == $admission['id_proof'] ) ? 'selected' : '' }}>Aadhar Card</option>
                                            <option value="Voter ID Card" {{ ("Voter ID Card" == $admission['id_proof'] ) ? 'selected' : '' }}>Voter ID Card</option>
                                            <option value="Driving License" {{ ("Driving License" == $admission['id_proof'] ) ? 'selected' : '' }}>Driving License</option>
                                            <option value="PAN Card" {{ ("PAN Card" == $admission['id_proof'] ) ? 'selected' : '' }}>PAN Card</option>
                                        </select>
            						</div>
            					</div> 
            					
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="id_number">ID Number</label>
            							<div class="input-group">
            							    <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-credit-card"></i>
                                                </span>
                                            </div>
            						        <input class="form-control" name="id_number" id="id_number" placeholder="ID Number" value="{{$admission['id_number'] ?? '' }}">
            							</div>
            						</div>
            					</div> 
            					
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="dob">Date of Birth</label>
            							<div class="input-group">
            							    <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                    <i class="fa fa-calendar"></i>
                                                </span>
                                            </div>
            						        <input type="date" class="form-control" name="dob" id="dob" value="{{$admission['dob'] ?? '' }}">
            							</div>
            						</div>
            					</div> 
            					<div class="col-md-2">
            						<div class="form-group">
            							<label for="dob">Status</label>
            						        <select class="form-control" id="status" name="status" title="Select User Status" >
												<option value="">Select Status</option>
												<option value="1" {{ ("1" == $library['status'] ) ? 'selected' : '' }}>Active</option>
												<option value="0" {{ ("0" == $library['status'] ) ? 'selected' : '' }}>Inactive</option>
											</select>
            						</div>
            					</div> 
            					<div class="col-md-12">
									<label style="color:red;"> {{ __('common.Address') }}*</label>
									<textarea type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="{{ __('common.Address') }}">{{$admission['address']}}</textarea>
                                    @error('address')
									<span class="invalid-feedback" role="alert">
										<strong>{{ $message }}</strong>
									</span>
									@enderror
								</div>
							</div>
							

							<div class="row m-2">
								<div class="col-md-12 text-center">
									<button type="submit" class="btn btn-primary">{{ __('common.Update') }} </button>
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
	$('#library_id').on('change', function(e) {
		var library_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: '/libraryData/' + library_id,
			success: function(data) {
				if (data != '') {
					$(".cabin_id").html(data);
				} else {
					$(".cabin_id").html(data);
					alert('Cabin Not Found');
				}
			}
		});
	});
</script>


@endsection