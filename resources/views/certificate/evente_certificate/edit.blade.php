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
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('certificate.Edit Event Certificate') }}</h3>
							<div class="card-tools"> <a href="{{url('evente/certificate/index')}}" class="btn btn-primary  btn-sm {{ Helper::permissioncheck(5)->view ? '' : 'd-none' }}"><i class="fa fa-eye"></i> {{ __('common.View') }}</a>
							 <a href="{{url('certificate_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a> </div>
						</div>
            <div class="card-body">
                <form id="quickForm" action="{{ url('evente/certificate/edit') }}/{{($data->id)}}" method="post" >
                     @csrf
             <!--    <div class="row">
                     
                    <div class="col-md-6">
			<div class="form-group">
				<label> Search Student</label>
				<select type="text" name="student" id="student" class="form-control @error('student') is-invalid @enderror" value="{{$data->student}}" onchange="student_data()">
               @if(!empty($getstudents)) 
                      @foreach($getstudents as $students)
                         <option value="{{ $students->id ?? ''  }}" >{{ $students->name ?? ''  }}</option>
                      @endforeach
                  @endif
                  
                  	@error('student')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                </select>
		    </div>
		</div>
                </div>  -->   
                
                <div class="row">
                    <div class="col-md-3">
			<div class="form-group">
				<label style="color:red;">{{ __('certificate.Admission No.') }}*</label>
				<input type="hidden" name="admission_id" id="admission_id" class="form-control" readonly="readonly" placeholder="{{ __('certificate.Admission No.') }}"  value="{{$data->admission_id}}">
				<input type="text" name="registration_no" id="registration_no" class="form-control" readonly="readonly" placeholder="{{ __('certificate.Admission No.') }}"  value="{{$data->admissionNo}}">
                
		    </div>
		</div>
                    <div class="col-md-3">
			<div class="form-group">
				<label style="color:red;">{{ __('certificate.Student Name') }}*</label>
				<input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" readonly="readonly" placeholder="{{ __('certificate.Student Name') }}"  value="{{$data->stu_name}}">
                	@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
                    <div class="col-md-3">
			<div class="form-group">
				<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
				<input type="text" name="father_name" id="father_name" class="form-control @error('father_name') is-invalid @enderror" readonly="readonly" placeholder="{{ __('common.Fathers Name') }}"  value="{{$data['stu_father_name'] ?? '' }}">
                	@error('father_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
	<!--	<div class="col-md-3">
			<div class="form-group">
				<label style="color:red;">Student Roll No.*</label>
				<input type="text" name="student_roll_no" id="student_roll_no" class="form-control @error('student_roll_no') is-invalid @enderror" placeholder="Student Roll No."  value="{{$data->student_roll_no}}" maxlength="6" onkeypress="javascript:return isNumber(event)">
                	@error('student_roll_no')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>-->
		
		<div class="col-md-3">
			<div class="form-group">
				<label>{{ __('Held On') }}</label>
				<input type="date" name="organized_date" id="organized_date" class="form-control @error('organized_date') is-invalid @enderror"  value="{{$data->organized_date}}">
                	@error('organized_date')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
                
 <!--       <div class="col-md-3">
			<div class="form-group">
				<label> {{ __('certificate.Rank') }}</label>
				<input type="text" name="rank" id="rank" class="form-control @error('rank') is-invalid @enderror" placeholder="{{ __('certificate.Rank') }}"  value="{{$data->rank}}">
                	@error('rank')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>-->
	<div class="col-md-3">
			<div class="form-group">
				<label>{{ __('certificate.Event Type') }}</label>
				<input type="text" name="event_type" id="event_type" class="form-control @error('event_type') is-invalid @enderror" placeholder="{{ __('certificate.Event Type') }}"  value="{{$data->event_type}}">
                	@error('event_type')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
		    </div>
		</div>
            </div>
       
        <div class="row m-2">
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">{{ __('common.Update') }}</button>
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