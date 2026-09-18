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
    <form id="quickForm" action="{{ url('students/roll/number/edit') }}/{{($data->id)}}" method="post" >
        @csrf
		<div class="row mb-2 m-2">
		    <div class="col-md-4">
				<div class="form-group">
					<label for="class" style="color:red;"> Class :*</label>
					<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder=" Class" value="{{ $data->name }}">
					@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<label for="section">section :</label>
					<input type="text" class="form-control @error('section') is-invalid @enderror " id="section" name="section" placeholder="section" value="{{ $data->section }}">
					@error('section')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				</div>
			</div>

		</div>
	
	
	
        <div class="col-md-12 text-center">
			<button type="submit" class="btn btn-info ">Updete</button><br><br>
		</div>
    	</form>
    </div>
 </div>   
    
    
    

@endsection  



