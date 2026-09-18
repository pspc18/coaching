@php
$getstudents = Helper::getstudents();
$getgenders = Helper::getgender();
$classType = Helper::classType();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
$getSetting = Helper::getSetting();
$bloodGroupType = Helper::bloodGroupType();
$getAdmissionDatatableFields = Helper::getAdmissionDatatableFields();
$list = DB::table('custom_villages_list')->orderBy('name','ASC')->whereNull('deleted_at')->get();
$gender = DB::table('gender')->whereNull('deleted_at')->pluck('name')->implode(',');
$villageList = DB::table('custom_villages_list')->whereNull('deleted_at')->pluck('name')->implode(',');
$class = DB::table('class_types')->whereNull('deleted_at')->pluck('name')->implode(',');
$setting = Db::table('settings')->whereNull('deleted_at')->first();
$stateList = DB::table('states')->where('id', 13)->pluck('name')->implode(',');
$cityList = DB::table('citys')->whereNull('deleted_at')->where('state_id', 13)->take(25)->pluck('name')->implode(',');
$bloodgroupList = DB::table('blood_groups')->whereNull('deleted_at')->pluck('name')->implode(',');
// DB से student fields fetch करें
$student_fields = DB::table('student_fields')->whereNull('deleted_at')->where('status',0)->pluck('field_name'); // Collection
$studentFields_new = DB::table('student_fields')->whereNull('deleted_at')->where('status',0)->where('type','new_input')->get();
$student_fields_required = DB::table('student_fields')->whereNull('deleted_at')->pluck('required', 'field_name')->toArray(); 
@endphp
@extends('layout.app')
@section('content')

@php
    $studentCount = DB::table('admissions')->where('deleted_at',null)->count();
  
@endphp

<div class="content-wrapper admission-add-page">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="admission-shell">
				<div class="admission-hero">
					<div>
						<div class="page-kicker">{{ __('student.Students Admission Management') }}</div>
						<h1>{{ __('student.Add New Admission') }}</h1>
						<p>Same admission process, cleaner layout, and better mobile handling.</p>
					</div>
					<div class="page-actions">
						<a href="{{url('admissionView')}}" class="btn btn-light btn-sm {{ Helper::permissioncheck(3)->view ? '' : 'd-none' }}"><i class="fa fa-eye"></i> <span class="Display_none_mobile"> {{ __('common.View') }} </span></a>
						<a href="{{url('studentsDashboard')}}" class="btn btn-outline-light btn-sm"><i class="fa fa-arrow-left"></i> <span class="Display_none_mobile"> {{ __('common.Back') }} </span></a>
					</div>
				</div>

				<div class="admission-card">
					<div class="admission-section-head">
						<div>
							<span class="section-pill">{{ __('student.Search Registered Students') }}</span>
							<h3>Find existing students before creating a new admission</h3>
						</div>
					</div>

					<form id="quickForm" action="{{url('admissionStudentSearch')}}" method="post">
							@csrf

							<div class="row g-3 admission-form-grid">
								<div class="col-md-2">
									<div class="form-group">
										<label>{{ __('student.Registration No') }}</label>
										<input type="text" class="form-control"  id="registration_no"name="registration_no" placeholder="{{ __('student.Registration No') }}" value="{{ $search['registration_no'] ?? '' }}">
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class='text-danger'>{{ __('common.Class') }}*</label>
										<select class="form-control select2 " id="class_search_id" name="class_search_id" required>
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($classType))
											@foreach($classType as $type)
											<option value="{{ $type->id ?? ''  }}">{{ $type->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								
								<div class="col-md-5">
									<div class="form-group">
										<label>{{ __('common.Search By Keywords') }}</label>
										<input type="text" class="form-control" id="searchName" name="name" placeholder="{{ __('common.Ex. Name, Mobile, Email, Aadhaar etc.') }}" value="{{ $search['name'] ?? '' }}">
									</div>
								</div>
								<div class="col-md-1 admission-search-action">
									<div class="form-group">
										<label class="text-white">{{__('common.Search') }}</label>
										<button type="button" class="btn btn-primary w-100" onclick="SearchValue()">{{ __('common.Search') }}</button>
									</div>
								</div>

							</div>
						</form>

						<div class="student_list_show"></div>
                        <div class="section-divider"></div>
						<form id="form-submit" action="{{ url('admissionAdd') }}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="admission-form-section">
								<div class="admission-section-head">
									<div>
										<span class="section-pill">01</span>
										<h3>{{ __('student.Personal Details') }}</h3>
									</div>
								</div>
                                <input type="text" class="form-control" id="reg_id" name="registration_id" placeholder="{{ __('student.Registration No') }}" value="">

								<div class="row g-3 admission-form-grid">
                                    <div class="col-md-2 {{ $student_fields->contains('admissionNo') ? '' : 'd-none' }}">
                                            <div class="form-group">
                                                <label>{{ __('student.Admission No.') }}
                                                @if($student_fields_required['admissionNo'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                            </label>
                                                <input type="text" class="form-control"  id="admissionNo"name="admissionNo"placeholder="{{ __('student.Admission No.') }}" value="{{ $BillCounter ?? '' }}" 
                                                       onkeypress="return isNumber(event)"
                                                      >
                                            
                                            </div>
                                        </div>
								<div class="col-md-2  {{ $student_fields->contains('ledger_no') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Ledger No') }}
										 @if($student_fields_required['ledger_no'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " name="ledger_no" placeholder="{{ __('Ledger No') }}"  >
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('student_pen') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Student Pen
										 @if($student_fields_required['student_pen'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="student_pen" name="student_pen" placeholder="Student Pen" onkeypress="javascript:return isNumber(event)" maxlength="11">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('apaar_id') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Apaar Id
										    @if($student_fields_required['apaar_id'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="apaar_id" name="apaar_id" placeholder="Apaar Id" onkeypress="javascript:return isNumber(event)" maxlength="12">
									</div>
								</div>
							
								<div class="col-md-2 {{ $student_fields->contains('family_id') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Family ID') }}
										@if($student_fields_required['family_id'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="family_id" name="family_id" placeholder="{{ __('Family ID') }}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('first_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Student Name') }}
										@if($student_fields_required['first_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="first_name" id="first_name" class="form-control invalid " value="{{ old('first_name') }}" placeholder="{{ __('Student Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
							
								<div class="col-md-2 {{ $student_fields->contains('aadhaar') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Aadhaar No.') }}
										@if($student_fields_required['aadhaar'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="aadhaar" name="aadhaar" placeholder=" {{ __('common.Aadhaar No.') }}" value="{{old('aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('jan_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Jan Aadhaar No.') }}
										@if($student_fields_required['jan_aadhaar'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="jan_aadhaar" name="jan_aadhaar" placeholder=" {{ __('Jan Aadhaar No.') }}" value="{{old('jan_aadhaar')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('gender_id') ? '' : 'd-none' }}">
    <div class="form-group">

        <label>
            {{ __('common.Gender') }}

            @if($student_fields_required['gender_id'] == 0)
                <span style="color:red;">*</span>
            @endif
        </label>

        <select class="form-control invalid select2"
                id="gender_id"
                name="gender_id">

            <option value="">
                {{ __('common.Select') }}
            </option>

            @if(!empty($getgenders))

                @foreach($getgenders as $value)

                    <option value="{{ $value->id }}"
                            {{ ($value->id == old('gender_id')) ? 'selected' : '' }}>

                        {{ $value->name ?? '' }}

                    </option>

                @endforeach

            @endif

        </select>

    </div>
</div>
								<div class="col-md-2 {{ $student_fields->contains('dob') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Date Of  Birth') }}
										@if($student_fields_required['dob'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="date" class="form-control invalid" id="dob" name="dob" placeholder=" Date Of  Birth" value="{{old('dob')}}">
									
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('mobile') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Mobile No.') }}
										@if($student_fields_required['mobile'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
				                        <div id="mobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>


									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('email') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.E-Mail') }}
										@if($student_fields_required['email'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="email" class="form-control" id="email" name="email" placeholder="{{ __('common.E-Mail') }}" value="{{old('email')}}">
							          
									</div>
								</div>
								
                                	<div class="col-md-2 {{ $student_fields->contains('class_type_id') ? '' : 'd-none' }}">
    <div class="form-group">

        <label>
            {{ __('common.Class') }}

            @if($student_fields_required['class_type_id'] == 0)
                <span style="color:red;">*</span>
            @endif
        </label>

        <select class="form-control invalid select2"
                id="class_type_id"
                name="class_type_id">

            <option value="">
                {{ __('common.Select') }}
            </option>

            @if(!empty($classType))

                @foreach($classType as $type)

                    <option value="{{ $type->id ?? '' }}"
                            data-orderBy="{{ $type->orderBy ?? '' }}"
                            {{ ($type->id == old('class_type_id')) ? 'selected' : '' }}>

                        {{ $type->name ?? '' }}

                    </option>

                @endforeach

            @endif

        </select>

    </div>
</div>
								
								
								<div class="col-md-2 " id="stream_subject_div" style="display:none;">
									<div class="form-group">
										<label>Stream Subject
									</label>

										<select class="form-control select2" multiple id="stream_subject" name="stream_subject[]">
											<option value="">{{ __('common.Select') }}</option>
										</select>
									</div>
								</div>
                               
                              <div class="col-md-2 {{ $student_fields->contains('admission_type_id') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Admission Type(Non RTE)
										@if($student_fields_required['admission_type_id'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control invalid select2" id="admission_type_id" name="admission_type_id">
											<option value="">{{ __('common.Select') }}</option>
											<option value="1" {{ (1 == old('admission_type_id')) ? 'selected' : 'selected' }}>Yes</option>
											<option value="2" {{ (2 == old('admission_type_id')) ? 'selected' : '' }}>NO</option>
										</select>
									  
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('religion') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Religion
										@if($student_fields_required['religion'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="religion" name="religion">
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
								
								<div class="col-md-2 {{ $student_fields->contains('category') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Category
										@if($student_fields_required['category'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="category" name="category">
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
								
								<div class="col-md-2 {{ $student_fields->contains('caste_category') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Caste') }}@if($student_fields_required['caste_category'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="caste_category" name="caste_category" placeholder="{{ __('Caste') }}" value="{{old('caste_category')}}" >
									
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('blood_group') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Blood Group') }}@if($student_fields_required['blood_group'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<select class="form-control select2" id="blood_group" name="blood_group">
											<option value="">{{ __('common.Select') }}</option>
        										@if(!empty($bloodGroupType))
        											@foreach($bloodGroupType as $bloodtype)
        											<option value="{{ $bloodtype->name ?? ''  }}" {{ ($bloodtype->name == old('blood_group')) ? 'selected' : '' }}>{{ $bloodtype->name ?? ''  }}</option>
        											@endforeach
        										@endif
										</select>
									
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('medium') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Medium
										@if($student_fields_required['medium'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="medium" name="medium">
											<option value="">Select</option>
											<option value="Hindi">Hindi</option>
											<option value="English">English</option>
										</select>
									</div>
								</div>
								
                                <div class="col-md-2 {{ $student_fields->contains('admission_date') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('student.Date Of Admission') }}
										@if($student_fields_required['admission_date'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="date" class="form-control" id="admission_date" name="admission_date" value="{{date('Y-m-d')}}">
									
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('country') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Country') }}
										@if($student_fields_required['country'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" name="country" id="country_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getCountry))
											@foreach($getCountry as $country)
											<option value="{{ $country->id ?? ''  }}" {{ ($country->id == $getSetting->country_id) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('state') ? '' : 'd-none' }}">
									<div class="form-group">
										<label for="State" class="required">{{ __('common.State') }}
										@if($student_fields_required['state'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control stateId select2" id="state_id" name="state">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getState))
											@foreach($getState as $state)
											<option value="{{ $state->id ?? ''}}" {{ ($state->id == $getSetting->state_id) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
											@endforeach
											@endif
										</select>

									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('city') ? '' : 'd-none' }}">
									<div class="form-group">
										<label for="City">{{ __('common.City') }}
										@if($student_fields_required['city'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control cityId select2" name="city" id="city_id">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getCity))
											@foreach($getCity as $cities)
											<option value="{{ $cities->id ?? ''  }}" {{ ($cities->id == $getSetting->city_id) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
											@endforeach
											@endif
										</select>
									</div>
								</div>
								
							
								<div class="col-md-2 {{ $student_fields->contains('village_city') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('student.Village/City') }}
										@if($student_fields_required['village_city'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="village_city" name="village_city" placeholder="{{ __('student.Village/City') }}" value="{{old('village_city')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('address') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('student.Students Address') }}
										@if($student_fields_required['address'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="address" name="address" placeholder="{{ __('student.Students Address') }}" value="{{old('address')}}">
										
									</div>
								</div>
									<div class="col-md-2 {{ $student_fields->contains('family_annual_income') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Family Annual Income') }}
										@if($student_fields_required['family_annual_income'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="family_annual_income" id="family_annual_income" class="form-control" value="" placeholder="{{ __('Family Annual Income') }}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('pincode') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Pin Code') }}
										@if($student_fields_required['pincode'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="pincode" name="pincode" placeholder="{{ __('common.Pin Code') }}" value="{{old('pincode')}}" maxlength="6" onkeypress="javascript:return isNumber(event)">
										
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('relation_student') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Relation with the student') }}
										@if($student_fields_required['relation_student'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="relation_student" id="relation_student" class="form-control" value="" placeholder="{{ __('Relation with the student') }}">
										
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('school_namestudied_last_year') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('School Studied Last Year') }}
										@if($student_fields_required['school_namestudied_last_year'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="school_namestudied_last_year" id="school_namestudied_last_year" class="form-control" value="" placeholder="{{ __('School Studied Last Year') }}">
										
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('house') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('House') }}
										@if($student_fields_required['house'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="house" id="house" class="form-control" value="" placeholder="{{ __('House') }}">
										
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('height') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Height') }}
										@if($student_fields_required['height'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="height" id="height" class="form-control" value="" placeholder="{{ __('Height') }}">
										
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('weight') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Weight') }}
										@if($student_fields_required['weight'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" name="weight" id="weight" class="form-control" value="" placeholder="{{ __('Weight') }}">
										
									</div>
								</div>
                                
								
								<div class="col-md-2 {{ $student_fields->contains('remark_1') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('student.Remark') }}
										@if($student_fields_required['remark_1'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="remark_1" name="remark_1" placeholder="{{ __('student.Remark') }} " value="{{old('remark_1')}}">
									</div>
								</div>
									<div class="col-md-2 {{ $student_fields->contains('transport') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Transport') }}
										@if($student_fields_required['transport'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="transport" name="transport">
											<option value="Yes" {{ ('Yes' == old('transport')) ? 'selected' : 'selected' }}>{{ __('Yes') }}</option>
											<option value="No" {{ ('No' == old('transport')) ? 'selected' : '' }}>{{ __('No') }}</option>
										</select>
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bus_number') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Bus Number') }}
										@if($student_fields_required['bus_number'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bus_number" name="bus_number" placeholder="{{ __('Bus Number') }} " value="{{old('bus_number')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bus_route') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Bus Route') }}
										@if($student_fields_required['bus_route'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bus_route" name="bus_route" placeholder="{{ __('Bus Route') }} " value="{{old('bus_route')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('stoppage') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Stoppage') }}
										@if($student_fields_required['stoppage'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="stoppage" name="stoppage" placeholder="{{ __('Stoppage') }} " value="{{old('stoppage')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('transpor_charges') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Transpor Charges') }}
										@if($student_fields_required['transpor_charges'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="transpor_charges" name="transpor_charges" placeholder="{{ __('Transpor Charges') }} " value="{{old('transpor_charges')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bank_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Bank Name') }}
										@if($student_fields_required['bank_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="{{ __('Bank Name') }} " value="{{old('bank_name')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bank_account') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Bank Account') }}
										@if($student_fields_required['bank_account'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_account" name="bank_account" placeholder="{{ __('Bank Account') }} " value="{{old('bank_account')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('branch_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Branch Name') }}
										@if($student_fields_required['branch_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="{{ __('Branch Name') }} " value="{{old('branch_name')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('ifsc') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('IFSC') }}
										@if($student_fields_required['ifsc'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="{{ __('IFSC') }} " value="{{old('ifsc')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('micr_code') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __(' MICR Code') }}
										@if($student_fields_required['micr_code'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="micr_code" name="micr_code" placeholder="{{ __('MICR Code') }} " value="{{old('micr_code')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bank_account_holder') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Bank Account Holder
										@if($student_fields_required['bank_account_holder'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_account_holder" name="bank_account_holder" placeholder="Bank Account Holder" value="{{old('bank_account_holder')}}">
									</div>
								</div>
							
							
								<div class="col-md-2 {{ $student_fields->contains('district') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>District
										@if($student_fields_required['district'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="district" name="district" placeholder="District" value="{{old('district')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('tehsil') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Tehsil
										@if($student_fields_required['tehsil'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="tehsil" name="tehsil" placeholder="Tehsil" value="{{old('tehsil')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('father_pancard') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Father's Pancard
										@if($student_fields_required['father_pancard'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="father_pancard" name="father_pancard" placeholder="Father's Pancard" value="{{old('father_pancard')}}">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('mother_pancard') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Mother's Pancard
										@if($student_fields_required['mother_pancard'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="mother_pancard" name="mother_pancard" placeholder="Mother's Pancard" value="{{old('mother_pancard')}}">
									</div>
								</div>
							
							
								<div class="col-md-2 {{ $student_fields->contains('bpl') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>BPL
										@if($student_fields_required['bpl'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="bpl" name="bpl">
										    <option value="">Select</option>
										    <option value="Yes">Yes</option>
										    <option value="No">No</option>
										</select>
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('bpl_certificate_no') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>BPL Cetificate No.
										@if($student_fields_required['bpl_certificate_no'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bpl_certificate_no" name="bpl_certificate_no" placeholder="BPL Cetificate No." value="{{old('bpl_certificate_no')}}">
									</div>
								</div>
							
						
								<div class="col-md-4 {{ $student_fields->contains('previous_school') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Name  And  Address Of Previous School
										@if($student_fields_required['previous_school'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="previous_school" name="previous_school" placeholder="Name  And  Address Of Previous School" value="{{old('previous_school')}}">
									</div>
								</div>
                    			@foreach($studentFields_new as $field)
                                    <div class="col-md-{{ $field->grid_column }}">
                                        <div class="form-group">
                                            <label>
                                                {{ $field->field_label }}
                                                @if($field->required == 0) 
                                                    <span style="color:red;">*</span>
                                                @endif
                                            </label>
                                
                                            {{-- Text --}}
                                            @if($field->field_type == 'text')
                                                <input type="text" name="{{ $field->field_name }}"  class="form-control" placeholder="{{ $field->field_label }}"  value="{{ old($field->field_name, $field->default_value) }}">
                                
                                            {{-- Email --}}
                                            @elseif($field->field_type == 'email')
                                                <input type="email" 
                                                       name="{{ $field->field_name }}"  class="form-control"  placeholder="{{ $field->field_label }}" value="{{ old($field->field_name, $field->default_value) }}">
                                            {{-- Number --}}
                                            @elseif($field->field_type == 'number')
                                                <input type="number" 
                                                       name="{{ $field->field_name }}"  class="form-control"  placeholder="{{ $field->field_label }}" value="{{ old($field->field_name, $field->default_value) }}">
                                
                                            {{-- Date --}}
                                            @elseif($field->field_type == 'date')
                                                <input type="date" name="{{ $field->field_name }}" class="form-control" value="{{ old($field->field_name, $field->default_value) }}">
                                
                                            {{-- Select / Dropdown --}}
                                            @elseif($field->field_type == 'dropdown')
                                                <select name="{{ $field->field_name }}" class="form-control">
                                                    <option value="">Select</option>
                                                    @foreach(explode(',', $field->default_value ?? '') as $option)
                                                        <option value="{{ trim($option) }}" >
                                                            {{ trim($option) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                
                                            {{-- File Upload --}}
                                            @elseif($field->field_type == 'file')
                                                <input type="file" name="{{ $field->field_name }}" class="form-control">
                                
                                            {{-- Checkbox --}}
                                            @elseif($field->field_type == 'checkbox')
                                                @foreach(explode(',', $field->default_value ?? '') as $checkbox)
                                                 @if(!empty($checkbox))
                                                    <div class="form-check">
                                                        <input type="checkbox" 
                                                               name="{{ $field->field_name }}[]" 
                                                               value="{{ $checkbox }}"
                                                               class="form-check-input" id="{{ $checkbox }}{{$field->id}}">
                                                        <label class="form-check-label" for="{{ $checkbox }}{{$field->id}}">{{ $checkbox }}</label>
                                                    </div>
                                                     @endif
                                                @endforeach
                                
                                            {{-- Radio --}}
                                            @elseif($field->field_type == 'radio')
                                                @foreach(explode(',', $field->default_value ?? '') as $radio)
                                                   @if(!empty($radio))
                                                    <div class="form-check">
                                                       
                                                        <input type="radio"   id="{{ $radio }}{{$field->id}}" name="{{ $field->field_name }}" value="{{ $radio ?? '' }}" class="form-check-input">
                                                        <label class="form-check-label" for="{{ $radio }}{{$field->id}}">{{ $radio }}</label>
                                                    </div>
                                                     @endif
                                                @endforeach
                                            @endif
                                
                                        </div>
                                    </div>
                                @endforeach


								</div>
							</div>
							<div class="section-divider"></div>
							<div class="admission-form-section">
								<div class="admission-section-head">
									<div>
										<span class="section-pill">02</span>
										<h3>{{ __('Guardian Details') }}</h3>
									</div>
								</div>
								<div class="row g-3 admission-form-grid">
								<div class="col-md-2 {{ $student_fields->contains('father_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Fathers Name') }}
										@if($student_fields_required['father_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{old('father_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
										</select>
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('father_mobile') ? '' : 'd-none' }}">  
									<div class="form-group">
										<label>{{ __('common.Fathers Contact No') }}
										@if($student_fields_required['father_mobile'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="father_mobile" name="father_mobile" placeholder="{{ __('common.Fathers Contact No') }}" value="{{old('father_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
										 <div id="fathermobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>

								
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('father_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Fathers Aadhaar') }}
										@if($student_fields_required['father_aadhaar'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                    </label>
										<input type="text" class="form-control" id="father_aadhaar" name="father_aadhaar" placeholder="{{ __('Fathers Aadhaar') }}" value="{{old('father_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('father_occupation') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Father Occupation
										@if($student_fields_required['father_occupation'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                    </label>
										<input type="text" class="form-control" id="father_occupation" name="father_occupation" placeholder="Father Occupation" value="{{old('father_occupation')}}">
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('mother_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('common.Mothers Name') }}@if($student_fields_required['mother_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{old('mother_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('mother_mob') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Mother Mobile No') }}
										@if($student_fields_required['mother_mob'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_mob" name="mother_mob" placeholder="{{ __('Mother Mobile No') }}" value="{{old('mother_mob')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('mother_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Mothers Aadhaar') }}
										@if($student_fields_required['mother_aadhaar'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_aadhaar" name="mother_aadhaar" placeholder="{{ __('Mothers Aadhaar') }}" value="{{old('mother_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('mother_occupation') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>Mother Occupation
										@if($student_fields_required['mother_occupation'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="{{old('mother_occupation')}}">
									</div>
								</div>
								
								<div class="col-md-2 {{ $student_fields->contains('guardian_name') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Guardian Name') }}
										@if($student_fields_required['guardian_name'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="guardian_name" name="guardian_name" placeholder="{{ __('Guardian Name') }}" value="{{old('guardian_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
								<div class="col-md-2 {{ $student_fields->contains('guardian_mobile') ? '' : 'd-none' }}">
									<div class="form-group">
										<label>{{ __('Guardian Mobile No') }}
										@if($student_fields_required['guardian_mobile'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control " id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('Guardian Mobile No') }}" value="{{old('guardian_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
								
									</div>
								</div>
								
						    </div>		
							</div>
							<div class="section-divider"></div>
							<div class="admission-form-section">
								<div class="admission-section-head">
									<div>
										<span class="section-pill">03</span>
										<h3>{{ __('student.Document Upload') }}</h3>
									</div>
								</div>
								<div class="row g-3 admission-form-grid">
								<div class="col-md-3 {{ $student_fields->contains('student_img') ? '' : 'd-none' }}">
									<lable>{{ __('student.Student Photo') }}
									@if($student_fields_required['student_img'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </lable>
									<div class="input file form-control">
										<input type="file" class="" name="student_img" id="student_img" value="{{old('student_img')}}">
									</div>
								</div>
								<div class="col-md-1 {{ $student_fields->contains('student_img') ? '' : 'd-none' }}">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3 {{ $student_fields->contains('father_img') ? '' : 'd-none' }}">
									<lable>{{ __('student.Father Photo') }}
									@if($student_fields_required['father_img'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </lable>
									<div class="input file form-control">
										<input type="file" name="father_img" id="father_img" value="{{old('father_img')}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								            <p class="text-danger" id="image_errors"></p>
									</div>
								</div>
								<div class="col-md-1 {{ $student_fields->contains('father_img') ? '' : 'd-none' }}">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								<div class="col-md-3 {{ $student_fields->contains('mother_img') ? '' : 'd-none' }}">
									<lable>{{ __('student.Mother Photo') }}
									@if($student_fields_required['mother_img'] == 0)
                                                    <span style="color:red;">*</span>
                                                @endif
                                                </lable>
									<div class="input file form-control">
										<input type="file" name="mother_img" id="mother_img" value="{{old('mother_img')}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								           <p class="text-danger" id="image_er"></p>
									</div>
								</div>
								<div class="col-md-1 {{ $student_fields->contains('mother_img') ? '' : 'd-none' }}">
									<img src="{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								</div>
								</div>
							</div>
							<div class="section-divider"></div>
							<div class="mesterClassAmt" class="row m-2"></div>
							<div class="admission-submit-bar text-center"> 
							
							@if(Session::get('student_count') >= Session::get('register_student'))
								<button type="submit" class="btn btn-primary btn-submit"  id="submitButton">{{ __('common.submit') }}</button>
							@endif
							</div>
                        </form>
                    </div>
                </div>
            </div>
    </section>

</div>
 
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>

<script>
    $(document).ready(function(){
        var baseUrl = "{{ url('/') }}";
        
        $('#class_type_id').change(function(){
            var class_type_id = parseInt($(this).val());
            var orderBy = parseInt($(this).find('option:selected').attr('data-orderBy'));
            
            if(orderBy > 10){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'post',
                    url: baseUrl + '/getStreamSubjects',
                    data: {
                        class_type_id: class_type_id
                    },
                    success: function(data) {
                        var options = "";
                        $('#stream_subject').html("");
                            for(var i = 0; i < data.length; i++){
                                options += '<option value="'+ data[i].id +'">'+ data[i].name +'</option>';
                            }
                        $('#stream_subject').html(options);
                        $('#stream_subject_div').show();
                    }
                });
            }else{
                $('#stream_subject').html("");
                $('#stream_subject_div').hide();
            }
        });
    });
</script>





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

    .admission-add-page {
        background: #f5f8ff;
    }

    .admission-shell {
        max-width: 1480px;
        margin: 0 auto;
    }

    .admission-hero {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 24px;
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(135deg, #314fa2 0%, #3f62c0 55%, #2f4d9f 100%);
        box-shadow: 0 18px 40px rgba(39, 63, 129, 0.18);
        margin-bottom: 18px;
    }

    .admission-hero h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
        line-height: 1.15;
    }

    .admission-hero p {
        margin: 6px 0 0;
        color: rgba(255,255,255,.82);
        font-size: 14px;
    }

    .page-kicker,
    .section-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .page-kicker {
        margin-bottom: 8px;
        background: rgba(255,255,255,.16);
        color: #e8efff;
    }

    .section-pill {
        background: #eaf0ff;
        color: #3550a7;
        margin-bottom: 8px;
    }

    .page-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .page-actions .btn {
        border-radius: 12px;
        min-width: 110px;
        font-weight: 600;
    }

    .admission-card {
        background: #fff;
        border: 1px solid rgba(44, 73, 150, 0.10);
        border-radius: 22px;
        box-shadow: 0 10px 28px rgba(17, 24, 39, 0.06);
        padding: 22px;
    }

    .admission-section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }

    .admission-section-head h3 {
        margin: 0;
        color: #172241;
        font-size: 18px;
        font-weight: 800;
    }

    .admission-form-grid .form-group {
        margin-bottom: 0;
    }

    .admission-form-grid .form-control,
    .admission-form-grid .select2-container--default .select2-selection--single,
    .admission-form-grid .select2-selection--multiple,
    .admission-form-grid .input.file.form-control {
        border-radius: 14px !important;
        border-color: #d9e2f3 !important;
        min-height: 44px;
        box-shadow: none !important;
    }

    .admission-form-grid label {
        color: #3a4767;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(120, 139, 189, 0.32), transparent);
        margin: 20px 0;
    }

    .admission-submit-bar {
        padding-top: 8px;
    }

    .admission-submit-bar .btn-submit {
        min-width: 220px;
        border-radius: 14px;
        box-shadow: 0 12px 24px rgba(49, 79, 162, 0.22);
    }

    .student_list_show {
        margin-top: 18px;
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

        .admission-hero {
            padding: 18px;
            border-radius: 18px;
            flex-direction: column;
            align-items: flex-start;
        }

        .admission-hero h1 {
            font-size: 24px;
        }

        .admission-card {
            padding: 16px;
            border-radius: 18px;
        }

        .admission-section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .page-actions {
            width: 100%;
        }

        .page-actions .btn {
            flex: 1 1 0;
            min-width: 0;
        }

        .admission-submit-bar .btn-submit {
            width: 100%;
            min-width: 0;
        }

        .admission-form-grid .col-md-1,
        .admission-form-grid .col-md-2,
        .admission-form-grid .col-md-3,
        .admission-form-grid .col-md-4,
        .admission-form-grid .col-md-5 {
            width: 100%;
            max-width: 100%;
        }
	}
</style>
<script>
$(document).ready(function(){
    $('#class_type_id').val('');
    
    $('#class_type_id').change(function(){
        if($('#admission_type_id').val() == 1){
            $('.mesterClassAmt').removeClass('d-none');
            mesterData();
        }else{
            $('.mesterClassAmt').addClass('d-none');
        }
    });
    
    $('#admission_type_id').change(function(){
      //      $('#class_type_id').val('');
      
            if($('#admission_type_id').val() == 1){
            $('.mesterClassAmt').removeClass('d-none');
            mesterData();
        }else{
            $('.mesterClassAmt').addClass('d-none');
        }veClass('d-none');
            mesterData();
    });
    
})
	var basurl = "{{ url('/') }}";
	function mesterData() {
	
		var class_type_id = $('#class_type_id :selected').val();
		if (class_type_id > 0) {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				type: 'post',
				url: basurl + '/mesterClassAmt',
				data: {
					class_type_id: class_type_id
				},
				//dataType: 'json',
				success: function(data) {
                if(data != ""){
                     $('.mesterClassAmt').show();
                    	$('.mesterClassAmt').html(data);

                }else{
                   $('.mesterClassAmt').hide();
                   $('#class_type_id').val("");
                    alert('Please assign master fees*!');
                      window.open(basurl+'/feesMasterAdd', 'blank');
                  
                    			
                }

				}
			});
		} else {
			toastr.error('Please put a value in one column !');
		}
	};
		function sum_amount(amot) {
		    var sum = 0;
    
            sum += amot;
       
            $("#net_amount").val(sum.toFixed(2));
		}
		
		
		
	function SearchValue() {
		var basurl = "{{ url('/') }}";
		var name = $('#searchName').val();
		var registration_no = $('#registration_no').val();
		var class_search_id = $('#class_search_id :selected').val();
		if (class_search_id > 0 || registration_no != '' || name != '') {
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				type: 'post',
				url: basurl + '/admissionStudentSearch',
				data: {
					class_search_id: class_search_id,
					name: name,
					registration_no: registration_no
				},
				//dataType: 'json',
				success: function(data) {
                    $('.student_list_show').addClass('fadeinout');
					$('.student_list_show').html(data);
                setTimeout(function() {
                         $('.student_list_show').removeClass('fadeinout');
                     }, 9000);
				}
			});
		} else {
			toastr.error('Please put a value in one column !');
		}
	};
    
 

function showData(student_id) {

    var basurl = "{{ url('/') }}";

    $.ajax({

        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        type: 'POST',

        url: basurl + '/admissionStudentOnClick',

        data: {
            student_id: student_id
        },

        dataType: 'json',

        success: function(data) {

            /*
            |--------------------------------------------------------------------------
            | Already Admitted Check
            |--------------------------------------------------------------------------
            */
            if (data.status === 'already_admitted') {
                toastr.error(data.message,'Already Admitted');
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Student Not Found / Error
            |--------------------------------------------------------------------------
            */

            if (data.status === 'error') {

                alert(data.message);

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Student Data
            |--------------------------------------------------------------------------
            */

            if (data.status === 'success' && data.stu_data) {

                if (data.stu_data.status != 1) {

                    /*
                    |--------------------------------------------------------------------------
                    | Student Basic Details
                    |--------------------------------------------------------------------------
                    */

                    $('#reg_id').val(
                        data.stu_data.id || ''
                    );

                    $('#first_name').val(
                        data.stu_data.first_name || ''
                    );

                    $('#aadhaar').val(
                        data.stu_data.aadhaar || ''
                    );

                    $('#student_id').val(
                        data.stu_data.name || ''
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Gender
                    |--------------------------------------------------------------------------
                    */

                    $('#gender_id')
                        .val(data.stu_data.gender_id || '')
                        .trigger('change');


                    /*
                    |--------------------------------------------------------------------------
                    | Class
                    |--------------------------------------------------------------------------
                    */

                    $('#class_type_id')
                        .val(data.stu_data.class_type_id || '')
                        .trigger('change');


                    /*
                    |--------------------------------------------------------------------------
                    | Other Details
                    |--------------------------------------------------------------------------
                    */

                    $('#dob').val(
                        data.stu_data.dob || ''
                    );

                    $('#mobile').val(
                        data.stu_data.mobile || ''
                    );

                    $('#email').val(
                        data.stu_data.email || ''
                    );

                    $('#father_name').val(
                        data.stu_data.father_name || ''
                    );

                    $('#mother_name').val(
                        data.stu_data.mother_name || ''
                    );

                    $('#father_mobile').val(
                        data.stu_data.father_mobile || ''
                    );

                    $('#address').val(
                        data.stu_data.previous_school || ''
                    );

                    $('#remark_1').val(
                        data.stu_data.remark_1 || ''
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Images
                    |--------------------------------------------------------------------------
                    */

                    $('#student_img').val(
                        data.stu_data.student_img || ''
                    );

                    $('#father_img').val(
                        data.stu_data.father_img || ''
                    );

                    $('#mother_img').val(
                        data.stu_data.mother_img || ''
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Other Data
                    |--------------------------------------------------------------------------
                    */

                    mesterData();

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | Clear Fields
                    |--------------------------------------------------------------------------
                    */

                    $(
                        '#reg_id,' +
                        '#first_name,' +
                        '#last_name,' +
                        '#aadhaar,' +
                        '#student_id,' +
                        '#dob,' +
                        '#mobile,' +
                        '#email,' +
                        '#father_name,' +
                        '#mother_name,' +
                        '#father_mobile,' +
                        '#address,' +
                        '#remark_1,' +
                        '#student_img,' +
                        '#father_img,' +
                        '#mother_img'
                    ).val('');


                    /*
                    |--------------------------------------------------------------------------
                    | Clear Select2 Fields
                    |--------------------------------------------------------------------------
                    */

                    $('#gender_id')
                        .val('')
                        .trigger('change');

                    $('#class_type_id')
                        .val('')
                        .trigger('change');

                }

            } else {

                alert('No more records found');

            }

        },

        error: function(xhr) {

            console.log(xhr.responseText);

            alert('Something went wrong while loading student data.');

        }

    });
}
</script>


<style>
/*    .fadeinout*/
/*{*/
/*  animation: fadeinout 1s infinite;*/
/*}*/

/*@keyframes fadeinout*/
/*{*/
/*  0%{*/
/*    opacity:0;*/
/*  }*/
/*  50%*/
/*  {*/
/*    opacity:1;*/
/*  }*/
/*  100%*/
/*  {*/
/*    opacity:0;*/
/*  }*/
/*}*/
</style>



@endsection
