@php
  $classType = Helper::ClassType();
  $getsubject = Helper::getSubject();
@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        @include('examination.offline_exam.partials.workflow')
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="nav-icon fas fa fa-leanpub"></i> &nbsp;{{__('examination.Add Exams') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('view/exam')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    <a href="{{url('view/exam')}}" class="btn btn-primary  btn-sm" title="View Users"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                    </div>
                    
                    </div>        
                <form id="quickForm" action="{{ url('add/exam') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row m-2">
                       <div class="col-md-3">
            			<div class="form-group">
            				<label style="color:red;">{{ __('examination.Exam Name') }}*</label>
            				<input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('examination.Exam Name') }}" value="{{old('name')}}">
                             @error('name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>
                     
            		<div class="col-md-3">
						<div class="form-group">
							<label style="color:red;">{{ __('common.Class') }}*</label>
							<select class="form-control select2 @error('class_type_id') is-invalid @enderror" id="class_type_id" name="class_type_id" required>
								<option value="">{{ __('common.Select') }}</option>
								@if(!empty($classType))
								@foreach($classType as $type)
								<option value="{{ $type->id ?? ''  }}" {{ old('class_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
								@endforeach
								@endif
							</select>
            				@error('class_type_id')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
						</div>
					</div>
                    <div class="col-md-12">
            			<div class="form-group">
            				<label>{{ __('examination.Description') }}</label>
                            <textarea id="description" name="description"  placeholder="{{ __('examination.Description') }}" class="form-control @error('description') is-invalid @enderror ">{{old('description')}}</textarea>
                             @error('description')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div> 
        		
		        </div>

                <div class="row m-2 pb-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-arrow-right"></i> Save & Fill Marks</button>
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
    $("[type='number']").keypress(function (evt) {
    evt.preventDefault();
    
    alert("Use only Buttons For Increase/Decrease Time")
    
});
</script>



@endsection
