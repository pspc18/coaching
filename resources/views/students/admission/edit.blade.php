@php
$getstudents = Helper::getstudents();
$getgenders = Helper::getgender();
$classType = Helper::classType();
$getCountry = Helper::getCountry();
$bloodGroupType = Helper::bloodGroupType();
$list = DB::table('custom_villages_list')->orderBy('name','ASC')->whereNull('deleted_at')->get();
@endphp

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row"> 
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						
						<form id="form-admission-edit" action="{{ url('admissionEdit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="row m-4">
								<div class=" col-md-12 title mt-n3">
									<h5 class="text-danger">{{ __('messages.Personal Details') }}:-</h5>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Admission No.') }}</label>
										<input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Admission No" value="{{$data['admissionNo']}}" onkeypress="javascript:return isNumber(event)">
									   
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Ledger No') }}<span style=""></span></label>
										<input type="text" class="form-control " name="ledger_no" placeholder="{{ __('Ledger No') }}"  value="{{$data['ledger_no']}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Student Pen</label>
										<input type="text" class="form-control" id="student_pen" name="student_pen" placeholder="Student Pen" value="{{$data['student_pen']}}" onkeypress="javascript:return isNumber(event)" >
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Apaar Id</label>
										<input type="text" class="form-control" id="apaar_id" name="apaar_id" placeholder="Apaar Id" value="{{$data['apaar_id']}}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
							
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Family ID') }}</label>
										<input type="text" class="form-control" id="family_id" name="family_id" value="{{ $data['family_id'] ?? '' }}" placeholder="{{ __('Family ID') }}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Student Name') }}<span style="color:red;">*</span></label>
										<input type="text" name="first_name" id="first_name" class="form-control invalid" value="{{ $data['first_name'] ?? '' }}" placeholder="{{ __('Student Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
								<!--<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Last Name') }}</label>
										<input type="text" name="last_name" id="last_name" class="form-control" value="{{ $data['last_name'] ?? '' }}" placeholder="{{ __('common.Last Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									</div>
								</div>-->
							
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Aadhaar No.') }}</label>
										<input type="text" class="form-control" id="aadhaar" name="aadhaar" placeholder=" {{ __('common.Aadhaar No.') }}" value="{{ $data['aadhaar'] ?? '' }}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Jan Aadhaar No.') }}</label>
										<input type="text" class="form-control" id="jan_aadhaar" name="jan_aadhaar" placeholder=" {{ __('Jan Aadhaar No.') }}" value="{{ $data['jan_aadhaar'] ?? '' }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Gender') }}<span style="color:red;">*</span></label>
										<select class="form-control invalid" id="gender_id" name="gender_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getgenders))
											@foreach($getgenders as $value)
											<option value="{{ $value->id}}" {{ ( $value->id == $data['gender_id'] ? 'selected' : '' ) }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Date Of  Birth') }}<span style="color:red;">*</span></label>
										<input type="date" class="form-control invalid" id="dob" name="dob" placeholder=" Date Of  Birth" value="{{$data['dob'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Mobile No.') }}</label>
										<!--<input type="text" class="form-control " id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{ $data['mobile'] ?? ''}}"minlength="10" maxlength="10" onkeypress="javascript:return isNumber(event)">-->
						                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{ $data['mobile'] ?? '' }}" maxlength="10">
                                            <div id="mobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>

									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.E-Mail') }}</label>
										<input type="email" class="form-control " id="email" name="email" placeholder="{{ __('common.E-Mail') }}" value="{{ $data['email'] ?? '' }}">
							          
									</div>
								</div>
								


								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Class') }}<span style="color:red;">*</span></label>

										<select class="form-control invalid" id="class_type_id" name="class_type_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($classType))
											@foreach($classType as $type)
											<option value="{{ $type->id ?? ''  }}" data-orderBy="{{ $type->orderBy ?? ''  }}"  {{ ( $type->id == $data['class_type_id'] ? 'selected' : '' ) }} >{{ $type->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
										
									</div>
								</div>
								
								@php
								    $streamSubjects = Helper::getStreamSubjects($data->class_type_id ?? '');
								@endphp
								
								<div class="col-md-2" id="stream_subject_div" style="display:{{ $data['stream_subject'] != "" ? 'block' : 'none' }}">
									<div class="form-group">
										<label>Stream Subject<span style="color:red;">*</span></label>

										<select class="form-control select2" multiple id="stream_subject" name="stream_subject[]">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($streamSubjects))
                                                @foreach($streamSubjects as $subject)
                                                    <option value="{{ $subject->id ?? '' }}" {{ in_array($subject->id, explode(',', $data->stream_subject)) ? 'selected' : ''  }}>{{ $subject->name ?? '' }}</option>
                                                @endforeach
                                            @endif
										</select>
									</div>
								</div>
								
								
								<div class="col-md-2">
									<div class="form-group">
										<label>Admission Type(Non RTE)</label>
										<select class="form-control" id="admission_type_id" name="admission_type_id">
											<option value="1" {{ (1 == $data['admission_type_id'] ? 'selected' : '' ) }}>Yes</option>
											<option value="2" {{ (2 == $data['admission_type_id'] ? 'selected' : '' ) }}>No</option>
										</select>
										
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Religion') }}</label>
										<select class="form-control" id="religion" name="religion">
											<option value="">{{ __('common.Select') }}</option>
											<option value="Hindu" {{ ('Hindu' == $data['religion'] ? 'selected' : '' ) }}>Hindu</option>
											<option value="Islam" {{ ('Islam' == $data['religion'] ? 'selected' : '' ) }}>Islam</option>
											<option value="Sikh" {{ ('Sikh' == $data['religion'] ? 'selected' : '' ) }}>Sikh</option>
											<option value="Buddhism" {{ ('Buddhism' == $data['religion'] ? 'selected' : '' ) }}>Buddhism</option>
											<option value="Adivasi" {{ ('Adivasi' == $data['religion'] ? 'selected' : '' ) }}>Adivasi</option>
											<option value="Jain" {{ ('Jain' == $data['religion'] ? 'selected' : '' ) }}>Jain</option>
											<option value="Christianity" {{ ('Christianity' == $data['religion'] ? 'selected' : '' ) }}>Christianity</option>
											<option value="Other" {{ ('Other' == $data['religion'] ? 'selected' : '' ) }}>Other</option>
										</select>
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Category') }}</label>
										<select class="form-control" id="category" name="category">
											<option value="">{{ __('common.Select') }}</option>
											<option value="OBC" {{ ('OBC' == $data['category'] ? 'selected' : '' ) }}>OBC</option>
											<option value="ST" {{ ('ST' == $data['category'] ? 'selected' : '' ) }}>ST</option>
											<option value="SC" {{ ('SC' == $data['category'] ? 'selected' : '' ) }}>SC</option>
											<option value="BC" {{ ('BC' == $data['category'] ? 'selected' : '' ) }}>BC</option>
											<option value="GEN" {{ ('GEN' == $data['category'] ? 'selected' : '' ) }}>GEN</option>
											<option value="SBC" {{ ('SBC' == $data['category'] ? 'selected' : '' ) }}>SBC</option>
											<option value="Other" {{ ('Other' == $data['category'] ? 'selected' : '' ) }}>Other</option>
										</select>
										
									</div>
								</div>
								
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Caste') }}</label>
										<input type="text" class="form-control" id="caste_category" name="caste_category" placeholder="{{ __('Caste') }}" value="{{ $data['caste_category'] ?? ''}}" >
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Blood Group') }}</label>
										<select class="form-control" id="blood_group" name="blood_group">
											<option value="">{{ __('common.Select') }}</option>
        										@if(!empty($bloodGroupType))
        											@foreach($bloodGroupType as $bloodtype)
        											<option value="{{ $bloodtype->name ?? ''  }}" {{ ( $bloodtype->name == $data['blood_group'] ? 'selected' : '' ) }}>{{ $bloodtype->name ?? ''  }}</option>
        											@endforeach
        										@endif
										</select>
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Medium</label>
										<select class="form-control" id="medium" name="medium">
											<option value="">Select</option>
											<option value="Hindi" {{ ('Hindi' == $data['medium'] ? 'selected' : '' ) }}>Hindi</option>
											<option value="English" {{ ('English' == $data['medium'] ? 'selected' : '' ) }}>English</option>
										</select>
									</div>
								</div>
                                <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Date Of Admission') }}</label>
										<input type="date" class="form-control" id="admission_date" name="admission_date" value="{{ $data['admission_date'] == '1970-01-01' ? '' : $data['admission_date'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Country') }}</label>
										<select class="form-control" name="country" id="country_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getCountry))
											@foreach($getCountry as $country)
											<option value="{{ $country->id ?? ''  }}" {{ ( $country->id == $data['country_id'] ? 'selected' : '' ) }} >{{ $country->name ?? ''  }}</option>
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
											<option value="{{ $state->id ?? ''}}" {{ ( $state->id == $data['state_id'] ? 'selected' : '' ) }} >{{ $state->name ?? ''}}</option>
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
											<option value="{{ $cities->id ?? ''  }}" {{ ( $cities->id == $data['city_id'] ? 'selected' : '' ) }} >{{ $cities->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								<!--	<div class="col-md-2">-->
								<!--	<div class="form-group">-->
								<!--		<label>{{ __('student.Village/City') }}</label>-->
								<!--		<select class="form-control select2 " id="village_city" name="village_city">-->
								<!--			<option value="">{{ __('common.Select') }}</option>-->
								<!--			@if(!empty($list))-->
								<!--			@foreach($list as $type)-->
								<!--			<option value="{{ $type->name ?? ''  }}" {{ ( $type->name == $data['village_city'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>-->
								<!--			@endforeach-->
								<!--			@endif-->
								<!--		</select>-->
								<!--	</div>-->
								<!--</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('messages.Village/City') }}</label>
										<input type="text" class="form-control" id="village_city" name="village_city" placeholder="{{ __('messages.Village/City') }}" value="{{ $data['village_city'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Students Address') }}</label>
										<input type="text" class="form-control " id="address" name="address" placeholder="{{ __('student.Students Address') }}" value="{{ $data['address'] ?? ''}}">
										
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Family Annual Income') }}</label>
										<input type="text" name="family_annual_income" id="family_annual_income" class="form-control" value="{{ $data['family_annual_income'] ?? ''}}" placeholder="{{ __('Family Annual Income') }}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Relation With The Student') }}</label>
										<input type="text" name="relation_student" id="relation_student" class="form-control" value="{{ $data['relation_student'] ?? ''}}" placeholder="{{ __('Relation With The Student') }}">
										
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('School Studied Last Year') }}</label>
										<input type="text" name="school_namestudied_last_year" id="school_namestudied_last_year" class="form-control" value="{{ $data['school_namestudied_last_year'] ?? ''}}" placeholder="{{ __('School Studied Last Year') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('House') }}</label>
										<input type="text" name="house" id="house" class="form-control" value="{{ $data['house'] ?? ''}}" placeholder="{{ __('House') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Height') }}</label>
										<input type="text" name="height" id="height" class="form-control" value="{{ $data['height'] ?? ''}}" placeholder="{{ __('Height') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Weight') }}</label>
										<input type="txt" name="weight" id="weight" class="form-control" value="{{ $data['weight'] ?? ''}}" placeholder="{{ __('Weight') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('messages.Pin Code') }}</label>
										<input type="text" class="form-control" id="pincode" name="pincode" placeholder="{{ __('messages.Pin Code') }}" value="{{ $data['pincode'] ?? ''}}" maxlength="6" onkeypress="javascript:return isNumber(event)">
										
									</div>
								</div>
                                
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Remark') }} </label>
										<input type="text" class="form-control" id="remark_1" name="remark_1" placeholder="{{ __('student.Remark') }} " value="{{ $data['remark_1'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bus Number') }} </label>
										<input type="text" class="form-control" id="bus_number" name="bus_number" placeholder="{{ __('Bus Number') }} " value="{{ $data['bus_number'] ?? ''}} ">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bus Route') }} </label>
										<input type="text" class="form-control" id="bus_route" name="bus_route" placeholder="{{ __('Bus Route') }} " value="{{$data['bus_route'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Stoppage') }} </label>
										<input type="text" class="form-control" id="stoppage" name="stoppage" placeholder="{{ __('Stoppage') }} " value="{{$data['stoppage'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Transpor Charges') }} </label>
										<input type="text" class="form-control" id="transpor_charges" name="transpor_charges" placeholder="{{ __('Transpor Charges') }} " value="{{$data['transpor_charges'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bank Name') }} </label>
										<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="{{ __('Bank Name') }} " value="{{$data['bank_name'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bank Account') }} </label>
										<input type="text" class="form-control" id="bank_account" name="bank_account" placeholder="{{ __('Bank Account') }} " value="{{$data['bank_account'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Branch Name') }} </label>
										<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="{{ __('Branch Name') }} " value="{{$data['branch_name'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('IFSC') }} </label>
										<input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="{{ __('IFSC') }} " value="{{$data['ifsc'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __(' MICR Code') }} </label>
										<input type="text" class="form-control" id="micr_code" name="micr_code" placeholder="{{ __('MICR Code') }} " value="{{$data['micr_code'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Bank Account Holder</label>
										<input type="text" class="form-control" id="bank_account_holder" name="bank_account_holder" placeholder="Bank Account Holder" value="{{ $data['bank_account_holder'] ?? ''}}">
									</div>
								</div>
							
								
								<div class="col-md-2">
									<div class="form-group">
										<label>District</label>
										<input type="text" class="form-control" id="district" name="district" placeholder="District" value="{{ $data['district'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Tehsil</label>
										<input type="text" class="form-control" id="tehsil" name="tehsil" placeholder="Tehsil" value="{{$data['tehsil'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Father's Pancard</label>
										<input type="text" class="form-control" id="father_pancard" name="father_pancard" placeholder="Father's Pancard" value="{{ $data['father_pancard'] ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Mother's Pancard</label>
										<input type="text" class="form-control" id="mother_pancard" name="mother_pancard" placeholder="Mother's Pancard" value="{{ $data['mother_pancard'] ?? ''}}">
									</div>
								</div>
							
								<div class="col-md-2">
									<div class="form-group">
										<label>BPL</label>
										<select class="form-control" id="bpl" name="bpl">
										    <option value="">Select</option>
										    <option value="Yes" {{ "Yes" == $data['bpl'] ? 'selected' : '' }}>Yes</option>
										    <option value="No" {{ "No" == $data['bpl'] ? 'selected' : '' }}>No</option>
										</select>
									</div> 
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>BPL Cetificate No.</label>
										<input type="text" class="form-control" id="bpl_certificate_no" name="bpl_certificate_no" placeholder="BPL Cetificate No." value="{{ $data['bpl_certificate_no'] ?? ''}}">
									</div>
								</div>
							<div class="col-md-4">
									<div class="form-group">
										<label>Name  And  Address Of Previous School</label>
										<input type="text" class="form-control" id="previous_school" name="previous_school" placeholder="Name  And  Address Of Previous School" value="{{ $data['previous_school'] ?? ''}}">
									</div>
								</div>
								
							</div>
							
							<div class="row m-2 ">
							    
							    </div>
							<div class="row m-2">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('Guardian Ditels') }}:-</h5>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Fathers Name') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{ $data['father_name'] ?? ''}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Fathers Contact No') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid" id="father_mobile" name="father_mobile" placeholder="{{ __('common.Fathers Contact No') }}" value="{{ $data['father_mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
						                 <div id="fathermobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>

								
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Fathers Aadhaar') }}</label>
										<input type="text" class="form-control" id="father_aadhaar" name="father_aadhaar" placeholder="{{ __('Fathers Aadhaar') }}" value="{{$data['father_aadhaar'] ?? ''}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Father Occupation</label>
										<input type="text" class="form-control" id="father_occupation" name="father_occupation" placeholder="Father Occupation" value="{{old('father_occupation')}}">
									</div>
								</div>
                                
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Mothers Name') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid" id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{ $data['mother_name'] ?? ''}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Mother Mobile No') }}</label>
										<input type="text" class="form-control" id="mother_mob" name="mother_mob" placeholder="{{ __('Mother Mobile No') }}" value="{{$data['mother_mob'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Mothers Aadhaar') }}</label>
										<input type="text" class="form-control" id="mother_aadhaar" name="mother_aadhaar" placeholder="{{ __('Mothers Aadhaar') }}" value="{{$data['mother_aadhaar'] ?? ''}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								
                                <div class="col-md-2">
									<div class="form-group">
										<label>Mother Occupation</label>
										<input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="{{$data['mother_occupation'] ?? ''}}">
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Guardian Name') }}</label>
										<input type="text" class="form-control" id="guardian_name" name="guardian_name" placeholder="{{ __('Guardian Name') }}" value="{{$data['guardian_name'] ?? ''}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Guardian Mobile No') }}</label>
										<input type="text" class="form-control " id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('Guardian Mobile No') }}" value="{{$data['guardian_mobile'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
								
									</div>
								</div>
							
								
							</div>
							<hr>
							<div class="row m-2">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('messages.Document Upload') }}:-</h5>
								</div>
								<div class="col-md-3">
									<lable>{{ __('student.Student Photo') }}</lable>
									<div class="input file form-control">
										<input type="file" class="" name="student_img" id="student_img" value="{{ $data['image'] ?? '' }}">
                                   
								    </div>
								</div>

								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable><b>{{ __('messages.Father Photo') }}</b></lable>
									<div class="input file form-control @error('father_img') is-invalid @enderror">
										<input type="file" name="father_img" id="father_img" value="{{ $data['father_img'] ?? '' }}">
										
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'father_image/'.$data['father_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable><b>{{ __('messages.Mother Photo') }}</b></lable>
									<div class="input file form-control @error('mother_img') is-invalid @enderror">
										<input type="file" name="mother_img" id="mother_img" value="{{ $data['mother_img'] ?? '' }}">
										 
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'mother_image/'.$data['mother_img'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
							</div>
							<hr>
							
							<div class="mesterClassAmt" class="row m-2"></div>
							<div class="col-md-12 text-center ">
							    <div >
								<button type="submit" class="btn btn-primary btn-submit"   id="is-invalid" >{{ __('messages.Update') }}</button><br><br>
							</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>






<style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    #image_er{
        font-weight: bold;
    font-size: 14px;
    }
    #image_errors{
        font-weight: bold;
    font-size: 14px;
    }
</style>


<script>
$(document).ready(function(){
    var baseUrl = "{{ url('/') }}";

    // Initially hide/show based on selected
    toggleStream($('#class_type_id'));

    $('#class_type_id').change(function(){
        toggleStream($(this));
    });

    function toggleStream($el) {
        var orderBy = parseInt($el.find('option:selected').data('orderby')) || 0;
        var class_type_id = parseInt($el.val()) || 0;

        if(orderBy > 10){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: baseUrl + '/getStreamSubjects',
                data: { class_type_id: class_type_id },
                success: function(data) {
                    var options = "";
                    $.each(data, function(i, subject){
                        options += '<option value="'+ subject.id +'">'+ subject.name +'</option>';
                    });
                    $('#stream_subject').html(options);
                    $('#stream_subject_div').show();
                }
            });
        } else {
            $('#stream_subject').html("");
            $('#stream_subject_div').hide();
        }
    }
});
</script>



<script>
	$(function() {
		//Initialize Select2 Elements
		$('.select2').select2()

		//Initialize Select2 Elements
		$('.select2bs4').select2({
			theme: 'bootstrap4'
		})

	})
	
	$(document).on("submit", "#form-admission-edit", function (e) {
    e.preventDefault();
    var $form = $(this);
    var btn = $form.find(".btn-submit");
    var originalBtnText = btn.html(); // Save original button text
    var formData = new FormData(this);

    $.ajax({
        url: $form.attr("action"),
        type: "POST",
        data: formData,
        dataType: "json",
        contentType: false,
        processData: false,
        cache: false,
        beforeSend: function () {
            btn.prop("disabled", true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
            $(".error, .alert").remove(); // Clear old messages
            $(".is-invalid").removeClass("is-invalid");
        },
        success: function (response) {
            toastr.success(response.message);

            // Modal close karo
            $("#editStudentModal").modal("hide");
            $form[0].reset();
            btn.prop("disabled", false).html(originalBtnText);
        },
        error: function (xhr) {
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                $.each(xhr.responseJSON.errors, function (index, value) {
                    var inputField = $form.find("[name='" + index + "']");
                    if (inputField.length) {
                        inputField.addClass("is-invalid");
                        if (inputField.closest(".form-group").find(".error").length === 0) {
                            inputField.closest(".form-group").append("<div class='error invalid-feedback'></div>");
                        }
                        inputField.closest(".form-group").find(".error").text(value[0]);
                    }
                });
            } else {
                var errorMessage = xhr.responseJSON?.message || "An unexpected error occurred.";
                toastr.error(errorMessage);
            }

            btn.prop("disabled", false).html(originalBtnText);
        },
    });
});

	
</script>




