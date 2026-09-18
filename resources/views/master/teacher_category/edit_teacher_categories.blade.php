@php
   $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();

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
                             <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Staff Categories') }} </h3>
                		<div class="card-tools">
                                         <a href="{{url('teacher_categories')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }} </a>
          
            </div>
            
            </div>               

    <form id="quickForm" action="{{ url('edit_teacher_categories') }}/{{($data->id)}}" method="post" >
        @csrf
			<div class="row mb-2 m-2">
		     <div class="col-md-4">
				<div class="form-group">
				<label class="text-danger">{{ __('master.Staff Categories') }} *</label>
					<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder="{{ __('master.Staff Categories') }}" value="{{$data->name}}">
					@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
							</div>
			</div>

		</div>
	
	
	
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary ">{{ __('common.Update') }}</button><br><br>
		</div>
    	</form>
    </div>
        </div>
    </div>

</div>    
</section>
</div>                                                           
    
   
@endsection