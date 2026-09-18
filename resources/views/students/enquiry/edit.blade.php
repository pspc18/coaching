@php
$getTypeclass = Helper::classType();
$getgenders = Helper::getgender();
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
            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('student.Edit Students Enquiry') }} </h3>
            <div class="card-tools">
            <a href="{{url('enquiryView')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-eye"></i> {{ __('View') }} </a>
            <a href="{{url('studentsDashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
            </div>
            
            </div>         
        <form id="form-submit-edit" action="{{ url('enquiryEdit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">   
         @csrf
    <div class="row m-2">
	
		<div class="col-md-4">
	    	<div class="form-group">
				<label style="color:red;">{{ __('Full Name') }}*</label>
				<input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="{{ __('common.First Name') }}" value="{{ $data->first_name ?? old('first_name') }}">
		         @error('first_name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
		

		 <div class="col-md-4">
                 	<div class="form-group">
				<label style="color:red;">{{ __('Mobile') }}*</label>
				<input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="{{ __('student.Mobile') }}" value="{{ $data->mobile ?? old('mobile') }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
				 @error('mobile')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
            </div>
            
            
            	    <div class="col-md-4">
	    	<div class="form-group">
                  <label style="color:red;">{{ __('common.Gender') }}*</label>
                  <select class="form-control @error('gender_id') is-invalid @enderror" id="gender_id" name="gender_id">
    				<option value="">{{ __('common.Select') }}</option>
                    @if(!empty($getgenders)) 
                          @foreach($getgenders as $value)
                             <option value="{{ $value->id }}" {{ $value->id == old('gender_id', $data->gender_id) ? 'selected' : ''}}>{{ $value->name ?? ''  }}</option>
                          @endforeach
                      @endif
                    </select>
                     @error('gender_id')
    					<span class="invalid-feedback" role="alert">
    						<strong>{{ $message }}</strong>
    					</span>
    				@enderror
                </div>
		</div>
		
		<div class="col-md-4">
	    	<div class="form-group">
				<label>{{ __('common.DOB') }}</label>
				<input type="date" class="form-control" id="dob" name="dob" placeholder="{{ __('common.DOB') }}" value="{{ $data->dob ?? old('dob') }}">
		    </div>
		  </div>
		  
		  <div class="col-md-4">
	    	<div class="form-group">
				<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
				<input type="text" class="form-control @error('father_name') is-invalid @enderror" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{ $data->father_name ?? old('father_name') }}">
				@error('father_name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>	
        <div class="col-md-4">
	    	<div class="form-group">
				<label style="color:red;">{{ __('common.Mothers Name') }}*</label>
				<input type="text" class="form-control @error('mother_name') is-invalid @enderror" id="mother_name" name="mother_name" placeholder="{{ __('common.Mothers Name') }}" value="{{ $data->mother_name ?? old('mother_name') }}">
				 @error('mother_name')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
		</div>
	    
	    <div class="col-md-4">
			<div class="form-group">
				<label>{{ __('common.Class') }}</label>
				<select class="select2 form-control" id="class_type_id" name="class_type_id">
				   <option value="">{{ __('common.Select') }}</option>
                 @if(!empty($getTypeclass)) 
                      @foreach($getTypeclass as $type)
                         <option value="{{ $type->id ?? ''  }}" {{ $type->id == old('class_type_id', $data->class_type_id) ? 'selected' : ''}}>{{ $type->name ?? ''  }}</option>
                      @endforeach
                  @endif
                </select>
		    </div>
		</div>

      	<div class="col-md-4">
	    	<div class="form-group">
				<label>{{ __('common.E-Mail') }}</label>
				<input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ __('common.E-Mail') }}" value="{{ $data->email ?? old('email') }}">
		    </div>
		</div>
		
		 <div class="col-md-4">
	    	<div class="form-group">
			    <label>{{ __('No Of Child') }}</label>
			    <input type="tel" class="form-control" id="no_of_child" name="no_of_child" placeholder="{{ __('common.no_of_child') }}" value="{{ $data->no_of_child ?? old('no_of_child') }}" maxlength="10" minlength="10" onkeypress="javascript:return isNumber(event)">
	        </div>
	    </div>
	    
	    <div class="col-md-4">
  <div class="form-group">
    <label>{{ __('Assigned By') }}</label>
    <select class="select2 form-control" id="assigned_by" name="assigned_by">
        <option value="">{{ __('common.Select') }}</option>
        @if(!empty($users))
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $user->id == old('assigned_by', $data->assigned_by) ? 'selected' : '' }}>
                    {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}
                </option>
            @endforeach
        @endif
    </select>
  </div>
</div>


		
	    <div class="col-md-4 d-none">
			<div class="form-group">
				<label>{{ __('Reference') }}</label>
				<select class="select2 form-control" id="reference_id" name="reference_id">
    <option value="">{{ __('common.Select') }}</option>
    @if(!empty($references))
        @foreach($references as $ref)
            <option value="{{ $ref->id }}" {{ $ref->id == old('reference_id', $data->reference_id) ? 'selected' : '' }}>
                {{ $ref->name ?? '' }}
            </option>
        @endforeach
    @endif
</select>
		    </div>
		</div>
		
		
	    <div class="col-md-4 d-none">
			<div class="form-group">
				<label>{{ __('Response') }}</label>
				<select class="select2 form-control" id="response_id" name="response_id">
    <option value="">{{ __('common.Select') }}</option>
    @if(!empty($responses))
        @foreach($responses as $res)
            <option value="{{ $res->id }}" {{ $res->id == old('response_id', $data->response_id) ? 'selected' : '' }}>
                {{ $res->name ?? '' }}
            </option>
        @endforeach
    @endif
</select>
		    </div>
		</div>
	    
		 <div class="col-md-6">
	    	<div class="form-group">
				<label>{{ __('Students Previous School') }}</label>
				 <textarea class="form-control @error('previous_school') is-invalid @enderror" id="previous_school" name="previous_school"  placeholder="Previous School" rows="2" value="">{{ $data->previous_school ?? old('previous_school') }} </textarea>
				@error('previous_school')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
	    </div>
	    
		 <div class="col-md-6">
	    	<div class="form-group">
				<label>{{ __('Response') }}</label>
				 <textarea class="form-control @error('response') is-invalid @enderror" id="response" name="response"  placeholder="Response" rows="2" value="">{{ $data->response ?? old('response') }} </textarea>
				@error('response')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
	    </div>
	    
		 <div class="col-md-12">
	    	<div class="form-group">
				<label>{{ __('Note') }}</label>
				 <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note"  placeholder="Note" rows="2" value="">{{ $data->note ?? old('note') }} </textarea>
				@error('note')
					<span class="invalid-feedback" role="alert">
						<strong>{{ $message }}</strong>
					</span>
				@enderror
		    </div>
	    </div>
	    
	</div>

    <div class="row m-2">
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-submit" >{{ __('common.Update') }}</button>
        </div>
    </div>
    
    </form>
    </div>
   </div>
   </div>
   </div>
   </section>
</div>  
 
  <script src="{{URL::asset('public/assets/school/js/form/form_save.js')}}"></script>

<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2()

    //Initialize Select2 Elements
    $('.select2bs4').select2({
      theme: 'bootstrap4'
    })

  })


</script>
@endsection    
    