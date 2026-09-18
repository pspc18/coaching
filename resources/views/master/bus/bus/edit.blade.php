@extends('layout.app')
@section('content')
<div class="content-wrapper">

	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12">

					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-edit"></i> &nbsp;{{ __('bus.Edit Bus') }}</h3>
							<div class="card-tools">
								<!--         <a href="{{url('bus/dashboard')}}" class="btn btn-primary  btn-sm" title="View Holiday"><i class="fa fa-home"></i> home </a>-->
								<a class="pl-2"><a href="{{url('busView')}}" class="btn btn-primary  btn-xs"><i class="fa fa-eye"></i>{{ __('messages.View') }} </a>
								<a href="{{url('busDashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>
							</div>

						</div>

						<form id="form-submit-edit" action="{{url('busEdit')}}/{{$data['id']}}" method="post" enctype="multipart/form-data">
							@csrf
							<div class="row m-2">
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Name') }}*</label>
										<input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" placeholder="Bus Name" value="{{$data['name'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Company') }}*</label>
										<input type="text" class="form-control @error('bus_company') is-invalid @enderror " id="bus_company" name="bus_company" placeholder="Bus Company" value="{{$data['bus_company'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Model No.') }}*</label>
										<input type="text" class="form-control @error('bus_model_no') is-invalid @enderror " id="bus_model_no" name="bus_model_no" placeholder="Bus Model No" value="{{$data['bus_model_no'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus No.') }}*</label>
										<input type="text" class="form-control @error('bus_no') is-invalid @enderror " id="bus_no" name="bus_no" placeholder="Bus No." value="{{$data['bus_no'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Owner Name') }}*</label>
										<input type="text" class="form-control @error('bus_owmer_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="bus_owmer_name" name="bus_owmer_name" placeholder="Bus Owner Name" value="{{$data['bus_owmer_name'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Owner Contact No.') }}*</label>
										<input type="text" class="form-control @error('owner_no') is-invalid @enderror " id="owner_no" name="owner_no" placeholder="Bus Owner Contant No." value="{{$data['owner_no'] ?? ''}}" maxlength="10" onkeypress="javascript:return isNumber(event)">
										
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('bus.Bus Rigistration No.') }} *</label>
										<input type="text" class="form-control @error('bus_rigistration_no') is-invalid @enderror " id="bus_rigistration_no" name="bus_rigistration_no" placeholder="Add Role" value="{{$data['bus_rigistration_no'] ?? ''}}">
									
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label>{{ __('bus.Capacity Of Bus') }}</label>
										<input type="text" class="form-control" id="capacity_bus" name="capacity_bus" placeholder="Capacity Of Bus" value="{{$data['capacity_bus'] ?? ''}}">
										
									</div>
								</div>
								<div class="col-md-3">
									<lable style="color:red;" >{{ __('bus.Bus Photo') }}*</lable>
									<div class="input file form-control @error('bus_photo') is-invalid @enderror">
										<input type="file" name="bus_photo" id="bus_photo" value="{{old('bus_photo')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_photo'] }}" width="60px" height="60px">
								</div>
								<div class="col-md-3">
									<lable> {{ __('bus.Bus Rigistration Card') }} </lable>
									<div class="input file form-control @error('bus_rigistration_card') is-invalid @enderror">
										<input type="file" name="bus_rigistration_card" id="bus_rigistration_card" value="{{old('bus_rigistration_card')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_errors"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_rigistration_card'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Insurance') }} </lable>
									<div class="input file form-control @error('bus_insurance') is-invalid @enderror">
										<input type="file" name="bus_insurance" id="bus_insurance" value="{{old('bus_insurance')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_errorss"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_insurance'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Other Document') }} </lable>
									<div class="input file form-control @error('bus_document') is-invalid @enderror">
										<input type="file" name="bus_document" id="bus_document" value="{{old('bus_document')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_er"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_document'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Pollution Certificate') }} </lable>
									<div class="input file form-control @error('bus_pollution') is-invalid @enderror">
										<input type="file" name="bus_pollution" id="bus_pollution" value="{{old('bus_pollution')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_erro"></p>
										
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_pollution'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Fitness Certificate') }} </lable>
									<div class="input file form-control @error('bus_fitness') is-invalid @enderror">
										<input type="file" name="bus_fitness" id="bus_fitness" value="{{old('bus_fitness')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_errorr"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_fitness'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Speed Certificate') }} </lable>
									<div class="input file form-control @error('bus_speed') is-invalid @enderror">
										<input type="file" name="bus_speed" id="bus_speed" value="{{old('bus_speed')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_bus"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_speed'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Permit Certificate') }} </lable>
									<div class="input file form-control @error('bus_permit') is-invalid @enderror">
										<input type="file" name="bus_permit" id="bus_permit" value="{{old('bus_permit')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_permit"></p>
										
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_permit'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus GPS Certificate') }} </lable>
									<div class="input file form-control @error('bus_gps') is-invalid @enderror">
										<input type="file" name="bus_gps" id="bus_gps" value="{{old('bus_gps')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_permit"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_gps'] }}" width="60px" height="60px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
								<div class="col-md-3">
									<lable>{{ __('bus.Bus Camera Certificate') }} </lable>
									<div class="input file form-control @error('bus_camera') is-invalid @enderror">
										<input type="file" name="bus_camera" id="bus_camera" value="{{old('bus_camera')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_camera"></p>
									
									</div>
								</div>
								<div class="col-md-1">
									<img src="{{ env('IMAGE_SHOW_PATH').'/bus_photo/'.$data['bus_camera'] }}" width="60px" height="50px" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/pdf_image.jpg' }}'">
								</div>
							</div>
							<div class="col-md-12 text-center">
								<button type="submit" class="btn btn-primary btn-submit">{{ __('messages.Update') }}</button><br><br>
							</div>
						</form>

					</div>
				</div>
			</div>
		</div>
	</section>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#bus_photo').change(function(e){
            $('#image_error').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_error').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_error').html("");
            }
        }else{
            $('#image_error').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_rigistration_card').change(function(e){
            $('#image_errors').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_errors').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_errors').html("");
            }
        }else{
            $('#image_errors').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_insurance').change(function(e){
            $('#image_errorss').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_errorss').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_errorss').html("");
            }
        }else{
            $('#image_errorss').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_document').change(function(e){
            $('#image_er').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_er').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_er').html("");
            }
        }else{
            $('#image_er').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_pollution').change(function(e){
            $('#image_erro').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_erro').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_erro').html("");
            }
        }else{
            $('#image_erro').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_fitness').change(function(e){
            $('#image_errorr').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_errorr').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_errorr').html("");
            }
        }else{
            $('#image_errorr').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_speed').change(function(e){
            $('#image_bus').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_bus').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_bus').html("");
            }
        }else{
            $('#image_bus').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_permit').change(function(e){
            $('#image_permit').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_permit').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_permit').html("");
            }
        }else{
            $('#image_permit').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_gps').change(function(e){
            $('#image_gps').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_gps').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_gps').html("");
            }
        }else{
            $('#image_gps').html("Image Size File");
            $(this).val('');
        }
        });
    });
    $(document).ready(function(){
        $('#bus_camera').change(function(e){
            $('#image_camera').html("");
            var fileName = $(this).val();
        var extension = fileName.split(".").pop();
        if (
          extension.toLowerCase() === "png" ||
          extension.toLowerCase() === "jpg" ||
          extension.toLowerCase() === "jpeg"
        ) {
            if (e.target.files[0].size > Img_Size) {
                $('#image_camera').html("please select Image Size under 2MB");
                $(this).val('');
            }else{
                $('#image_camera').html("");
            }
        }else{
            $('#image_camera').html("Image Size File");
            $(this).val('');
        }
        });
    });
    </script>
    
    <style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    #image_errors{
        font-weight: bold;
    font-size: 14px;
    }
    #image_errorss{
        font-weight: bold;
    font-size: 14px;
    }
    #image_er{
        font-weight: bold;
    font-size: 14px;
    }
    #image_erro{
        font-weight: bold;
    font-size: 14px;
    }
    #image_errorr{
        font-weight: bold;
    font-size: 14px;
    }
    #image_bus{
        font-weight: bold;
    font-size: 14px;
    }
    #image_permit{
        font-weight: bold;
    font-size: 14px;
    }
    #image_gps{
        font-weight: bold;
    font-size: 14px;
    }
    #image_camera{
        font-weight: bold;
    font-size: 14px;
    }
    </style>
@endsection