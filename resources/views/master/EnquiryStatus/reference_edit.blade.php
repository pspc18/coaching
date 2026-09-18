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
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('Edit Config Reference') }}</h3>
							
							<div class="card-tools">
                                        <a href="{{url('enquiry_status_add')}}" class="btn btn-primary btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
                                        <a href="{{url('reception_file')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }}</a>

                        </div>
                           </div>                      

               
   

    <form id="form-submit-edit" action="{{ url('enquiry_status_edit') }}/{{($data->id)}}" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		    <div class="col-md-4">
				<div class="form-group">
					<label class="text-danger">{{ __('Status Name') }}*</label>
					<input type="text" class="form-control @error('name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="name" name="name" placeholder="Section" value="{{$data->name}}">
					
				</div>
			</div>

		</div>
	
	
	
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-primary btn-submit">{{ __('common.Update') }}</button><br><br>
		</div>
    	</form>
   
            </div>
        </div>
    </div>
    </section>
</div>>

@endsection