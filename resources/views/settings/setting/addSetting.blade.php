@php
    $getCountry = Helper::getCountry();
    $getState = Helper::getState();
    $getCity = Helper::getCity();
    $getaccounts = Helper::getaccount();
@endphp

@extends('layout.app') 
@section('content')
<div class="content-wrapper">
   
   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card card-outline card-orange">

                <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-cogs"></i> &nbsp;{{ __('setting.Add Setting') }} </h3>
                    <div class="card-tools">
                        <a href="{{url('viewSetting')}}" class="btn btn-primary  btn-sm" title="View"><i class="fa fa-eye"></i> {{ __('common.View') }} </a>
                    </div>
                </div> 

        <div class="card-body">
         <form id="quickForm" action="{{ url('addSetting') }}" method="post"  enctype="multipart/form-data">   
         @csrf            
            <div class="row">
                 @if(Session::get('role_id') == 1)
                <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="branch" class="text-danger @error('branch_id') is-invalid @enderror">{{ __('setting.Branch') }}*</label>
				    	 <select class="form-control" id="branch_id" name="branch_id" 
				    	 
				    	 >
				    	     <option value="">{{ __('common.Select') }}</option>
			    	     	@if(!empty($branch)) 
                                      @foreach($branch as $Branch)
                                         <option value="{{ $Branch->id ?? ''  }}">{{ $Branch->branch_name ?? ''  }}</option>
                                      @endforeach
                          	@endif
					       </select>	
					       @error('branch_id')
	    						<span class="invalid-feedback" role="alert">
	    							<strong>{{ $message }}</strong>
	    						</span>
	    					@enderror
				    </div>
			    </div>
			    @endif
			    <!-- <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username" style="color:red;">{{ __('messages.Name') }}*</label>
				    	<input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" placeholder="Name" value="">
					@error('name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>                
                
			    <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username" style="color:red;">{{ __('messages.Mobile No.') }}*</label>
				    	<input type="numbre" class="form-control @error('mobile') is-invalid @enderror" id="mobile" name="mobile" placeholder="Mobile No." value="" maxlength="10" onkeypress="javascript:return isNumber(event)">
					@error('mobile')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>                
                
			    <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username" style="color:red;">{{ __('messages.E-Mail') }}*</label>
				    	<input type="text" class="form-control @error('gmail') is-invalid @enderror" id="gmail" name="gmail" placeholder="Gmail" value="">
					@error('gmail')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>  --> 
			<!--  <div class="col-md-3" >
                <div class="form-group">
                 <label>{{ __('messages.Country') }}</label>
                  <select class="select2 form-control select2" name="country_id" id="country_id" >
                      @if(!empty($getCountry)) 
                          @foreach($getCountry as $country)
                             <option value="{{ $country->id ?? ''  }}">{{ $country->name ?? ''  }}</option>
                          @endforeach
                      @endif
                    
              
                	@error('country')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
              </select>
              </div>
            </div>
			<div class="col-md-3">
				<div class="form-group"> 
					<label for="State" class="required">{{ __('messages.State') }}</label>
					<select class="select2 form-control" id="state_id" name="state_id">
                @if(!empty($getState)) 
                      @foreach($getState as $state)
                         <option value="{{ $state->id ?? ''}}">{{ $state->name ?? ''}}</option>
                      @endforeach
                @endif
                  
                  
                  
                  	@error('state')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </select>
				
				</div>
			</div>
			<div class="col-md-3">
			    <div class="form-group">
			        <label for="City">{{ __('messages.City') }}</label>
			        <select class="select2 form-control" name="city_id" id="city_id" >
			            @if(!empty($getCity)) 
                      @foreach($getCity as $cities)
                         <option value="{{ $cities->id ?? ''  }}">{{ $cities->name ?? ''  }}</option>
                      @endforeach
                  @endif
					
					@error('city')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
					</select>
			    </div>
			</div> -->	    
                
			    <!-- <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username" style="color:red;">{{ __('messages.Pin Code') }}*</label>
				    	<input type="numbre" class="form-control @error('pincode') is-invalid @enderror" id="pincode" name="pincode" placeholder="Pin Code" value="">
					@error('pincode')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>   
			    
			     <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username" style="color:red;">{{ __('messages.Address') }}*</label>
				    	<input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" placeholder="Address" value="">
					@error('address')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>			    
               
                <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="right_logo" class="required">{{ __('messages.Right Logo') }}</label>
				    	 <input type="file" class="form-control  @error('right_logo') is-invalid @enderror" id="right_logo" name="right_logo" value="">
					    	@error('right_logo')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>

                <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="left_logo" class="required">{{ __('messages.Left Logo') }}</label>
				    	<input type="file" class="form-control  @error('left_logo') is-invalid @enderror" id="left_logo" name="left_logo" value="">
						@error('left_logo')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
				    	@enderror
				    </div>
			    </div>
			  
                <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="seal_sign" class="required">{{ __('messages.Seal & Sign.') }}</label>
				    	<input type="file" class="form-control  @error('seal_sign') is-invalid @enderror" id="seal_sign" name="seal_sign" value="">
						@error('seal_sign')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
				    	@enderror
				    </div>
			    </div>
			   
			    <div class="col-md-3">
				    <div class="form-group"> 
					    <label for="Username">{{ __('messages.Tin No.') }}</label>
				    	<input type="text" class="form-control @error('tin_no') is-invalid @enderror" id="tin_no" name="tin_no" placeholder="Tin No" value="">
						@error('tin_no')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
				    </div>
			    </div>
               <div class="col-md-3">
                         <div class="form-group">
                         <label>{{ __('messages.Account Name') }}</label>
                       <select class="select2 form-control  @error('account_name') is-invalid @enderror" id="account_id" name="account_id" placeholder="Account Name" >
                        <option value="">{{ __('messages.select') }} </option>
                         @if(!empty($getaccounts)) 
                              @foreach($getaccounts as $account)
                                 <option value="{{ $account->id ?? '' }}" >{{ $account->bank_name ?? ''  }} / Holder Name ={{ $account->name ?? ''  }}</option>
                              @endforeach
                         @endif
                        </select>
                        	@error('account_name')
						<span class="invalid-feedback" role="alert">
							<strong>{{ $message }}</strong>
						</span>
					@enderror
                    </div>
                </div> -->
			    
             <div class="col-md-12 text-center">
    			<button type="submit" class="btn btn-primary ">{{ __('common.Submit') }}</button>
    		</div>



            
            </div>
            </form>
        </div>

		
    </div>
</div>        
</div>
</div>
</section>
</div>
@endsection        