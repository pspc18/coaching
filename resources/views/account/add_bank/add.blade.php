
    
@extends('layout.app') 
@section('content')
    
   <div class="content-wrapper">
	<section class="content pt-3">
		<div class="container-fluid">
			<div class="row">
				<div class="col-12 col-md-12">
					<div class="card card-outline card-orange">
						<div class="card-header bg-primary">
							<h3 class="card-title"><i class="fa fa-bank"></i> &nbsp; {{__('accounts.Add Account') }}</h3>
						<div class="card-tools">
						     <a href="{{url('bank/account/index')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i> {{__('common.View') }}</a>
						     <!--<a href="{{url('account_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> Back</a>-->
						 </div>

						</div>
                 <form id="quickForm" action="{{ url('bank/account/add') }}" method="post" enctype="multipart/form-data">
                      @csrf
                <div class="row m-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="color:red;">{{__('accounts.Account Holder Name') }} *</label>
                            <input type="text" class="form-control  @error('name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="name" name="name" placeholder="Account Holder Name" value="{{old('name')}}">
                            	@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                        </div>    
                    </div> 
                     <div class="col-md-4">
                         <div class="form-group">
                         <label style="color:red;">{{__('accounts.Account Number') }}*</label>
                       <input type="text" class="form-control  @error('account_number') is-invalid @enderror" id="account_number" name="account_number" placeholder="Account Number" value="{{old('account_number')}}" maxlength="16" onkeypress="javascript:return isNumber(event)">
                        	@error('account_number')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </div>
                    </div>
                     <div class="col-md-4">
                         <div class="form-group">
                         <label style="color:red;">{{__('accounts.Bank Name') }}*</label>
                       <input type="text" class="form-control  @error('bank_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="bank_name" name="bank_name" placeholder="Account Holder Name" value="{{old('bank_name')}}">
                        	@error('bank_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </div>
                    </div>
                </div> 
               <div class="row m-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label style="color:red;">{{__('accounts.Bank Branch Name') }}*</label>
                            <input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" placeholder="Account Holder Name" value="{{old('branch_name')}}">
                            @error('branch_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                        </div>    
                    </div> 
                     <div class="col-md-4">
                         <label style="color:red;">{{__('accounts.Bank IFSC Code') }}*</label>
                       <input type="text" class="form-control @error('ifsc_code') is-invalid @enderror" id="ifsc_code" name="ifsc_code" placeholder="Account Name" value="{{old('ifsc_code')}}">
                        @error('ifsc_code')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </div>
                    <div class="col-md-4">
                         <label style="color:red;">{{__('accounts.Uplode QR.Code') }}*</label>
                       <input type="file" class="form-control @error('uplode_qr') is-invalid @enderror" id="uplode_qr" name="uplode_qr" value="{{old('uplode_qr')}}" accept="image/png, image/jpg, image/jpeg">
										 <p class="text-danger" id="image_error"></p>
                        @error('uplode_qr')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </div>
                </div>   
                <div class="row m-2">
                <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">{{__('accounts.Submit') }}</button>
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
        $('#uplode_qr').change(function(e){
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
    
    