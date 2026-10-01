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

@section('styles')
<style>
/* ==========================================================================
   ARISE ERP - ADD ADMISSION (same compact navy theme as Add User)
   ========================================================================== */

.admission-add-wrapper {
    background: #eef2f6;
    color: #0f172a;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    font-size: 12px;
    padding: 6px 10px 20px;
    min-height: calc(100vh - 56px);
}
.admission-add-wrapper * {
    box-sizing: border-box;
}

/* Top Hero Banner (Matching Dashboard & User View Hero) */
.adm-hero {
    background: linear-gradient(135deg, #002C54 0%, #0f3460 100%);
    color: #fff;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 3px rgba(0,44,84,.15);
    margin-bottom: 6px;
}
.adm-hero-text {
    display: flex;
    flex-direction: column;
}
.adm-kicker {
    font-size: 9.5px;
    text-transform: uppercase;
    letter-spacing: .06em;
    opacity: .85;
    display: block;
    margin-bottom: 1px;
    color: #93c5fd;
    font-weight: 600;
}
.adm-title {
    font-size: 15px;
    font-weight: 700;
    margin: 0;
    line-height: 1.2;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 6px;
}
.adm-subtitle {
    font-size: 10.5px;
    margin: 1px 0 0;
    opacity: .85;
    color: #cbd5e1;
}

/* Hero Action Buttons */
.dash-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    height: 27px;
    padding: 0 10px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    border: 1px solid transparent;
    cursor: pointer;
    text-decoration: none !important;
    transition: all .15s ease-in-out;
    white-space: nowrap;
}
.dash-btn-light {
    background: #ffffff;
    color: #002C54 !important;
    border-color: #ffffff;
}
.dash-btn-light:hover {
    background: #f1f5f9;
    color: #001f3d !important;
}
.dash-btn-outline {
    background: transparent;
    color: #ffffff !important;
    border-color: rgba(255,255,255,.35);
}
.dash-btn-outline:hover {
    background: rgba(255,255,255,.15);
    border-color: rgba(255,255,255,.6);
}

/* Sharp Compact Form Cards (Matching .dash-card in Dashboard) */
.adm-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
    display: flex;
    flex-direction: column;
    margin-bottom: 6px;
}
.adm-card-header {
    padding: 5px 10px;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.adm-card-title {
    font-size: 12px;
    font-weight: 700;
    margin: 0;
    color: #002C54;
    line-height: 1.2;
    display: flex;
    align-items: center;
    gap: 6px;
}
.adm-card-title .card-step {
    background: #002C54;
    color: #fff;
    font-size: 9px;
    font-weight: 700;
    padding: 1px 5px;
    border-radius: 2px;
    display: inline-block;
}
.adm-card-desc {
    font-size: 10px;
    color: #64748b;
    margin: 0;
}
.adm-card-body {
    padding: 8px 10px;
}

/* Compact Form Grid & Controls */
.form-group-compact {
    margin-bottom: 6px;
}
.form-label-compact {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.form-label-compact .req-star {
    color: #ef4444;
    margin-left: 2px;
    font-weight: 700;
}
.form-label-compact .label-note {
    font-size: 9.5px;
    color: #64748b;
    font-weight: 400;
}

.form-control-compact,
.admission-add-wrapper .form-control {
    height: 29px;
    font-size: 11.5px;
    color: #0f172a;
    background-color: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 2px;
    padding: 3px 7px;
    box-shadow: none !important;
    transition: border-color .15s ease-in-out;
}
.form-control-compact:focus,
.admission-add-wrapper .form-control:focus {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}

.admission-add-wrapper .is-invalid,
.admission-add-wrapper .form-control.is-invalid {
    border-color: #ef4444 !important;
    background-image: none !important;
}
.admission-add-wrapper .invalid-feedback,
.admission-add-wrapper .error.invalid-feedback {
    font-size: 10px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    display: block;
}

/* Compact Select2 Styling (Sharp 2px radius) */
.admission-add-wrapper .select2-container--default .select2-selection--single,
.admission-add-wrapper .select2-container--default .select2-selection--multiple {
    border: 1px solid #cbd5e1 !important;
    border-radius: 2px !important;
    min-height: 29px !important;
    height: 29px;
    padding: 1px 6px;
}
.admission-add-wrapper .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 25px;
    font-size: 11.5px;
    color: #0f172a;
    padding-left: 2px;
}
.admission-add-wrapper .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 27px;
    right: 4px;
}
.admission-add-wrapper .select2-container--default.select2-container--focus .select2-selection--single,
.admission-add-wrapper .select2-container--default.select2-container--focus .select2-selection--multiple,
.admission-add-wrapper .select2-container--default.select2-container--open .select2-selection--single,
.admission-add-wrapper .select2-container--default.select2-container--open .select2-selection--multiple {
    border-color: #002C54 !important;
    box-shadow: 0 0 0 2px rgba(0, 44, 84, 0.1) !important;
}
.admission-add-wrapper .select2-container--default .select2-selection--multiple {
    height: auto;
    min-height: 29px;
    padding-bottom: 2px;
}
.admission-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice,
.select2-container--default .select2-selection--multiple .select2-selection__choice,
.select2-container .select2-selection--multiple .select2-selection__choice {
    background-color: #e0f2fe !important;
    background: #e0f2fe !important;
    border: 1px solid #7dd3fc !important;
    color: #0369a1 !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    border-radius: 2px !important;
    padding: 2px 7px !important;
    margin: 3px 4px 3px 0 !important;
    line-height: 1.4 !important;
    display: inline-flex !important;
    align-items: center !important;
}
.admission-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
.select2-container .select2-selection--multiple .select2-selection__choice__remove {
    color: #0284c7 !important;
    font-weight: 700 !important;
    font-size: 13px !important;
    line-height: 1 !important;
    margin-right: 5px !important;
    float: none !important;
    cursor: pointer !important;
    border: none !important;
}
.admission-add-wrapper .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover,
.select2-container .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #dc2626 !important;
}

/* Compact Document Upload Cards */
.compact-doc-card {
    border: 1px dashed #cbd5e1;
    border-radius: 2px;
    padding: 6px 8px;
    background: #f8fafc;
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 90px;
    cursor: pointer;
    transition: all .15s;
}
.compact-doc-card:hover {
    border-color: #002C54;
    background: #f0f7ff;
}
.compact-doc-card.has-file {
    border-style: solid;
    border-color: #10b981;
    background: #f0fdf4;
}
.compact-doc-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.compact-doc-icon {
    font-size: 16px;
    color: #002C54;
    margin-bottom: 2px;
}
.compact-doc-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.compact-doc-desc {
    font-size: 9.5px;
    color: #64748b;
    margin: 0;
}
.compact-doc-preview {
    display: none;
    margin-top: 4px;
    position: relative;
    z-index: 3;
}
.compact-doc-preview img {
    max-height: 48px;
    max-width: 100%;
    border-radius: 2px;
    border: 1px solid #cbd5e1;
}
.compact-doc-info {
    font-size: 9.5px;
    font-weight: 600;
    color: #0f172a;
    margin-top: 2px;
}
.compact-doc-remove {
    position: absolute;
    top: -5px;
    right: calc(50% - 30px);
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    font-size: 9px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}
.compact-doc-error {
    font-size: 9.5px;
    color: #ef4444;
    font-weight: 600;
    margin-top: 2px;
    margin-bottom: 0;
}

/* Bottom Action Footer Bar */
.adm-footer-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 2px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
    box-shadow: 0 1px 2px rgba(0,0,0,.02);
}
.btn-compact-submit {
    background: #002C54;
    color: #ffffff !important;
    border: 1px solid #002C54;
    height: 29px;
    padding: 0 18px;
    font-size: 11.5px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-submit:hover {
    background: #001f3d;
    border-color: #001f3d;
}
.btn-compact-reset {
    background: #f1f5f9;
    color: #475569 !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all .15s;
}
.btn-compact-reset:hover {
    background: #e2e8f0;
    color: #1e293b !important;
}
.btn-compact-cancel {
    background: #ffffff;
    color: #64748b !important;
    border: 1px solid #cbd5e1;
    height: 29px;
    padding: 0 12px;
    font-size: 11px;
    font-weight: 600;
    border-radius: 2px;
    display: inline-flex;
    align-items: center;
    text-decoration: none !important;
}
.btn-compact-cancel:hover {
    background: #f8fafc;
    color: #0f172a !important;
}

@media (max-width: 576px) {
    .adm-hero {
        flex-direction: column;
        align-items: flex-start;
        padding: 8px 10px;
    }
    .dash-btn {
        flex: 1;
    }
}

/* ---- Admission specific ---- */
.admission-add-wrapper .form-group-compact label,
.admission-add-wrapper .form-group-compact lable {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 2px;
    display: block;
}
.admission-add-wrapper .req-star { color: #ef4444; margin-left: 2px; font-weight: 700; }
.admission-add-wrapper select[multiple].form-control { height: auto; }
.admission-add-wrapper .form-check-label { font-size: 11px; font-weight: 500; }
.admission-add-wrapper #mobileValidationMessage,
.admission-add-wrapper #fathermobileValidationMessage { font-size: 10px !important; font-weight: 600; }
.compact-doc-card.has-error { border-color: #ef4444; }
#image_error, #image_er, #image_errors { font-weight: 600; font-size: 10px; }
.blink_me { animation: blinker 1s linear infinite; }
@keyframes blinker { 50% { opacity: 0; } }
</style>
@endsection

@section('content')

@php
    $studentCount = DB::table('admissions')->where('deleted_at',null)->count();
@endphp

<div class="content-wrapper admission-add-wrapper">

    {{-- 1. Top Hero Banner (Dark Navy Theme - same as Add User) --}}
    <div class="adm-hero">
        <div class="adm-hero-text">
            <span class="adm-kicker"><i class="fa fa-graduation-cap mr-1"></i> {{ __('student.Students Admission Management') }}</span>
            <h1 class="adm-title"><i class="fa fa-user-plus mr-1"></i> {{ __('student.Add New Admission') }}</h1>
            <p class="adm-subtitle">Search a registered student or fill the admission form below</p>
        </div>
        <div class="d-flex align-items-center gap-1 flex-wrap">
            <a href="{{ url('admissionView') }}" class="dash-btn dash-btn-light {{ Helper::permissioncheck(3)->view ? '' : 'd-none' }}" title="View Admissions">
                <i class="fa fa-list"></i> {{ __('common.View') }} Admissions
            </a>
            <a href="{{ url('studentsDashboard') }}" class="dash-btn dash-btn-outline" title="Dashboard">
                <i class="fa fa-th-large"></i> Dashboard
            </a>
        </div>
    </div>

    {{-- 2. Admission Form --}}
    <form id="form-submit" action="{{ url('admissionAdd') }}" method="post" enctype="multipart/form-data">
        @csrf

                {{-- Card 01: Student &amp; Admission Details --}}
                <div class="adm-card {{ ($student_fields->intersect(['admissionNo','ledger_no','student_pen','apaar_id','family_id','first_name','aadhaar','jan_aadhaar','gender_id','dob','mobile','email','class_type_id','admission_type_id','religion','category','caste_category','blood_group','medium','admission_date','house','height','weight','remark_1','school_namestudied_last_year','previous_school'])->isEmpty() && $studentFields_new->isEmpty()) ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">01</span> <i class="fa fa-graduation-cap text-info"></i> Student &amp; Admission Details</h3>
                        <span class="adm-card-desc">Admission identity, class and personal information</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('admissionNo') ? '' : 'd-none' }}">
                                            <div class="form-group form-group-compact">
                                                <label>{{ __('student.Admission No.') }}
                                                @if($student_fields_required['admissionNo'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                            </label>
                                                <input type="text" class="form-control"  id="admissionNo"name="admissionNo"placeholder="{{ __('student.Admission No.') }}" value="{{ $BillCounter ?? '' }}" 
                                                       onkeypress="return isNumber(event)"
                                                      >
                                            
                                            </div>
                                        </div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2  {{ $student_fields->contains('ledger_no') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Ledger No') }}
										 @if($student_fields_required['ledger_no'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " name="ledger_no" placeholder="{{ __('Ledger No') }}"  >
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('student_pen') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Student Pen
										 @if($student_fields_required['student_pen'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="student_pen" name="student_pen" placeholder="Student Pen" onkeypress="javascript:return isNumber(event)" maxlength="11">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('apaar_id') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Apaar Id
										    @if($student_fields_required['apaar_id'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="apaar_id" name="apaar_id" placeholder="Apaar Id" onkeypress="javascript:return isNumber(event)" maxlength="12">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('family_id') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Family ID') }}
										@if($student_fields_required['family_id'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="family_id" name="family_id" placeholder="{{ __('Family ID') }}" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('first_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Student Name') }}
										@if($student_fields_required['first_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="first_name" id="first_name" class="form-control invalid " value="{{ old('first_name') }}" placeholder="{{ __('Student Name') }}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('aadhaar') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Aadhaar No.') }}
										@if($student_fields_required['aadhaar'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="aadhaar" name="aadhaar" placeholder=" {{ __('common.Aadhaar No.') }}" value="{{old('aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('jan_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Jan Aadhaar No.') }}
										@if($student_fields_required['jan_aadhaar'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="jan_aadhaar" name="jan_aadhaar" placeholder=" {{ __('Jan Aadhaar No.') }}" value="{{old('jan_aadhaar')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('gender_id') ? '' : 'd-none' }}">
    <div class="form-group form-group-compact">

        <label>
            {{ __('common.Gender') }}

            @if($student_fields_required['gender_id'] == 0)
                <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('dob') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Date Of  Birth') }}
										@if($student_fields_required['dob'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="date" class="form-control invalid" id="dob" name="dob" placeholder=" Date Of  Birth" value="{{old('dob')}}">
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mobile') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Mobile No.') }}
										@if($student_fields_required['mobile'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
				                        <div id="mobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>


									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('email') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.E-Mail') }}
										@if($student_fields_required['email'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="email" class="form-control" id="email" name="email" placeholder="{{ __('common.E-Mail') }}" value="{{old('email')}}">
							          
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('class_type_id') ? '' : 'd-none' }}">
    <div class="form-group form-group-compact">

        <label>
            {{ __('common.Class') }}

            @if($student_fields_required['class_type_id'] == 0)
                <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 " id="stream_subject_div" style="display:none;">
									<div class="form-group form-group-compact">
										<label>Stream Subject
									</label>

										<select class="form-control select2" multiple id="stream_subject" name="stream_subject[]">
											<option value="">{{ __('common.Select') }}</option>
										</select>
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('admission_type_id') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Admission Type(Non RTE)
										@if($student_fields_required['admission_type_id'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<select class="form-control invalid select2" id="admission_type_id" name="admission_type_id">
											<option value="">{{ __('common.Select') }}</option>
											<option value="1" {{ (1 == old('admission_type_id')) ? 'selected' : 'selected' }}>Yes</option>
											<option value="2" {{ (2 == old('admission_type_id')) ? 'selected' : '' }}>NO</option>
										</select>
									  
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('religion') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Religion
										@if($student_fields_required['religion'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('category') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Category
										@if($student_fields_required['category'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('caste_category') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Caste') }}@if($student_fields_required['caste_category'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="caste_category" name="caste_category" placeholder="{{ __('Caste') }}" value="{{old('caste_category')}}" >
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('blood_group') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Blood Group') }}@if($student_fields_required['blood_group'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('medium') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Medium
										@if($student_fields_required['medium'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="medium" name="medium">
											<option value="">Select</option>
											<option value="Hindi">Hindi</option>
											<option value="English">English</option>
										</select>
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('admission_date') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('student.Date Of Admission') }}
										@if($student_fields_required['admission_date'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="date" class="form-control" id="admission_date" name="admission_date" value="{{date('Y-m-d')}}">
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('house') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('House') }}
										@if($student_fields_required['house'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="house" id="house" class="form-control" value="" placeholder="{{ __('House') }}">
										
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('height') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Height') }}
										@if($student_fields_required['height'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="height" id="height" class="form-control" value="" placeholder="{{ __('Height') }}">
										
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('weight') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Weight') }}
										@if($student_fields_required['weight'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="weight" id="weight" class="form-control" value="" placeholder="{{ __('Weight') }}">
										
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('remark_1') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('student.Remark') }}
										@if($student_fields_required['remark_1'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="remark_1" name="remark_1" placeholder="{{ __('student.Remark') }} " value="{{old('remark_1')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('school_namestudied_last_year') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('School Studied Last Year') }}
										@if($student_fields_required['school_namestudied_last_year'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="school_namestudied_last_year" id="school_namestudied_last_year" class="form-control" value="" placeholder="{{ __('School Studied Last Year') }}">
										
									</div>
								</div>
<div class="col-12 col-md-6 col-lg-4 {{ $student_fields->contains('previous_school') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Name  And  Address Of Previous School
										@if($student_fields_required['previous_school'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="previous_school" name="previous_school" placeholder="Name  And  Address Of Previous School" value="{{old('previous_school')}}">
									</div>
								</div>
@foreach($studentFields_new as $field)
                                    <div class="col-12 col-md-{{ $field->grid_column }}">
                                        <div class="form-group form-group-compact">
                                            <label>
                                                {{ $field->field_label }}
                                                @if($field->required == 0) 
                                                    <span class="req-star">*</span>
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
                </div>

                {{-- Card 02: Address Details --}}
                <div class="adm-card {{ ($student_fields->intersect(['country','state','city','district','tehsil','village_city','address','pincode'])->isEmpty()) ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">02</span> <i class="fa fa-map-marker text-info"></i> Address Details</h3>
                        <span class="adm-card-desc">Residential address of the student</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('country') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Country') }}
										@if($student_fields_required['country'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('state') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label for="State" class="required">{{ __('common.State') }}
										@if($student_fields_required['state'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('city') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label for="City">{{ __('common.City') }}
										@if($student_fields_required['city'] == 0)
                                                    <span class="req-star">*</span>
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
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('district') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>District
										@if($student_fields_required['district'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="district" name="district" placeholder="District" value="{{old('district')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('tehsil') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Tehsil
										@if($student_fields_required['tehsil'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="tehsil" name="tehsil" placeholder="Tehsil" value="{{old('tehsil')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('village_city') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('student.Village/City') }}
										@if($student_fields_required['village_city'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="village_city" name="village_city" placeholder="{{ __('student.Village/City') }}" value="{{old('village_city')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('address') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('student.Students Address') }}
										@if($student_fields_required['address'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control " id="address" name="address" placeholder="{{ __('student.Students Address') }}" value="{{old('address')}}">
										
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('pincode') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Pin Code') }}
										@if($student_fields_required['pincode'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="pincode" name="pincode" placeholder="{{ __('common.Pin Code') }}" value="{{old('pincode')}}" maxlength="6" onkeypress="javascript:return isNumber(event)">
										
									</div>
								</div>
                        </div>
                    </div>
                </div>

                {{-- Card 03: Parents &amp; Guardian Details --}}
                <div class="adm-card {{ ($student_fields->intersect(['father_name','father_mobile','father_aadhaar','father_occupation','father_pancard','mother_name','mother_mob','mother_aadhaar','mother_occupation','mother_pancard','guardian_name','guardian_mobile','relation_student','family_annual_income','bpl','bpl_certificate_no'])->isEmpty()) ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">03</span> <i class="fa fa-users text-info"></i> Parents &amp; Guardian Details</h3>
                        <span class="adm-card-desc">Father, mother and guardian information</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('father_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Fathers Name') }}
										@if($student_fields_required['father_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{old('father_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
										</select>
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('father_mobile') ? '' : 'd-none' }}">  
									<div class="form-group form-group-compact">
										<label>{{ __('common.Fathers Contact No') }}
										@if($student_fields_required['father_mobile'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="father_mobile" name="father_mobile" placeholder="{{ __('common.Fathers Contact No') }}" value="{{old('father_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
										 <div id="fathermobileValidationMessage" style="color: red; display: none; font-size:13px;">must be at least 10 characters</div>

								
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('father_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Fathers Aadhaar') }}
										@if($student_fields_required['father_aadhaar'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                    </label>
										<input type="text" class="form-control" id="father_aadhaar" name="father_aadhaar" placeholder="{{ __('Fathers Aadhaar') }}" value="{{old('father_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('father_occupation') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Father Occupation
										@if($student_fields_required['father_occupation'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                    </label>
										<input type="text" class="form-control" id="father_occupation" name="father_occupation" placeholder="Father Occupation" value="{{old('father_occupation')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('father_pancard') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Father's Pancard
										@if($student_fields_required['father_pancard'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="father_pancard" name="father_pancard" placeholder="Father's Pancard" value="{{old('father_pancard')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mother_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('common.Mothers Name') }}@if($student_fields_required['mother_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control invalid" id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{old('mother_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mother_mob') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Mother Mobile No') }}
										@if($student_fields_required['mother_mob'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_mob" name="mother_mob" placeholder="{{ __('Mother Mobile No') }}" value="{{old('mother_mob')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mother_aadhaar') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Mothers Aadhaar') }}
										@if($student_fields_required['mother_aadhaar'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_aadhaar" name="mother_aadhaar" placeholder="{{ __('Mothers Aadhaar') }}" value="{{old('mother_aadhaar')}}" maxlength="12" onkeypress="javascript:return isNumber(event)">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mother_occupation') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Mother Occupation
										@if($student_fields_required['mother_occupation'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="mother_occupation" name="mother_occupation" placeholder="Mother Occupation" value="{{old('mother_occupation')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('mother_pancard') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Mother's Pancard
										@if($student_fields_required['mother_pancard'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="mother_pancard" name="mother_pancard" placeholder="Mother's Pancard" value="{{old('mother_pancard')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('guardian_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Guardian Name') }}
										@if($student_fields_required['guardian_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control" id="guardian_name" name="guardian_name" placeholder="{{ __('Guardian Name') }}" value="{{old('guardian_name')}}" onkeydown="return /[a-zA-Z ]/i.test(event.key)">
									
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('guardian_mobile') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Guardian Mobile No') }}
										@if($student_fields_required['guardian_mobile'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                                </label>
										<input type="text" class="form-control " id="guardian_mobile" name="guardian_mobile" placeholder="{{ __('Guardian Mobile No') }}" value="{{old('guardian_mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
								
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('relation_student') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Relation with the student') }}
										@if($student_fields_required['relation_student'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="relation_student" id="relation_student" class="form-control" value="" placeholder="{{ __('Relation with the student') }}">
										
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('family_annual_income') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Family Annual Income') }}
										@if($student_fields_required['family_annual_income'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" name="family_annual_income" id="family_annual_income" class="form-control" value="" placeholder="{{ __('Family Annual Income') }}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bpl') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>BPL
										@if($student_fields_required['bpl'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="bpl" name="bpl">
										    <option value="">Select</option>
										    <option value="Yes">Yes</option>
										    <option value="No">No</option>
										</select>
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bpl_certificate_no') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>BPL Cetificate No.
										@if($student_fields_required['bpl_certificate_no'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bpl_certificate_no" name="bpl_certificate_no" placeholder="BPL Cetificate No." value="{{old('bpl_certificate_no')}}">
									</div>
								</div>
                        </div>
                    </div>
                </div>

                {{-- Card 04: Transport Details --}}
                <div class="adm-card {{ ($student_fields->intersect(['transport','bus_number','bus_route','stoppage','transpor_charges'])->isEmpty()) ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">04</span> <i class="fa fa-bus text-info"></i> Transport Details</h3>
                        <span class="adm-card-desc">Bus facility, route and charges</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('transport') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Transport') }}
										@if($student_fields_required['transport'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<select class="form-control select2" id="transport" name="transport">
											<option value="Yes" {{ ('Yes' == old('transport')) ? 'selected' : 'selected' }}>{{ __('Yes') }}</option>
											<option value="No" {{ ('No' == old('transport')) ? 'selected' : '' }}>{{ __('No') }}</option>
										</select>
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bus_number') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Bus Number') }}
										@if($student_fields_required['bus_number'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bus_number" name="bus_number" placeholder="{{ __('Bus Number') }} " value="{{old('bus_number')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bus_route') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Bus Route') }}
										@if($student_fields_required['bus_route'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bus_route" name="bus_route" placeholder="{{ __('Bus Route') }} " value="{{old('bus_route')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('stoppage') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Stoppage') }}
										@if($student_fields_required['stoppage'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="stoppage" name="stoppage" placeholder="{{ __('Stoppage') }} " value="{{old('stoppage')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('transpor_charges') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Transpor Charges') }}
										@if($student_fields_required['transpor_charges'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="transpor_charges" name="transpor_charges" placeholder="{{ __('Transpor Charges') }} " value="{{old('transpor_charges')}}">
									</div>
								</div>
                        </div>
                    </div>
                </div>

                {{-- Card 05: Bank Details --}}
                <div class="adm-card {{ ($student_fields->intersect(['bank_name','bank_account','bank_account_holder','branch_name','ifsc','micr_code'])->isEmpty()) ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">05</span> <i class="fa fa-university text-info"></i> Bank Details</h3>
                        <span class="adm-card-desc">Student / parent bank account information</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bank_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Bank Name') }}
										@if($student_fields_required['bank_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="{{ __('Bank Name') }} " value="{{old('bank_name')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bank_account') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Bank Account') }}
										@if($student_fields_required['bank_account'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_account" name="bank_account" placeholder="{{ __('Bank Account') }} " value="{{old('bank_account')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('bank_account_holder') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>Bank Account Holder
										@if($student_fields_required['bank_account_holder'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="bank_account_holder" name="bank_account_holder" placeholder="Bank Account Holder" value="{{old('bank_account_holder')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('branch_name') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('Branch Name') }}
										@if($student_fields_required['branch_name'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="branch_name" name="branch_name" placeholder="{{ __('Branch Name') }} " value="{{old('branch_name')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('ifsc') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __('IFSC') }}
										@if($student_fields_required['ifsc'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="ifsc" name="ifsc" placeholder="{{ __('IFSC') }} " value="{{old('ifsc')}}">
									</div>
								</div>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 {{ $student_fields->contains('micr_code') ? '' : 'd-none' }}">
									<div class="form-group form-group-compact">
										<label>{{ __(' MICR Code') }}
										@if($student_fields_required['micr_code'] == 0)
                                                    <span class="req-star">*</span>
                                                @endif
                                        </label>
										<input type="text" class="form-control" id="micr_code" name="micr_code" placeholder="{{ __('MICR Code') }} " value="{{old('micr_code')}}">
									</div>
								</div>
                        </div>
                    </div>
                </div>

                {{-- Card 06: Documents --}}
                <div class="adm-card {{ $student_fields->intersect(['student_img','father_img','mother_img'])->isEmpty() ? 'd-none' : '' }}">
                    <div class="adm-card-header">
                        <h3 class="adm-card-title"><span class="card-step">06</span> <i class="fa fa-camera text-info"></i> {{ __('student.Document Upload') }}</h3>
                        <span class="adm-card-desc">Photographs (JPG, PNG under 2MB)</span>
                    </div>
                    <div class="adm-card-body">
                        <div class="row">
                            <div class="col-6 col-md-3 mb-2 {{ $student_fields->contains('student_img') ? '' : 'd-none' }}">
                                <div class="compact-doc-card" id="card_student_img">
                                    <input type="file" class="compact-doc-input adm-doc-input" id="student_img" name="student_img" accept="image/png, image/jpg, image/jpeg" data-card="card_student_img" data-error="image_error">
                                    <i class="fa fa-user compact-doc-icon"></i>
                                    <p class="compact-doc-title">{{ __('student.Student Photo') }} @if(($student_fields_required['student_img'] ?? 1) == 0)<span class="req-star">*</span>@endif</p>
                                    <span class="compact-doc-desc">Click to choose photo</span>
                                    <div class="compact-doc-preview">
                                        <img src="" alt="Preview">
                                        <button type="button" class="compact-doc-remove adm-doc-remove" data-input="student_img"><i class="fa fa-times"></i></button>
                                    </div>
                                    <div class="compact-doc-info"></div>
                                    <p class="compact-doc-error" id="image_error"></p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 {{ $student_fields->contains('father_img') ? '' : 'd-none' }}">
                                <div class="compact-doc-card" id="card_father_img">
                                    <input type="file" class="compact-doc-input adm-doc-input" id="father_img" name="father_img" accept="image/png, image/jpg, image/jpeg" data-card="card_father_img" data-error="image_errors">
                                    <i class="fa fa-male compact-doc-icon"></i>
                                    <p class="compact-doc-title">{{ __('student.Father Photo') }} @if(($student_fields_required['father_img'] ?? 1) == 0)<span class="req-star">*</span>@endif</p>
                                    <span class="compact-doc-desc">Click to choose photo</span>
                                    <div class="compact-doc-preview">
                                        <img src="" alt="Preview">
                                        <button type="button" class="compact-doc-remove adm-doc-remove" data-input="father_img"><i class="fa fa-times"></i></button>
                                    </div>
                                    <div class="compact-doc-info"></div>
                                    <p class="compact-doc-error" id="image_errors"></p>
                                </div>
                            </div>
                            <div class="col-6 col-md-3 mb-2 {{ $student_fields->contains('mother_img') ? '' : 'd-none' }}">
                                <div class="compact-doc-card" id="card_mother_img">
                                    <input type="file" class="compact-doc-input adm-doc-input" id="mother_img" name="mother_img" accept="image/png, image/jpg, image/jpeg" data-card="card_mother_img" data-error="image_er">
                                    <i class="fa fa-female compact-doc-icon"></i>
                                    <p class="compact-doc-title">{{ __('student.Mother Photo') }} @if(($student_fields_required['mother_img'] ?? 1) == 0)<span class="req-star">*</span>@endif</p>
                                    <span class="compact-doc-desc">Click to choose photo</span>
                                    <div class="compact-doc-preview">
                                        <img src="" alt="Preview">
                                        <button type="button" class="compact-doc-remove adm-doc-remove" data-input="mother_img"><i class="fa fa-times"></i></button>
                                    </div>
                                    <div class="compact-doc-info"></div>
                                    <p class="compact-doc-error" id="image_er"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

        {{-- 3. Bottom Action Footer Bar --}}
        <div class="adm-footer-bar">
            <div>
                <button type="button" class="btn-compact-reset" id="btn-admission-reset">
                    <i class="fa fa-refresh"></i> Reset Fields
                </button>
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ url('admissionView') }}" class="btn-compact-cancel">Cancel</a>
                @if(Session::get('student_count') >= Session::get('register_student'))
                    <button type="submit" class="btn-compact-submit btn-submit" id="submitButton">{{ __('common.submit') }}</button>
                @endif
            </div>
        </div>
    </form>
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
// Document upload cards (preview + JPG/PNG + 2MB check)
$(document).ready(function () {
    var maxSize = (typeof Img_Size !== 'undefined') ? Img_Size : (2 * 1024 * 1024);

    function clearDoc(input) {
        var $card = $('#' + input.data('card'));
        input.val('');
        $card.removeClass('has-file has-error');
        $card.find('.compact-doc-preview').hide().find('img').attr('src', '');
        $card.find('.compact-doc-info').text('');
    }

    $(document).on('change', '.adm-doc-input', function () {
        var input = $(this);
        var $card = $('#' + input.data('card'));
        var $err = $('#' + input.data('error'));
        $err.html('');
        $card.removeClass('has-error');
        var file = this.files && this.files[0];
        if (!file) { clearDoc(input); return; }

        var ext = (file.name.split('.').pop() || '').toLowerCase();
        if (['png', 'jpg', 'jpeg'].indexOf(ext) === -1) {
            $err.html('Only JPG / PNG image allowed');
            clearDoc(input); $card.addClass('has-error'); return;
        }
        if (file.size > maxSize) {
            $err.html('please select Image Size under 2MB');
            clearDoc(input); $card.addClass('has-error'); return;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $card.find('.compact-doc-preview').show().find('img').attr('src', e.target.result);
        };
        reader.readAsDataURL(file);
        $card.addClass('has-file');
        $card.find('.compact-doc-info').text(file.name.length > 22 ? file.name.substring(0, 19) + '...' : file.name);
    });

    $(document).on('click', '.adm-doc-remove', function (e) {
        e.preventDefault(); e.stopPropagation();
        var input = $('#' + $(this).data('input'));
        clearDoc(input);
        $('#' + input.data('error')).html('');
    });

    // Reset button: native reset + refresh select2 + clear previews
    $('#btn-admission-reset').on('click', function () {
        var form = document.getElementById('form-submit');
        form.reset();
        $(form).find('select').trigger('change.select2');
        $('.adm-doc-input').each(function () { clearDoc($(this)); });
        $('.compact-doc-error').html('');
    });
});
</script>
<script>
$(document).ready(function(){
    $('#class_type_id').val('');
});
</script>

@endsection
