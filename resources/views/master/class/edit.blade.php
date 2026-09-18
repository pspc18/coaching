@php
  $all_class = Helper::classType();
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
                            <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('master.Edit Class') }} </h3>
                            <div class="card-tools">
                                        <a href="{{url('add_class')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>

                        </div>
                           </div>                      

    <form id="form-submit-edit" action="{{ url('edit_class') }}/{{$data['id']}}" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		     <div class="col-md-4">
				<div class="form-group">
				<label class="text-danger">{{ __('master.Class') }}* </label>
					<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder="{{ __('master.Class') }}" value="{{$data['name'] ?? ''}}">
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

</div>    
</section>
</div>                                                           
    
        
        
        
        
@endsection