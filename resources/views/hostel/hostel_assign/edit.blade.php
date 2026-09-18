@php
$getHostel = Helper::getHostel();
$getHostelBuilding = Helper::getHostelBuildingAll();
$getHostelFloor = Helper::getHostelFloor();
$getHostelRoom = Helper::getHostelRoom();
$getHostelBed = Helper::getHostelBed();
$classType = Helper::classType();
$getStudent = Helper::getStudent();
$getgenders = Helper::getgender();
$getSetting=Helper::getSetting();
@endphp
@extends('layout.app')
@section('content')
<style>
	.select2-container .select2-selection--single {
		height: 38px !important;
	}

	.select2-container--default .select2-selection--single .select2-selection__arrow {
		height: 38px !important;
	}
</style>
<link rel="stylesheet" href="{{ asset('public/assets/school/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/school/css/select2-bootstrap4.min.css') }}">
<script src="{{URL::asset('public/assets/school/js/select2.full.min.js')}}"></script>



<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('hostel.Hostel Assign Edit') }} </h3>
							<div class="card-tools">
								<!--<a href="{{url('admissionView')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{ __('messages.View') }} </a>-->
								<a href="{{url('assign_student_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{ __('messages.View') }} </a>
								<a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
							</div>

						</div>

						

						<div class="student_list_show"></div>

						<form id="quickForm" action="{{ url('hostel_student_edit') }}/{{$admission['id']}}" method="post" enctype="multipart/form-data">
							@csrf

							<div class="row m-2 mt-3">
								<div class=" col-md-12 title mt-n3">
									<h5 class="text-danger">{{ __('hostel.Personal Details') }}:-</h5>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Admission No.') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control" id="admission_no" name="admission_no" placeholder="{{ __('hostel.Admission No.') }}" readonly value="{{$admission['admissionNo'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.First Name') }}<span style="color:red;">*</span></label>
										<input type="text" name="first_name" id="first_name" class="form-control invalid " value="{{ $admission['first_name'] ?? '' }}" placeholder="{{ __('common.First Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)" required>
										<span class="invalid-feedback" id="first_name_invalid" role="alert">
                                            <strong>{{ __('hostel.First name required') }}</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Last Name') }}<span style="color:red;">*</span></label>
										<input type="text" name="last_name" id="last_name" class="form-control invalid " value="{{ $admission['last_name'] ?? '' }}" placeholder="{{ __('common.Last Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)" required>
										<span class="invalid-feedback" id="last_name_invalid" role="alert">
                                            <strong>{{ __('hostel.Last name required') }}</strong>
                                        </span>
									</div>
								</div>
							
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Gender') }}</label>
										<select class="form-control " id="gender_id" name="gender_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getgenders))
											@foreach($getgenders as $value)
											<option value="{{ $value->id}}" {{ ( $value->id == $admission['gender_id'] ? 'selected' : '' ) }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									
									</div>
								</div>
							
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Mobile No.') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{ $admission['mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
						              
                                        
						                <span class="invalid-feedback" id="mobile_invalid" role="alert">
                                            <strong>{{ __('hostel.Mobile required') }}</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Email') }}</label>
										<input type="email" class="form-control " id="email" name="email" placeholder="{{ __('common.Email') }}" value="{{ $admission['email'] ?? '' }}">
							          
                                        
							            
									</div>
								</div>
								
								  <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.DOB') }}</label>
										<input type="date" class="form-control" id="dob" name="dob" value="{{ $admission['dob'] ?? '' }}">
									
									</div>
								</div>
								   <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Date Of Admission') }}</label>
										<input type="date" class="form-control " id="admission_date" name="admission_date" value="{{$hostel['date']}}">
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.College Name') }}</label>
										<input type="text" class="form-control" id="college" name="college" placeholder="{{ __('hostel.College Name') }}" value="{{$hostel['college']}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Course Name') }}</label>
										<input type="text" class="form-control" id="Course" name="Course" placeholder="{{ __('hostel.Course Name') }}" value="{{$hostel['Course']}}">
									</div>
								</div>
								<div class="col-md-2">
									<lable>{{ __('hostel.Student Photo') }}</lable>
									<div class="input file form-control">
										<input type="file" class="" name="student_img" id="student_img" value="{{old('student_img')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
								
									</div>
								</div>
								<div class="col-md-1">
								   
								<img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$admission['image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								
									<div class="col-md-3">
									<lable>{{ __('hostel.Student Signature') }}</lable>
									<div class="input file form-control">
										<input type="file" class="" name="Student_Signature_img" id="Student_Signature_img" value="{{old('Student_Signature_img')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
										
                                  
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'hostel/Student_Signature_img/'.$hostel['Student_Signature_img'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								
									<div class="col-md-12">
									<div class="form-group">
										<label>{{ __('common.Address') }}</label>
										<textarea type="text" class="form-control " id="address" name="address" placeholder="{{ __('common.Address') }}" value="{{old('address')}}">{{$admission['address']}}</textarea>
								
									
									</div>
								</div>
									<div class="col-md-2">
									<label style="color:red;">{{ __('hostel.Select Hostel') }}*</label>
									<select class="form-control @error('hostel_id') is-invalid @enderror" id="hostel_id" name="hostel_id">
										@if(!empty($getHostel))
										@foreach($getHostel as $type)
										<option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $hostel['hostel_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									
								</div>
								<div class="col-md-2">
									<label style="color:red;">{{ __('hostel.Select Building') }}*</label>
									<select class="form-control @error('building_id') is-invalid @enderror building_id" id="building_id" name="building_id">
										@if(!empty($getHostelBuilding))
										@foreach($getHostelBuilding as $type)
										<option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $hostel['building_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									
								</div>
								<div class="col-md-2">
									<label style="color:red;">{{ __('hostel.Select Floor') }}*</label>
									<select class="form-control @error('floor_id') is-invalid @enderror floor_id" id="floor_id" name="floor_id">
										@if(!empty($getHostelFloor))
										@foreach($getHostelFloor as $type)
										<option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $hostel['floor_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									
								</div>
								<div class="col-md-2">
									<label style="color:red;">{{ __('hostel.Select Room') }}*</label>
									<select class="form-control @error('room_id') is-invalid @enderror room_id" id="room_id" name="room_id">
										@if(!empty($getHostelRoom))
										@foreach($getHostelRoom as $type)
										<option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $hostel['room_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									
								</div>
								<div class="col-md-2">
									<label style="color:red;">{{ __('hostel.Select Bed') }}*</label>
									<select class="form-control @error('bed_id') is-invalid @enderror bed_id" id="bed_id" name="bed_id">
										@if(!empty($getHostelBed))
										@foreach($getHostelBed as $type)
										<option value="{{ $type->id ?? ''  }}" {{ ( $type['id'] == $hostel['bed_id']) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
										@endforeach
										@endif
									</select>
									
								</div>
								</div>
								<div class="row m-1">
							    <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Parents Details') }}:-</h5>
								</div>	
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Father/Guardian Name') }}</label>
										<input type="text" class="form-control " id="father_name" name="father_name" placeholder="{{ __('hostel.Father/Guardian Name') }}" value="{{ $admission['father_name'] ?? ''}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
						<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Father Contact No') }}</label>
										<input type="text" class="form-control " id="father_mobile" name="father_mobile" placeholder="{{ __('hostel.Father Contact No') }}" value="{{ $admission['father_mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
								
									</div>
								</div>
							<div class="col-md-3">
									<lable>{{ __('hostel.Father Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_photo" id="father_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								
							
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'father_photo/'.$admission['father_img'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Father Signature') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_Signature" id="father_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'hostel/father_Signature/'.$hostel['father_Signature'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
					
                            </div>
                            
                            	<div class="row m-1">
                            	 <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Mothers Name') }}</label>

										<input type="text" class="form-control " id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{ $admission['mother_name'] ?? ''}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
						
								<div class="col-md-2">
									
										<label>{{ __('common.Mother Contact No') }}</label>
											<div class="input file form-group">
											<input type="text" class="form-control " id="mothers_mobile" name="mothers_mobile" placeholder="{{ __('common.Mother Contact No') }}" value="{{$hostel['mothers_mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									</div>
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Mother Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="mother_photo" id="mother_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'mother_photo/'.$admission['mother_img'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Mother Signature') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="mother_Signature" id="mother_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'hostel/mother_Signature/'.$hostel['mother_Signature'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								</div>
								
								
								<div class="row m-2">
							    <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Local Guardian Details') }}:-</h5>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Name') }}</label>
										<input type="text" class="form-control " id="guardian_name" name="guardian_name" placeholder="{{ __('hostel.Guardian Name') }}" value="{{ $hostel['guardian_name'] ?? '' }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Mobile') }}</label>
										<input type="text" class="form-control" id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('hostel.Guardian Mobile') }}" value="{{ $hostel['guardian_mobile'] ?? '' }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Telephone') }}</label>
										<input type="text" class="form-control" id="guardian_tel" name="guardian_tel" placeholder="{{ __('hostel.Guardian Telephone') }}" value="{{ $hostel['guardian_tel'] ?? '' }}"  onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
										<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Whatsapp') }}</label>
										<input type="text" class="form-control" id="guardian_whatsapp" name="guardian_whatsapp" placeholder="{{ __('hostel.Guardian Whatsapp') }}" value="{{ $hostel['guardian_whatsapp'] ?? '' }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									
									
									</div>
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Guardian Photo') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="guardian_photo" id="guardian_photo" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'hostel/guardian_photo/'.$hostel['guardian_photo'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
									<div class="col-md-3">
									<lable>{{ __('hostel.Guardian Signature') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="guardian_Signature" id="guardian_Signature" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'hostel/guardian_Signature/'.$hostel['guardian_Signature'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								
								
									<div class="col-md-12">
									<div class="form-group">
										<label>{{ __('hostel.Guardian Address') }}</label>
										<textarea type="text" class="form-control " id="guardian_address" name="guardian_address" placeholder="{{ __('hostel.Guardian Address') }}" value="{{$hostel['guardian_address'] ?? ''}}">{{ $hostel['guardian_address'] ?? '' }}</textarea>
										
									</div>
								</div>
								
								</div>
							<hr>
							<div class="row m-1">
							 <div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Hostel Room Preference') }}:-</h5>
								</div>
								<div class="col-md-3 amount" style="">
                            		<label>{{ __('hostel.Hostel Fees') }}<span class="text-danger">*</span></label>
                        				<input type="text" class="form-control invalid" id="hostel_fees" name="hostel_fees" placeholder="{{ __('hostel.Hostel Fees') }}" value="{{$hostel['hostel_fees'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                                     <span class="invalid-feedback" id="hostel_fees_invalid" role="alert">
                                            <strong>{{ __('hostel.Hostel Fees required') }}</strong>
                                         </span> 
                                </div>
								<div class="col-md-2">
								
										<label>{{ __('hostel.Duration Of Stay') }}</label>
											<div class="input file form-group">
										<input type="text" class="form-control" id="duration_stay" name="duration_Of_stay" placeholder="Duration Of stay" value="{{$hostel['duration_Of_stay'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>{{ __('hostel.Hostel Room Preference') }}</label>
    									<select class="form-control" id="room_reference" name="room_reference">
                                        <option value="">{{__('common.Select') }}</option>
                                        <option value="Single" {{ ('Single' == $hostel['room_reference']) ? 'selected' : '' }}>Single</option>
                                        <option value="Double" {{ ('Double' == $hostel['room_reference']) ? 'selected' : '' }}>Double</option>
                                        <option value="Triple" {{ ('Triple' == $hostel['room_reference']) ? 'selected' : '' }}>Triple</option>
                                    </select>

									</div>
								</div>
								 </div>
						
							<div class="row m-2">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('hostel.Document Upload') }}:-</h5>
								</div>
								
						
									<div class="col-md-3">
									<lable>{{ __('hostel.Student ID Proof') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="student_id_proof" id="student_id_proof" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/student_id_proof/'.$hostel['student_id_proof'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.College Id') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="college_id" id="college_id" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/college_id/'.$hostel['college_id'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Police Verification') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="police_verification" id="police_verification" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/police_verification/'.$hostel['police_verification'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Covid Certificate') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="covid_certificate" id="covid_certificate" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/covid_certificate/'.$hostel['covid_certificate'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Father Aadhaar') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="father_adhar" id="father_adhar" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
									<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/father_adhar/'.$hostel['father_adhar'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable>{{ __('hostel.Other Document') }}</lable>
									<div class="input file form-group">
										<input type="file" class="form-control" name="other_document" id="other_document" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
								<img src="{{ env('IMAGE_SHOW_PATH').'hostel/other_document/'.$hostel['other_document'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
							</div>


					
							<hr>
							<div class="mesterClassAmt" class="row m-2"></div>
						
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-primary " id="uniqueSubmit">{{ __('common.submit') }}</button><br><br>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>


<script>
	function studentName(student) {

		var $student = $(
			'<span><span></span></span>'
		);

		$student.find("span").text(student.text);

		return $student;
	};

	$(".select2").select2({
		templateSelection: studentName
	});
</script>
<script>
	$('#hostel_id').on('change', function(e) {
		var basurl = "{{ url('/') }}";
		var hostel_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: basurl + '/hostelData/' + hostel_id,
			success: function(data) {
				if (data != '') {
					$(".building_id").html(data);
				} else {
					$(".building_id").html(data);
					alert('Building Not Found');
				}
			}
		});
	});

	$('#building_id').on('change', function(e) {
		var basurl = "{{ url('/') }}";
		var building_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: basurl + '/BuildingData/' + building_id,
			success: function(data) {
				if (data != '') {
					$(".floor_id").html(data);
				} else {
					$(".floor_id").html(data);
					alert('Floor Not Found');
				}
			}
		});
	});

	$('#floor_id').on('change', function(e) {
		var basurl = "{{ url('/') }}";
		var floor_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: basurl + '/FloorData/' + floor_id,
			success: function(data) {
				if (data != '') {
					$(".room_id").html(data);
				} else {
					$(".room_id").html(data);
					alert('Room Not Found');
				}
			}
		});
	});

	$('#room_id').on('change', function(e) {
		var basurl = "{{ url('/') }}";
		var room_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: basurl + '/RoomData/' + room_id,
			success: function(data) {
				if (data != '') {
					$(".bed_id").html(data);
				} else {
					$(".bed_id").html(data);
					alert('Bed Not Found');
				}
			}
		});
	});

	$('#class_type_id').on('change', function(e) {
		var basurl = "{{ url('/') }}";
		var class_type_id = $(this).val();
		$.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: basurl + '/StudentsData/' + class_type_id,
			success: function(data) {
				if (data != '') {
					$(".admissionNo").html(data);
				} else {
					$(".admissionNo").html(data);
					alert('Student Not Found');
				}
			}
		});
	});
</script>
<script>
    	$('#uniqueSubmit').click(function(e){
    e.preventDefault();
    var all_filled_up = 2000;
    $('.invalid').each(function(){
        var this_value = $(this).val();
        var this_id = $(this).attr('id'); 
        if(this_value == ''){
            $('#' + this_id + '_invalid').css('display','block');
            all_filled_up = all_filled_up + ' && ' + this_value;
        }else{
            $('#' + this_id + '_invalid').css('display','none');
        }
    })
    if(all_filled_up == 2000){
        $('#quickForm').trigger('submit');
    }else{
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }
})
</script>
@endsection