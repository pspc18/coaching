@php
$getstudents = Helper::getstudents();
$getgenders = Helper::getgender();
$classType = Helper::classType();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
$getSetting = Helper::getSetting();
$bloodGroupType = Helper::bloodGroupType();
@endphp


@php
    $studentCount = DB::table('admissions')->where('deleted_at',null)->count();
@endphp
						
<link rel="stylesheet" href="{{ asset('public/assets/school/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/assets/school/css/select2-bootstrap4.min.css') }}">
   <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<div class="content-wrapper">

<div class="header">
          <img src="{{env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting->left_logo}}" width='40px' alt="Company Logo">
        <a href="{{ url('logout') }}"><button class='btn btn-danger'>&larr;Back</button></a>
    </div>
		<div class="container">
			<div class="row">
				<div class="col-12 col-md-12">
						<form id="quickForm_addmission" action="{{ url('newStudentRegistration') }}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="row m-2">
								<div class=" col-md-12 title mt-n3">
							
									<h5 class="text-danger">{{ __('student.Personal Details') }}:-</h5>
								</div>
						        <!--<div class="col-md-2">
									<input type="hidden" class="form-control" id="reg_id" name="registration_id" placeholder="{{ __('student.Registration No') }}" value="">
									<div class="form-group">
										<label>{{ __('student.Admission No.') }}</label>
										<input type="text" class="form-control" id="admission_no" name="admissionNo" placeholder="{{ __('student.Admission No.') }}"  
										value=""
										onkeypress="javascript:return isNumber(event)">	
										<input type="text" class="form-control invalid" id="admissionNo" name="admissionNo" placeholder="{{ __('student.Admission No.') }}" value="" onkeypress="javascript:return isNumber(event)">
										<span class="invalid-feedback" id="admissionNo_invalid" role="alert">
                                            <strong>The Admission No field is required</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Ledger No') }}<span style=""></span></label>
										<input type="text" class="form-control " name="ledger_no" placeholder="{{ __('Ledger No') }}"  >
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('SRN') }}</label>
										<input type="text" class="form-control" id="srn" name="srn" placeholder="{{ __('SRN') }}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Family ID') }}</label>
										<input type="text" class="form-control" id="family_id" name="family_id" placeholder="{{ __('Family ID') }}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Student Name') }}<span style="color:red;">*</span></label>
										<input type="text" name="first_name" id="first_name" class="form-control invalid " value="{{ old('first_name') }}" placeholder="{{ __('Student Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										<span class="invalid-feedback" id="first_name_invalid" role="alert">
                                            <strong>The Student Name field is required</strong>
                                        </span>
									</div>
								</div>
							<!--<div class="col-md-2">
                    	    	<div class="form-group">
                    				<label>{{ __('common.Last Name') }}</label>
                    				<input type="text" class="form-control" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="last_name" name="last_name" placeholder="{{ __('common.Last Name') }}" value="{{old('last_name')}}">
                    		    </div>
                    		</div>-->
							
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Aadhaar No.') }}</label>
										<input type="text" class="form-control " id="aadhaar" name="aadhaar" placeholder=" {{ __('common.Aadhaar No.') }}" value="{{old('aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Gender') }}<span style="color:red;">*</span></label>
										<select class="form-control invalid" id="gender_id" name="gender_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getgenders))
											@foreach($getgenders as $value)
											<option value="{{ $value->id}}" {{ ($value->id == old('gender_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
										<span class="invalid-feedback" id="gender_id_invalid" role="alert">
                                            <strong>The gender field is required</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Date Of  Birth') }}<span style="color:red;">*</span></label>
										<input type="date" class="form-control invalid" id="dob" name="dob" placeholder=" Date Of  Birth" value="{{old('dob')}}">
										<span class="invalid-feedback" id="dob_invalid" role="alert">
                                            <strong>The dob field is required</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Mobile No.') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control " id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
				                        <div id="mobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>


									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.E-Mail') }}</label>
										<input type="email" class="form-control" id="email" name="email" placeholder="{{ __('common.E-Mail') }}" value="{{old('email')}}">
							          
                                        
							          
									</div>
								</div>
								
                                	<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Class') }}<span style="color:red;">*</span></label>

										<select class="form-control invalid" id="class_type_id" name="class_type_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($classType))
											@foreach($classType as $type)
											<option value="{{ $type->id ?? ''  }}" data-orderBy="{{ $type->orderBy ?? ''  }}" {{ ($type->id == old('class_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
										<span class="invalid-feedback" id="class_type_id_invalid" role="alert">
                                            <strong>The Class field is required</strong>
                                        </span>
									</div>
								</div>
								
								
							<!--	<div class="col-md-2" id="stream_subject_div" style="display:none;">
									<div class="form-group">
										<label>Stream Subject<span style="color:red;">*</span></label>

										<select class="form-control select2" multiple id="stream_subject" name="stream_subject[]">
											<option value="">{{ __('common.Select') }}</option>
										</select>
									</div>
								</div>-->
                                <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Admission Type') }}<span style="color:red;">*</span></label>
										<select class="form-control invalid" id="admission_type_id" name="admission_type_id">
											<!--<option value="">{{ __('common.Select') }}</option>-->
											<option value="1" {{ (1 == old('admission_type_id')) ? 'selected' : 'selected' }}>Non RTE</option>
											<option value="2" {{ (2 == old('admission_type_id')) ? 'selected' : '' }}>RTE</option>
										</select>
									    <span class="invalid-feedback" id="admission_type_id_invalid" role="alert">
                                            <strong>The Admission Type is required</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Religion</label>
										<select class="form-control" id="religion" name="religion">
											<option value="Select" selected="">Select</option>
											<option value="Hindu" {{ ('Hindu' == old('religion')) ? 'selected' : 'selected' }}>Hindu</option>
											<option value="Islam" {{ ('Islam' == old('religion')) ? 'selected' : '' }}>Islam</option>
											<option value="Sikh" {{ ('Sikh' == old('religion')) ? 'selected' : '' }}>Sikh</option>
											<option value="Buddhism" {{ ('Buddhism' == old('religion')) ? 'selected' : '' }}>Buddhism</option>
											<option value="Adivasi" {{ ('Adivasi' == old('religion')) ? 'selected' : '' }}>Adivasi</option>
											<option value="Jain" {{ ('Jain' == old('religion')) ? 'selected' : '' }}>Jain</option>
											<option value="Christianity" {{ ('Christianity' == old('religion')) ? 'selected' : '' }}>Christianity</option>
											<option value="Other" {{ ('Other' == old('religion')) ? 'selected' : '' }}>Other</option>
										</select>
									
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>Category</label>
										<select class="form-control" id="category" name="category">
											<option value="">Select</option>
											<option value="OBC" {{ ('OBC' == old('category')) ? 'selected' : 'selected' }}>OBC</option>
											<option value="ST" {{ ('ST' == old('category')) ? 'selected' : '' }}>ST</option>
											<option value="SC" {{ ('SC' == old('category')) ? 'selected' : '' }}>SC</option>
											<option value="BC" {{ ('BC' == old('category')) ? 'selected' : '' }}>BC</option>
											<option value="GEN" {{ ('GEN' == old('category')) ? 'selected' : '' }}>GEN</option>
											<option value="SBC" {{ ('SBC' == old('category')) ? 'selected' : '' }}>SBC</option>
											<option value="Other" {{ ('Other' == old('category')) ? 'selected' : '' }}>Other</option>
								        </select>
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Caste') }}</label>
										<input type="text" class="form-control" id="caste_category" name="caste_category" placeholder="{{ __('Caste') }}" value="{{old('caste_category')}}" >
									
									</div>
								</div>
								
                          <!--      <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Date Of Admission') }}</label>
										<input type="date" class="form-control" id="admission_date" name="admission_date" value="{{date('Y-m-d')}}">
									
									</div>
								</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Country') }}</label>
										<select class="form-control" name="country" id="country_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getCountry))
											@foreach($getCountry as $country)
											<option value="{{ $country->id ?? ''  }}" {{ ($country->id == $getSetting->country_id) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
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
											<option value="{{ $state->id ?? ''}}" {{ ($state->id == $getSetting->state_id) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
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
											<option value="{{ $cities->id ?? ''  }}" {{ ($cities->id == $getSetting->city_id) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Village/City') }}</label>
										<select class="form-control select2 " id="village_city" name="village_city">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($list))
											@foreach($list as $type)
											<option value="{{ $type->name ?? ''  }}">{{ $type->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								<!--<div class="col-md-2">-->
								<!--	<div class="form-group">-->
								<!--		<label>{{ __('student.Village/City') }}</label>-->
								<!--		<input type="text" class="form-control" id="village_city" name="village_city" placeholder="{{ __('student.Village/City') }}" value="{{old('village_city')}}">-->
								<!--	</div>-->
								<!--</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Students Address') }}</label>
										<input type="text" class="form-control " id="address" name="address" placeholder="{{ __('student.Students Address') }}" value="{{old('address')}}">
										
									</div>
								</div>
									<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Family Annual Income') }}</label>
										<input type="text" name="family_annual_income" id="family_annual_income" class="form-control" value="" placeholder="{{ __('Family Annual Income') }}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Pin Code') }}</label>
										<input type="text" class="form-control" id="pincode" name="pincode" placeholder="{{ __('common.Pin Code') }}" value="{{old('pincode')}}" maxlength="6" onkeypress="javascript:return isNumber(event)">
										
									</div>
								</div>
						<!--		<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('House') }}</label>
										<input type="text" name="house" id="house" class="form-control" value="" placeholder="{{ __('House') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Height') }}</label>
										<input type="text" name="height" id="height" class="form-control" value="" placeholder="{{ __('Height') }}">
										
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Weight') }}</label>
										<input type="text" name="weight" id="weight" class="form-control" value="" placeholder="{{ __('Weight') }}">
										
									</div>
								</div>-->
                                <div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Blood Group') }}</label>
										<select class="form-control" id="blood_group" name="blood_group">
											<option value="">{{ __('common.Select') }}</option>
        										@if(!empty($bloodGroupType))
        											@foreach($bloodGroupType as $bloodtype)
        											<option value="{{ $bloodtype->name ?? ''  }}" {{ ($bloodtype->name == old('blood_group')) ? 'selected' : '' }}>{{ $bloodtype->name ?? ''  }}</option>
        											@endforeach
        										@endif
										</select>
									
									</div>
								</div>
								
							<!--	<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Remark') }} </label>
										<input type="text" class="form-control" id="remark_1" name="remark_1" placeholder="{{ __('student.Remark') }} " value="{{old('remark_1')}}">
									</div>
								</div>-->
								<!--	<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Transport') }}</label>
										<select class="form-control" id="transport" name="transport">
											<option value="Yes" {{ ('Yes' == old('transport')) ? 'selected' : 'selected' }}>{{ __('Yes') }}</option>
											<option value="No" {{ ('No' == old('transport')) ? 'selected' : '' }}>{{ __('No') }}</option>
										</select>
									</div>
								</div>-->
							<!--	<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bus Number') }} </label>
										<input type="text" class="form-control" id="bus_number" name="bus_number" placeholder="{{ __('Bus Number') }} " value="{{old('bus_number')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bus Route') }} </label>
										<input type="text" class="form-control" id="bus_route" name="bus_route" placeholder="{{ __('Bus Route') }} " value="{{old('bus_route')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Stoppage') }} </label>
										<input type="text" class="form-control" id="stoppage" name="stoppage" placeholder="{{ __('Stoppage') }} " value="{{old('stoppage')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Transpor Charges') }} </label>
										<input type="text" class="form-control" id="transpor_charges" name="transpor_charges" placeholder="{{ __('Transpor Charges') }} " value="{{old('transpor_charges')}}">
									</div>
								</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bank Name') }} </label>
										<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="{{ __('Bank Name') }} " value="{{old('bank_name')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Bank Account') }} </label>
										<input type="text" class="form-control" id="bank_account" name="bank_account" placeholder="{{ __('Bank Account') }} " value="{{old('bank_account')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Branch Name') }} </label>
										<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="{{ __('Branch Name') }} " value="{{old('branch_name')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('IFSC') }} </label>
										<input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="{{ __('IFSC') }} " value="{{old('ifsc')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __(' MICR Code') }} </label>
										<input type="text" class="form-control" id="micr_code" name="micr_code" placeholder="{{ __('MICR Code') }} " value="{{old('micr_code')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Bank Account Holder</label>
										<input type="text" class="form-control" id="bank_account_holder" name="bank_account_holder" placeholder="Bank Account Holder" value="{{old('bank_account_holder')}}">
									</div>
								</div>
								<!--<div class="col-md-2">
									<div class="form-group">
										<label>Optional Subject</label>
										<input type="text" class="form-control" id="optional_subject" name="optional_subject" placeholder="Optional Subject" value="{{old('optional_subject')}}">
									</div>
								</div>-->
								<div class="col-md-2">
									<div class="form-group">
										<label>Urban/Ruler</label>
										<input type="text" class="form-control" id="urban" name="urban" placeholder="Urban/Ruler" value="{{old('urban')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>District</label>
										<input type="text" class="form-control" id="district" name="district" placeholder="District" value="{{old('district')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Tehsil</label>
										<input type="text" class="form-control" id="tehsil" name="tehsil" placeholder="Tehsil" value="{{old('tehsil')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Father's Pancard</label>
										<input type="text" class="form-control" id="father_pancard" name="father_pancard" placeholder="Father's Pancard" value="{{old('father_pancard')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Mother's Pancard</label>
										<input type="text" class="form-control" id="mother_pancard" name="mother_pancard" placeholder="Mother's Pancard" value="{{old('mother_pancard')}}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Income Tax Payee Father</label>
										<select class="form-control" id="income_tax_payee_father" name="income_tax_payee_father">
										    <option value="">Select</option>
										    <option value="Yes">Yes</option>
										    <option value="No">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>Income Tax Payee Mother</label>
										<select class="form-control" id="income_tax_payee_mother" name="income_tax_payee_mother">
										    <option value="">Select</option>
										    <option value="Yes">Yes</option>
										    <option value="No">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>BPL</label>
										<select class="form-control" id="bpl" name="bpl">
										    <option value="">Select</option>
										    <option value="Yes">Yes</option>
										    <option value="No">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>BPL Cetificate No.</label>
										<input type="text" class="form-control" id="bpl_certificate_no" name="bpl_certificate_no" placeholder="BPL Cetificate No." value="{{old('bpl_certificate_no')}}">
									</div>
								</div>
							</div>
								<hr>
							<div class="row m-2">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('Guardian Details') }}:-</h5>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Fathers Name') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{old('father_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										<span class="invalid-feedback" id="father_name_invalid" role="alert">
                                            <strong>The Father's name field is required</strong>
                                        </span>
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('common.Fathers Contact No') }}<span style="color:red;">*</span></label>
										<input type="text" class="form-control invalid" id="father_mobile" name="father_mobile" placeholder="{{ __('common.Fathers Contact No') }}" value="{{old('father_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
										 <div id="fathermobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>

									<span class="invalid-feedback" id="father_mobile_invalid" role="alert">
                                         <strong>The Fathers's No is required</strong>
                                    </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Fathers Aadhaar') }}</label>
										<input type="text" class="form-control" id="father_aadhaar" name="father_aadhaar" placeholder="{{ __('Fathers Aadhaar') }}" value="{{old('father_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
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
										<input type="text" class="form-control invalid" id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{old('mother_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
										<span class="invalid-feedback" id="mother_name_invalid" role="alert">
                                            <strong>The Mother's name field is required</strong>
                                        </span>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Mother Mobile No') }}</label>
										<input type="text" class="form-control" id="mother_mob" name="mother_mob" placeholder="{{ __('Mother Mobile No') }}" value="{{old('mother_mob')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Mothers Aadhaar') }}</label>
										<input type="text" class="form-control" id="mother_aadhaar" name="mother_aadhaar" placeholder="{{ __('Mothers Aadhaar') }}" value="{{old('mother_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>Mother Occupation</label>
										<input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="{{old('mother_occupation')}}">
									</div>
								</div>
								
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Guardian Name') }}</label>
										<input type="text" class="form-control" id="guardian_name" name="guardian_name" placeholder="{{ __('Guardian Name') }}" value="{{old('guardian_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('Guardian Mobile No') }}</label>
										<input type="text" class="form-control " id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('Guardian Mobile No') }}" value="{{old('guardian_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
								
									</div>
								</div>
								
						    </div>		
							<hr>
							<div class="row m-2">
								<div class=" col-md-12 title">
									<h5 class="text-danger">{{ __('student.Document Upload') }}:-</h5>
								</div>
								<div class="col-md-3">
									<lable>{{ __('student.Student Photo') }}</lable>
									<div class="input file form-control">
										<input type="file" class="" name="student_img" id="student_img" value="{{old('student_img')}}" accept="image/png, image/jpg, image/jpeg">
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('student.Father Photo') }}</lable>
									<div class="input file form-control">
										<input type="file" name="father_img" id="father_img" value="{{old('father_img')}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
								            <p class="text-danger" id="image_errors"></p>
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('student.Mother Photo') }}</lable>
									<div class="input file form-control">
										<input type="file" name="mother_img" id="mother_img" value="{{old('mother_img')}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" accept="image/png, image/jpg, image/jpeg">
								           <p class="text-danger" id="image_er"></p>
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
							</div>
							<hr>
							<div class="mesterClassAmt" class="row m-2"></div>
							<div class="col-md-12 text-center"> 
								<button type="submit" class="btn btn-primary " id="is-invalid">{{ __('common.submit') }}</button><br><br>
							</div>
						</form>
				
				</div>
			</div>
		</div>
	

</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>







<script>
$(document).ready(function() {
    // Handler for form submission
    $('#is-invalid').on('click', function(event) {
        var mobileValue = $('#mobile').val();
        var mobileMinLength = 10;

        if (mobileValue.length < mobileMinLength) {
            $('#mobileValidationMessage').show();
            event.preventDefault();  
            $('html, body').animate({
            scrollTop: 0
        }, 800);
        } else {
            $('#mobileValidationMessage').hide();
        }

        // Perform father's mobile input validation
        var father_mobileInputValue = $('#father_mobile').val();
        var fatherMobileMinLength = 10;

        if (father_mobileInputValue.length < fatherMobileMinLength) {
            $('#fathermobileValidationMessage').show();
            event.preventDefault(); 
            $('html, body').animate({
            scrollTop: 0
        }, 800);
        } else {
            $('#fathermobileValidationMessage').hide();
        }
    });
});
</script>
<script>
    $(document).ready(function(){
        $('#student_img').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
   

    $(document).ready(function(){
        $('#father_img').change(function(e){
            $('#image_errors').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_errors').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_errors').html("");
            }
        }else{
            $('#image_errors').html("Image Size File");
            $(this).val('');
        }
        });
    });
   

    $(document).ready(function(){
        $('#mother_img').change(function(e){
            $('#image_er').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_er').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_er').html("");
            }
        }else{
            $('#image_er').html("Image Size File");
            $(this).val('');
        }
        });
    });
   
</script>

<style>
  .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            margin-bottom:10px;
        }
        .header img {
            max-height: 50px;
        }
    
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
    
    .blink_me {
        animation: blinker 1s linear infinite;
    }

    @keyframes blinker {
      50% {
        opacity: 0;
      }
    }
</style>



<style>
	@media only screen and (max-width: 600px) {
		.upload {
			margin-left: 27%;
			margin-top: 7%;
		}
	}
</style>



