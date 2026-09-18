@php
   $getstudents = Helper::getstudents();
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
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('Achievement Certificate') }}</h3>
							<div class="card-tools"> <a href="{{url('cc/form/index')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->view ? '' : 'd-none' }}"><i class="fa fa-eye"></i> {{ __('common.View') }}</a> 
							<a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a>
							</div>
						</div>
				
 
            <div class="card-body">
               <!-- <h3>Character Certificate (CC)</h3>
                <hr>-->
              
                <form id="quickForm" action="{{ url('cc/form/edit') }}/{{$data['id'] ?? '' }}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('certificate.Admission No.') }} *</label>
                                            <input type="hidden" name="admission_id" id="admissionNo" class="form-control" placeholder="{{ __('certificate.Admission No.') }}" readonly="readonly" value="{{ $data->admission_id ?? '' }}">
                                            <input type="text" name="registration_no" id="registration_no" class="form-control" placeholder="{{ __('certificate.Admission No.') }}" readonly="readonly" value="{{ $data->admissionNo ?? '' }}">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Student Name</label>
                                        <input type="text" id="student_name" readonly="readonly" class="form-control" placeholder="{{ __('certificate.Student Name') }}" value="{{ $data->stu_first_name ?? '' }} {{ $data->stu_last_name ?? '' }}">
                                    </div>
                                </div>
                    
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label style="color:red;">{{ __('certificate.Date') }} *</label>
                                        <input type="date" name="iessu_date" id="iessu_date" class="form-control @error('iessu_date') is-invalid @enderror" value="{{ $data->iessu_date ?? date('Y-m-d') }}">
                                        @error('iessu_date')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('Class') }} </label>
                                        <input type="text" name="class_name" id="class_name" class="form-control" value="{{ $data->class_name ?? '' }}" placeholder="Class Name" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>{{ __('Achievement For') }} </label>
                                        <input type="text" name="achievement_for" id="achievement_for" class="form-control" placeholder="{{ __('Achievement For') }}" value="{{ $data->achievement_for ?? '' }}">
                                        @error('achievement_for')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row m-2">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">{{ __('common.submit') }} </button>
                                </div>
                            </div>
                        </div>
                    </form>
    </div>
</div>
</div>
</div>
</div>
</section>
</div>
@endsection