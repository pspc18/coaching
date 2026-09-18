@php
$getTypeclass = Helper::classType();
$getCountry = Helper::getCountry();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getgenders = Helper::getgender();
$getSetting=Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Enquiry From</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
          <div class="row">
               <div class="col-2 col-md-2 qr_code_school_logo"> 
         
                <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >
              
          </div>
          <div class="col-10 col-md-10 qr_code_school_name"> 
         
                  {{ $getSetting['name'] ?? ''}}
              
          </div>
          </div>
        <div class="row">
          <div class="col-12 col-md-12"> 
         
            <div class="card  card-outline card-orange">
             <div class="card-header bg-primary">
                 
            <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp; {{ __('Enquiry From') }}</h3>
     
            </div>                 
        <form id="quickForm" action="{{ url('qr_code') }}" method="post" >
        @csrf 
            <div class="row m-2">
            		<div class="col-md-2">
            	    	<div class="form-group">
            				<label style="color:red;">{{ __('Name') }}*</label>
            				<input type="text" class="form-control @error('first_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="first_name" name="first_name" placeholder="{{ __('common.First Name') }}" value="{{old('first_name')}}" required>
            		         @error('first_name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>	
            		
        		    <div class="col-md-2">
                       <div class="form-group">
        				<label style="color:red;">{{ __('Mobile No.') }}*</label>
        				<input type="text" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="{{ __('student.Student Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)" required>
        				 @error('mobile')
        					<span class="invalid-feedback" role="alert">
        						<strong>{{ $message }}</strong>
        					</span>
        				@enderror
        		      </div>
                    </div>
            	    <div class="col-md-2">
            	    	<div class="form-group">
            				<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
            				<input type="text" class="form-control @error('father_name') is-invalid @enderror" onkeydown="return /[a-zA-Z ]/i.test(event.key)" id="father_name" name="father_name" placeholder="{{ __('common.Fathers Name') }}" value="{{old('father_name')}}" required>
            				@error('father_name')
            					<span class="invalid-feedback" role="alert">
            						<strong>{{ $message }}</strong>
            					</span>
            				@enderror
            		    </div>
            		</div>
            		 <div class="col-md-4">
            	    	<div class="form-group">
            				<label>{{ __('student.Remark') }}</label>
            				<input type="text" class="form-control" id="remark_1" name="remark_1" placeholder="{{ __('student.Remark') }}" value="{{old('remark_1')}}">
            			</div>
            		</div>
            		
            		
            </div>
            
            
            
            
            
             <div class="row m-2">
        <div class="col-md-12 text-center pb-2">
            <button type="submit" class="btn btn-primary " id="submitButton">{{ __('common.submit') }}</button>
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

<style>
    .qr_code_school_logo img{
        width: 100px;
    }
    .qr_code_school_name{
        font-size: 40px;
        text-align:center;
    }
    @media screen and (max-width: 763px) {
  .qr_code_school_name {
    font-size: 20px;
  }
  .qr_code_school_logo img{
     width: 60px;
  }
}
</style>
</body>
</html>
