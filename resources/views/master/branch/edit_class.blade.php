@php
   $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();

@endphp
@extends('layout.app') 
@section('content')

                                                                    
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Class</h1>
                </div>
                <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                <li class="breadcrumb-item active"><a href="{{url('admin_dashboard')}}">Back</a></li>
                </ol>
                </div>
            </div>
        </div>
    </div>
    <hr class="bg-danger m-2">
<div class="card">
    <form id="quickForm" action="{{ url('edit_class') }}/{{($data->id)}}" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		    <div class="col-md-6">
				<div class="form-group">
					<label for="add_class">Edit :</label>
					<input type="text" class="form-control @error('add_class') is-invalid @enderror " id="add_class" name="add_class" placeholder="Edit Class" value="{{$data->name}}">
					@error('add_class')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

		</div>
	
	
	
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-info ">Update</button><br><br>
		</div>
    	</form>
    </div>
</div>

@endsection