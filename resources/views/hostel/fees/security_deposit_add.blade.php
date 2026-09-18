@php
$getPaymentMode = Helper::getPaymentMode();
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
							<h3 class="card-title"><i class="fa fa-users"></i> &nbsp; {{ __('Hostel Security Deposit Add') }}</h3>
							<div class="card-tools"> 
							    <!--<a href="{{url('meter_unit')}}" class="btn btn-primary  btn-sm"><i class="fa fa-plus"></i>View </a> -->
							    <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i>{{ __('common.Back') }}  </a> 
							</div>
						</div>
                             <form id="studentDetailsForm" method="post" action="{{ url('hostel/fees/security_deposite_add') }}">
                           @csrf
                            <div class="row m-2">
                                <div class="col-md-4">
                        			<label>{{ __('hostel.Select Student') }}<font style="color:red"><b>*</b></font></label>
                        
                           <select name="hostel_assign_id" id="hostel_assign_id" class="form-control select2 " required>
                              <option value="">{{ __('common.Select') }}</option>
                              @if(!empty($allstudents))
                              @foreach($allstudents as $value)
                              <option value="{{ $value->id }}" {{ ( $value->id == $search['student_details'] ?? '' ) ? 'selected' : '' }}>{{ $value->first_name ?? ''}}   {{ $value->father_name ?? ''}}</option>
                              @endforeach
                              @endif
                           </select>
                       
                            	</div>   
                            	<!--   <div class="col-md-2">-->
                            	<!--	<div class="form-group">-->
                            	<!--		<label>{{ __('hostel.Mess Security Deposit') }}</label>-->
                            	<!--		<input type="text" class="form-control" id="mess_security_deposite" name="mess_security_deposite" placeholder="{{ __('hostel.Mess Security Deposit') }}" required> -->
                            	<!--    </div>-->
                            	<!--</div>   -->
                               <div class="col-md-2">
                            		<div class="form-group">
                            			<label>{{ __('Hostel Security Deposit') }}</label>
                            			<input type="text" class="form-control" id="security_deposit" name="security_deposit" placeholder="{{ __('Hostel Security Deposit') }}" required> 
                            	    </div>
                            	</div>   
                               <div class="col-md-2">
                            		<div class="form-group">
                            			<label>{{ __('hostel.Deposit Date') }}</label>
                            			<input type="date" class="form-control" id="date" name="date" placeholder="{{ __('hostel.Deposit Date') }}"  value={{date('Y-m-d')}} required >
                            	    </div>
                            	</div>   
                            
                               <!--   <div class="col-md-2">
                              <div class="form-group">
                                 <label>{{ __('hostel.Payment Mode') }}</label>
                                 <select class="form-control" id="payment_mode_id" name="payment_mode_id" onchange="payment_mode_function(this.value);">

                                    @if(!empty($getPaymentMode))
                                    @foreach($getPaymentMode as $value)
                                    <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                    @endforeach
                                    @endif
                                 </select>
                              </div>
                           </div>-->
                           
                           
                            <div class="col-md-2">
                            <div class="form-group">
                             <label>{{ __('hostel.Payment Mode')}}</label>
                             <select class="form-control" id="payment_mode_id" name="payment_mode_id" onchange="payment_mode_function(this.value);" required>
                                 <option value="">Select</option>
                                @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                                @endif
                             </select>
                            </div>
                       </div>
                         <div class="col-md-2 transaction_slip d-none">
                              <div class="form-group">
                                 <label>{{ __('Transaction Id')}}</label>
                                 <input type="text" name="transaction_id" placeholder="{{ __('Transaction Id')}}" value="" id="transaction_id" class="form-control">
                              </div>
                           </div>
                         <div class="col-md-2 ">
                              <div class="form-group">
                                 <label>{{ __('Other')}}</label>
                                 <input type="text" name="pay_remark" placeholder="{{ __('Other')}}" value="" id="pay_remark" class="form-control">
                              </div>
                           </div>
                           
                           
                               
                                <div class="col-md-12 text-center ">
                            	    <div class="form-group">
                            	      
                            			<button type="submit" class="btn btn-primary">{{ __('hostel.Save') }}</button>
                            			
                            	    </div>                    
                            	</div>
                                
                            </div> 
                        </form>
                        
                        
        </div>
			</div>
		</div>
	</section>
</div>
@endsection   