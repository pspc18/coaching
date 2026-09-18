@php
$getPaymentMode = Helper::getPaymentMode();
$getAllUsers = Helper::getAllUsers();
$getAllHead = Helper::getAllHead();
//dd($data);
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
							<h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __('hostel.Hostel Expense Edit') }}</h3>
							<div class="card-tools">
								<a href="{{url('hostelExpensesView')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{ __('common.View') }}</a>
								<a href="{{url('hostel_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
							</div>

						</div>
						<form id="quickForm" action="{{ url('hostelExpensesEdit') }}/{{$data['id']}}" method="post" enctype="multipart/form-data">
							@csrf

							<div class="row m-2">
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Expense Head') }}*</label>
										<select class="form-control @error('expense_head') is-invalid @enderror" id="expense_head" name="expense_head">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getAllHead))
											@foreach($getAllHead as $value)
											<option value="{{ $value->id}}" {{ ($value->id == $data['expense_head']) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
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

								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Expense Name') }}*</label>
										<input type="text" class="form-control @error('expense_name') is-invalid @enderror" id="expense_name" name="expense_name" placeholder="{{ __('hostel.Expense Name') }}" value="{{ old('expense_name', $data['expense_name'] ?? '') }}">

										@error('expense_name')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Expense Date') }}*</label>
										<input type="date" class="form-control @error('expense_date') is-invalid @enderror" id="expense_date" name="expense_date"  value="{{$data['expense_date'] ?? '' }}">
										@error('expense_date')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Expense Amount') }}*</label>
										<input type="text" class="form-control @error('expense_amount') is-invalid @enderror" id="expense_amount" name="expense_amount" placeholder="{{ __('hostel.Expense Amount') }}" value="{{ old('expense_amount', $data['expense_amount'] ?? '') }}" maxlength="10" onkeypress="javascript:return isNumber(event)">
										@error('expense_amount')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror
									</div>
								</div>
								<div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Expense By') }}*</label>
										<select class="form-control @error('expense_by') is-invalid @enderror" id="expense_by" name="expense_by">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getAllUsers))
											@foreach($getAllUsers as $value)
											<option value="{{ $value->id}}" {{ ($value->id == $data['expense_by']) ? 'selected' : '' }}>{{ $value->first_name ?? ''  }}{{ $value->last_name ?? ''  }}</option>
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
							
	                               <div class="col-md-3">
									<div class="form-group">
										<label style="color:red;">{{ __('hostel.Payment Mode') }}*</label>
										<select class="form-control @error('payment_mode') is-invalid @enderror" id="payment_mode" name="payment_mode">
											<option value="">{{ __('common.Select') }}</option>
											@if(!empty($getPaymentMode))
											@foreach($getPaymentMode as $value)
											<option value="{{ $value->id}}" {{ ($value->id == $data['payment_mode']) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
											@endforeach
											@endif
										
										</select>
										@error('payment_mode')
										<span class="invalid-feedback" role="alert">
											<strong>{{ $message }}</strong>
										</span>
										@enderror
									</div>
								</div>
                                    <div class="col-md-3">
									<div class="form-group">
										<label>{{ __('hostel.Expense Bill') }}</label>
										<input type="file" class="form-control" id="expense_bill" name="expense_bill" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
										
									</div>
								</div>
									<div class="col-md-3">
									<img src="{{ env('IMAGE_SHOW_PATH').'expense_bill/'.$data['expense_bill'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" width="60px" height="60px">
								</div>
								</div>
							<div class="row m-2">
								<div class="col-md-12 text-center pb-2">
									<button type="submit" class="btn btn-primary ">{{ __('common.submit') }}</button>
									<!-- <a href="https://wa.me/?text= hello this  msg is for testing purpose">Whatsapp testing</a>-->

								</div>
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
        $('#expense_bill').change(function(e){
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
    </script>
    
    <style>
    #image_error{
        font-weight: bold;
    font-size: 14px;
    }
    </style>
@endsection