@php
   $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();
@endphp
@extends('layout.auth') 
@section('content')
  <style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    text-decoration: none;
}

img {
    max-width: 100%;
}

</style>
</head>
<body>
 <div>
        <div class="row">
            <div class="col-md-12 my-5">
                <div class="card">
                    <div class="card-body">
                        <div class="content-header">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-sm-6">
                                    <h1 class="m-0">Add Branch</h1>
                                    </div>
                                <!--<div class="col-sm-6">-->
                                <!--<ol class="breadcrumb float-sm-right">-->
                                <!--<li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>-->
                                <!--<li class="breadcrumb-item active"><a href="{{url('admin_dashboard')}}">Admin</a></li>-->
                                <!--</ol>-->
                                <!--</div>-->
                            </div>
                        </div>
                    </div>
                    <hr class="bg-danger m-2">
                    <form id="quickForm" action="{{ url('getregister') }}" method="post" >
                        @csrf
                		<div class="row mb-2 m-2">
                		    <div class="col-md-3">
                				
                					<label for="branch_code">Branch Code :</label>
                					<input type="text" class="form-control " id="branch_code" name="branch_code" placeholder="Branch Code" value="{{old('branch_code')}}">
                				
                			</div>
                			<div class="col-md-3">
                				
                					<label for="branch_name" class="required">Branch Name :</label>
                					<input type="text" class="form-control @error('branch_name') is-invalid @enderror" id="branch_name" name="branch_name" placeholder="Branch Name" value="{{old('branch_name')}}">
                					@error('branch_name')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3">
                				
                					<label for="contact_person" class="required">Director/Administrator :</label>
                					<input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" placeholder="Contact Person" value="{{old('contact_person')}}">
                					@error('contact_person')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3 mt-2">
                			   
                    		   	    <lable>Mobile Number :</lable>
                    		   	     <div style="display : inline-flex;">
                        				<div class="input select">
                                			<select name="phone_code" id="phone_code" class="form-control" style="width: 61px;">
                                        @if(!empty($getCountry)) 
                                          @foreach($getCountry as $country)
                                             <option value="{{ $country->phonecode ?? ''  }}" >{{ $country->phonecode ?? ''  }}</option>
                                          @endforeach
                                        @endif
                                            </select>
                                        </div> 
                    		   		    <div class="input text"><input name="mobile_number" id="mobile_number" class="form-control" placeholder="Mobile Number" maxlength="12" minlength="8" type="text" value="{{old('mobile_number')}}"></div>		   		
                    		     	</div>
                		     	
                		   	</div>
                		   	<div class="col-md-3">
                				 
                					<label for="emailid" class="required">Email Id :</label>
                					<input type="text" class="form-control" id="email" name="email" placeholder="Email ID" value="{{old('email')}}">
                				
                			</div>
                			<div class="col-md-3">
                			 
                					<label for="address" class="required">Address :</label>
                					<input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Address" value="{{old('address')}}">
                					@error('contact_person')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                		<div class="col-md-3" >
                            
                             <label>Country :</label>
                              <select class="form-control select2" name="country_id" id="country_id" value="{{ old('country_id') }}">
                                  <option >select</option>
                                  @if(!empty($getCountry)) 
                                      @foreach($getCountry as $country)
                                         <option value="{{ $country->id ?? ''  }}" >{{ $country->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                              
                              
                                	@error('country')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                              </select>
                             
                            </div>
                			<div class="col-md-3">
                				
                					<label for="State" class="required">State :</label>
                					<select class="form-control" id="state_id" name="state_id" value="{{ old('state_id') }}" placeholder="State">
                					     <option >select</option>
                                <!--@if(!empty($getState)) 
                                      @foreach($getState as $state)
                                         <option value="{{ $state->id ?? ''}}" >{{ $state->name ?? ''}}</option>
                                      @endforeach
                                @endif-->
                                 @error('state')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                                    </select>
                				
                			
                			</div>
                			<div class="col-md-3">
                			    
                			        <label for="City">City: </label>
                			        <select class="form-control" name="city_id" id="city_id" value="{{ old('city_id') }}">
                			             <option >select</option>
                			     <!--@if(!empty($getCity)) 
                                      @foreach($getCity as $cities)
                                         <option value="{{ $cities->id ?? ''  }}" >{{ $cities->name ?? ''  }}</option>
                                      @endforeach
                                  @endif-->
                					
                					@error('city')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                					</select>
                			    
                			</div>
                			<div class="col-md-3">
                				
                					<label for="Pin Code" class="required">Pin Code :</label>
                					<input type="text" class="form-control @error('pin_code') is-invalid @enderror" id="pin_code" name="pin_code" placeholder="Pin Code" value="{{old('pin_code')}}">
                					@error('pin_code')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3">
                				
                					<label for="Username" class="required">User Name :</label>
                					<input type="text" class="form-control @error('user_name') is-invalid @enderror" id="user_name" name="user_name" placeholder="User Name" value="{{old('user_name')}}">
                					@error('user_name')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3">
                				 
                					<label for="Password" class="required">Password :</label>
                					<input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" value="{{old('password')}}">
                					@error('password')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3">
                				 
                					<label for="Password" class="required">Trial Period  :</label>
                						<select name="trial_period" id="trial_period" class="form-control">
                                            <option value="7">1 Week</option>
                                            <option value="14">2 Week</option>
                                            <option value="30">1 Month</option>
                                            <option value="90">1 Quarter</option>
                                            <option value="365">1 Year</option>
                                            <option value="750">Life Time</option>
                                        </select>
                				
                			</div>
                		   	<div class="col-md-3">
                				
                					<label for="expert_name" class="required">Expert Name :</label>
                					<input type="text" class="form-control @error('expert_name') is-invalid @enderror" id="expert_name" name="expert_name" placeholder="Expert Name" value="{{old('expert_name')}}">
                					@error('expert_name')
                						<span class="invalid-feedback" role="alert">
                							<strong>{{ $message }}</strong>
                						</span>
                					@enderror
                				
                			</div>
                			<div class="col-md-3">
                				 
                					<label for="Whatsapp status" class="required">Whatsapp status :</label>
                						<select name="whatsapp_status" id="whatsapp_status" class="form-control">
                                            <option value="1">On</option>
                                            <option value="0">Off</option>
                                        </select>
                			
                			</div>
                			<div class="col-md-3">
                				
                					<label for="whatsapp_message" class="required">Whatsapp Message :</label>
                					<input type="text" class="form-control" id="whatsapp_message" name="whatsapp_message" placeholder="Whatsapp Message" value="{{old('whatsapp_message')}}">
                					
                				
                			</div>
                			<div class="col-md-3">
                				
                					<label for="login_background" class="required">Login Background :</label>
                					<input type="file" class="form-control" id="login_background" name="login_background"  value="{{old('login_background')}}">
                					
                				
                			</div>
                			<div class="col-md-3">
                				 
                					<label for="whatsapp_message" class="required"><i class="fa fa-whatsapp" aria-hidden="true"></i></label>
                					<input type="hidden" name="is_whatsapp" id="is_whatsapp" value="0">
                					<input type="checkbox" name="is_whatsapp" class="form-control" id="is_whatsapp"  value="1">  
                
                					
                				
                			</div>
                		</div>
                        <div class="col-md-12 text-center mt-3">
                			<button type="submit" class="btn btn-info ">Submit</button>
                		</div>
                    	</form>
                   </div>
                </div>
            </div>
        </div>
    </div>
 <!-- jQuery -->
@endsection

