@php
$getLibrary = Helper::getLibrary();
$getgenders = Helper::getgender();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
 $getPaymentMode = Helper::getPaymentMode();
 $getSetting = Helper::getSetting();

@endphp
@extends('layout.app') 
@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
    <div class="col-md-8">
        <div class="card card-primary">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h3 class="card-title">New Students </h3>
                    <a href="{{ url('library_dashboard') }}">
                        <button class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back
                        </button>
                    </a>
                </div>
            </div>

             <form id="submit_form" action="{{ url('library_assign') }}" method="post">
                @csrf
                <div class="card-body">
                <div class="row">
                            
                    <div class="col-md-4">
						<div class="form-group">
							<label>{{ __('library.Admission No.') }}<span style="color:red;">*</span></label>
							<input type="text" class="form-control" id="admission_no" name="admission_no" placeholder="{{ __('library.Admission No.') }}" readonly value="{{$admissionBillCounter->counter +1 ?? ''}}" onkeypress="javascript:return isNumber(event)">
						</div>
					</div>
                    <div class="col-md-4" >
                        <div class="form-group">
            			<label style="color:red;">{{ __('Name') }}*</label>
        				<input type="text" class="form-control invalid" placeholder="Enter Name"  id="first_name" name="first_name" value="{{old('first_name')}}" >
        					<span class="invalid-feedback" id="first_name_invalid" role="alert">
        						<strong>{{ __('library.First name can not be left blank') }}</strong>
        					</span>
    					</div>
                	</div> 
                	<div class="col-md-4" >
                          <label style="color:red;">{{ __('common.Gender') }}*</label>
                          <select class="form-control invalid" id="gender_id" name="gender_id">
            				<option value="">{{ __('common.Select') }}</option>
                            @if(!empty($getgenders)) 
                                  @foreach($getgenders as $value)
                                     <option value="{{ $value->id}}" {{ ($value->id == old('gender_id')) ? 'selected' : '' }}>{{ $value->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>
                            
                          <span class="invalid-feedback" id="gender_id_invalid" role="alert">
        						<strong>{{ __('Gender can not be left blank') }}</strong>
        					</span>
                    </div>	 
                    <div class="col-md-4" >
                        <div class="form-group">
            			<label style="color:red;">{{ __('common.Mobile No.') }}*</label>
        				<input type="text" class="form-control invalid" id="mobile" name="mobile" placeholder="{{ __('common.Mobile No.') }}" value="{{old('mobile')}}" maxlength="10" onkeypress="javascript:return isNumber(event)" >
        					<span class="invalid-feedback" id="mobile_invalid" role="alert">
        						<strong>{{ __('Mobile can not be left blank') }}</strong>
        					</span>
        					</div>
                	</div>
                	<div class="col-md-4" >
            			<label>{{ __('common.Email') }}</label>
        				<input type="email" class="form-control" placeholder="{{ __('common.Email') }}" id="email" name="email" value="{{old('email')}}" >
                         
                	</div>
                	<div class="col-md-4">
            			<label style="color:red;">{{ __('common.Fathers Name') }}*</label>
        				<input type="text" class="form-control invalid" onkeydown="return /[a-zA-Z ]/i.test(event.key)" placeholder="{{ __('common.Fathers Name') }}" id="father_name" name="father_name" value="{{old('father_name')}}">
                        	<span class="invalid-feedback" id="father_name_invalid" role="alert">
        						<strong>Father's Name can not be left blank</strong>
        					</span>
                	</div>
                	<div class="col-md-4" >
            			<div class="form-group">
                			    <label>Registration Date</label>
                    			<div class="input-group">
        							    <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                				<input type="date" class="form-control" id="registration_date" name="registration_date" value="{{old('registration_date')}}">
                                </div>
            			</div>
                	</div>
                	
                    <div class="col-md-4">
						<div class="form-group">
							<label>{{ __('common.Country') }}</label>
							<select class="form-control" name="country" id="country_id">
								<option value="">{{ __('common.Select') }}</option>
								@if(!empty($getCountry))
								@foreach($getCountry as $country)
								<option value="{{ $country->id ?? ''  }}" {{ ($country->id == $getSetting->country_id) ? 'selected' : '' }}>{{ $country->name ?? ''  }}</option>
								@endforeach
								@endif
							</select>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="State" class="required">{{ __('common.State') }}</label>
							<select class="form-control stateId " id="state_id" name="state">
								<option value="">{{ __('common.Select') }}</option>
								@if(!empty($getState))
								@foreach($getState as $state)
								<option value="{{ $state->id ?? ''}}" {{ ($state->id == $getSetting->state_id) ? 'selected' : '' }}>{{ $state->name ?? ''}}</option>
								@endforeach
								@endif
							</select>

						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="City">{{ __('common.City') }}</label>
							<select class="form-control cityId " name="city" id="city_id">
								<option value="">{{ __('common.Select') }}</option>
								@if(!empty($getCity))
								@foreach($getCity as $cities)
								<option value="{{ $cities->id ?? ''  }}" {{ ($cities->id == $getSetting->city_id) ? 'selected' : '' }}>{{ $cities->name ?? ''  }}</option>
								@endforeach
								@endif
							</select>
						</div>
					</div> 
					
					
                	
					<div class="col-md-4">
						<div class="form-group">
							<label for="City">{{ __('Pincode') }}</label>
							<input class="form-control" name="pincode" id="pincode" max-length="6" placeholder="pincode">
						</div>
					</div> 
					
					<div class="col-md-12" >
            			<label style="color:red;"> {{ __('common.Address') }}*</label>
        				<textarea type="text" class="form-control invalid" id="address" rows="4" name="address" placeholder="{{ __('common.Address') }}">{{old('address')}}</textarea>
                      	<span class="invalid-feedback" id="address_invalid" role="alert">
    						<strong>Address can not be left blank</strong>
    					</span>
                	</div>
                	
					<div class="col-md-4">
						<div class="form-group">
							<label for="City" class="text-danger">Select ID Proof*</label>
							<select class="form-control invalid" id="id_proof" name="id_proof" autocomplete="off">
                                <option value="">Select ID Proof</option>
                                <option value="Aadhar Card">Aadhar Card</option>
                                <option value="Voter ID Card">Voter ID Card</option>
                                <option value="Driving License">Driving License</option>
                                <option value="PAN Card">PAN Card</option>
                            </select>
                            
                            <span class="invalid-feedback" id="id_proof_invalid" role="alert">
    						    <strong>Id Proof can not be left blank</strong>
    					    </span>
						</div>
					</div> 
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="id_number">ID Number</label>
							<div class="input-group">
							    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-credit-card"></i>
                                    </span>
                                </div>
						        <input class="form-control" name="id_number" id="id_number" placeholder="ID Number">
							</div>
						</div>
					</div> 
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="dob">Date of Birth</label>
							<div class="input-group">
							    <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
						        <input type="date" class="form-control" name="dob" id="dob">
							</div>
						</div>
					</div> 

            		<input type="hidden" id="cabin_id">
            		<input type="hidden" id="cabin_name">
            		<div class="row">
						<label class="control-label required-field" for="appendprepend">Select Time Slot</label>
						<div class="col-md-12 overflow_scroll_plans">
							<div class="form-group">
								<div class="slots-container">
									<div class="slot-row">
									     @if(!empty($time_slot))
                                                  @foreach($time_slot as $type)
									    <div class="slot" id="slot_{{$type->id}}">
											<h4>{{$type->study_time ?? ''}}</h4>
											<!--<p>Slot 162</p>-->
											<div class="plan">
											    <input type="checkbox" name="time_slot_id[{{ $type->id ?? ''}}]" class="time_slot" id="time_slot_id" value="{{ $type->id ?? ''}}"  data-price="{{$type->amount ?? ''}}" data-id="{{ $type->id ?? ''}}" >
											    <label for="time_slot_id_{{ $type->id ?? ''}}" class="ml-1"> 1 Month</label>
											    <span class="price"> <b>Rs. {{$type->amount ?? ''}}</b></span></div>
											<div class="form-group">
												<label class="control-label" for="appendprepend">Renewal Date</label>
												<div class="row">
                                                    <div class="col-sm-12">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="fa fa-calendar"></i>
                                                            </span>
                                                        </div>
                                                        
                                                        <input type="date" class="form-control input-sm renew_date" placeholder="Enter Renewal Date" name="renew_date[{{ $type->id ?? ''}}]" id="renew_date_{{$type->id}}">
                                                    </div>
                                                    </div>
												</div>
											</div>
											<div class="form-group">
												<label for="selectedSeat">Selected Seat:</label>
												<input type="text" class="form-control selectedSeat" id="selectedSeat_{{$type->id ?? ''}}" name="" readonly="">
												<input type="hidden" class="form-control" id="cabin_seat_id_{{$type->id ?? ''}}" name="cabin_seat_id[{{$type->id ?? ''}}]" readonly="">
											</div>
											<div>
												<button type="button" class="btn btn-primary btn-sm" id="seat_allot_{{ $type->id ?? '' }}" disabled data-toggle="modal" data-target=".bd-example-modal-lg" onclick="allotSeatInSlot({{$type->id ?? ''}});">Allot Seat</button>
											</div>
										</div>
										  @endforeach
                                       @endif													
									</div>
									
									</div>
							</div>
						</div>
						<div class="col-md-4">
						    <div class="form-group">
                                <label >Total Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                         <input class="form-control @error('amount') is-invalid @enderror" readonly="" type="number" name="library_amount" id="library_amount" value="0"> 
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
							</div>
							<div class="col-md-4">
                                <div class="form-group">
                                    <label for="discountType">Discount Type:</label>
                                    <select class="form-control" id="discountType" name="discountType" >
                                        <option value="">Select Discount Type</option>
                                        <option value="value">Flat Discount</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discountValue">Discount Value:</label>
                                    <input type="number" class="form-control" id="discountValue" name="discountValue" value="0" readonly="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                <label for="discountAmount">Discount Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                        <input class="form-control" type="number" name="discountAmount" id="discountAmount" readonly="" value="0">
                                    </div>
                            </div>
                            </div>
							</div>
						<div class="row">
						 <div class="col-sm-4">
						     <label>Locker Fee</label>
						     <div class="input_group">
						         <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <input type="checkbox" class="apply-fee we-checkbox" id="locker_fees" data-check="0" data-amount="100" name="locker_fees_check">
                                    </span>
                                    <div class="input-group-append">
                                        <span class="input-group-text">RS</span>
                                    </div>
                                        <input type="number" class="fee-amount form-control" id="locker_fees_amount" name="locker_fees" value="100">
                                  </div>
						     </div>
                            </div> 
                            <div class="col-sm-4">
                                 <div class="form-group">
                                    <label for="selectedLocker">Selected Locker:</label>
                                        <div class="display-flex">
                                            <input type="text" class="form-control width-30p" id="library_locker_name" name="library_locker_name" readonly="">
                                            <input type="hidden" class="form-control" id="library_locker_id" name="library_locker_id" value="">
                                            <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#LockerModal" onclick="allotLocker();">Select Locker</button>
                                        </div>
                                   </div>
                             </div>
                            <div class="col-sm-4">
                            <label class="control-label" for="lockerRenewalDate">Locker Renewal Date</label>
                                <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                </div>
                                
                                <input type="date" class="form-control input-sm" placeholder="Enter Locker Renewal Date" name="lockerRenewalDate" id="lockerRenewalDate" value="">
                                </div>
                            </div>
                                                
                                        
					</div> 
                        <div class="col-md-6">
                            <label for="totalPayableAmount">Total Payable Amount:</label>
                               <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="totalPayableAmount" id="totalPayableAmount" readonly="" value="0">
                                    <!-- <span class="input-group-addon">.00</span> -->
                                </div>
                            </div>
                        <div class="col-md-6">
                             <label for="totalPayableAmount"> Payable Amount:</label>
                            <div class="input-group">
                           
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="text" name="amount" value="0" id="amount" autocomplete="off" data-gtm-form-interact-field-id="2">
                                </div>
                            </div>
                        
                         <div class="col-md-6">
                              <label class="text-danger"><b>{{ __('library.Payment Mode') }}*</b></label>
                            <select class="form-control" id="payment_mode_id" name="payment_mode_id" required>
                                @if(!empty($getPaymentMode))
                                    @foreach($getPaymentMode as $value)
                                    <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                    @endforeach
                                @endif  
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duesAmount">Dues Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="duesAmount" id="duesAmount" readonly="" value="0">
                                    <!-- <span class="input-group-addon">.00</span> -->
                                </div>
                            </div>
                        </div>
                  </div>
                    

                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary" id="is-invalid">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Time Slot</h3>
            </div>
            <div class="card-body">
                 @if(!empty($time_slot))
                    @foreach($time_slot as $type)
                        @php
                        $counts = Helper::getSeatCounts($type->id);
                        @endphp
                        <a href="#" class="list-group-item p-2">
        						<h5 class="list-group-item-heading">{{$type->study_time ?? ''}}</h5>
        						<p class="list-group-item-text" >{{$type->study_hour ?? ''}} Hours <span style="color:#000">|</span>
        						<span style="color:#F03; font-weight:600; font-size:14px;">	Fee: Rs. {{$type->amount ?? ''}} / Month </span><span style="color:#000">|</span><span style="color:#F03; font-weight:600; font-size:14px;"> Filled Seats: {{ $counts['booked_seats'] ?? '0' }} </span> <span style="color:#000">|</span> <span style="color:#03ad9d">Available Seats: {{ $counts['available_seats'] ?? '0' }}</span></p> 
        				</a>
				 @endforeach
                    @endif
            </div>
        </div>
    </div>
</div>

        </div>
    </section>
</div>

<div id="" class="seatMapModal modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="seatMapModalLabel">Select Seat</h5>
                    <button type="button" class="close" id="closeModal" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <input type="hidden" id="time_slot_id_select">
                <div class="modal-body">
                    <!-- Seat map legend -->
                    <div class="legend">
                        <span class="legend-item">
                            <i class="fa fa-seat"></i>
                            <img src="https://www.walsisindia.com/library/images/car-seat-occupied.png" alt="Seat Alloted">
                            Seat Alloted
                        </span>
                        <span class="legend-item">
                            <img src="https://www.walsisindia.com/library/images/car-seat-available.png" alt="Seat Available">
                            Seat Available
                        </span>
                    </div>
                    <div id="seatMapContainer">
                       
                        
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmSeatBtn">Confirm Seat</button>
                </div>
            </div>
  </div>
</div>
    <div class="modal fade" id="LockerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="LockerModalLabel">Select Locker</h5>
                    <button type="button" class="close closeModalLocker" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Seat map legend -->
                    <div class="legend">
                        <span class="legend-item">
                            <img src="https://www.walsisindia.com/library/images/safe-occupied.png" alt="Locker Occupied">
                            Locker Occupied
                        </span>
                        <span class="legend-item">
                            <img src="https://www.walsisindia.com/library/images/safe-available.png" alt="Locker Available">
                            Locker Available
                        </span>
                    </div>
                    <div id="LockerContainer">
                       
                </div>
              <!--  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmLockerBtn">Confirm Locker</button>
                </div>-->
            </div>
        </div>
    </div>
    </div>
<!-- Example CDN inclusion in your HTML file -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>



<script>
    
		$(document).ready(function(e) {
		       $('.time_slot').on("change", function() {
		            
                    var regDate =  new Date($('#registration_date').val());
                    if(regDate == "Invalid Date"){
                         var dateObj = new Date();
                    }else{
                        var dateObj = regDate;
                    }
                    
                    var numberOfDaysToAdd = 30;
				    dateObj.setDate(dateObj.getDate() + numberOfDaysToAdd);
				    var modifiedDate = dateObj.toISOString().split('T')[0]; 
                    
                    var library_amount = parseFloat($('#library_amount').val());
                        if($(this).prop('checked')){
                            var curruntVal = parseFloat($(this).data("price"));
                            var time_slot_id = $(this).data("id");
                            var amount = library_amount + curruntVal;
                            $(this).closest('.slot').addClass('plan-selected');
                            $('#renew_date_' + time_slot_id).val(modifiedDate);
                            $('#seat_allot_' + time_slot_id).attr('disabled',false);
                        }else{
                            var curruntVal = parseFloat($(this).data("price"));
                            var amount = library_amount - curruntVal;
                             $(this).closest('.slot').removeClass('plan-selected');
                             $(this).parent('.plan').siblings('.form-group').children('.renew_date').val("");
                             var time_slot_id = $(this).data("id");
                             $('#renew_date_' + time_slot_id).val("");
                             $('#selectedSeat_' + time_slot_id).val("");
                             $('#cabin_seat_id_' + time_slot_id).val("");
                             $('#cabin_id').val("");
                             $('#cabin_name').val("");
                             $('#seat_allot_' + time_slot_id).attr('disabled',true);
                        }
                    
                    if($('#locker_fees').prop('checked')){
                        var lockerAmt = parseFloat($('#locker_fees_amount').val());
                    }else{
                        var lockerAmt = 0;
                    }
                    
                    $("#library_amount").val(amount);
                    $('#totalPayableAmount').val(amount + lockerAmt);
                    $('#amount').val(amount + lockerAmt);
                    $('#discountType').val("");
                    $('#discountValue').val(0);
                    $('#discountAmount').val(0);
                    $('#duesAmount').val(0);
                    $('#discountValue').attr('readonly',true);
                    $('#discountAmount').attr('readonly',true);

			});
			
		       $('#locker_fees').on("click", function() {
		           var payAmt = parseFloat($('#totalPayableAmount').val());
		           var lockerFees = parseFloat($('#locker_fees_amount').val());
		            if ($(this).prop('checked')) {
		                var finalAmt = payAmt + lockerFees;
                        var regDate =  new Date($('#registration_date').val());
                        if(regDate == "Invalid Date"){
                             var dateObj = new Date();
                        }else{
                            var dateObj = regDate;
                        }

    				    var numberOfDaysToAdd = 30;
    				    dateObj.setDate(dateObj.getDate() + numberOfDaysToAdd);
    				    var modifiedDate = dateObj.toISOString().split('T')[0]; 
                        $("#lockerRenewalDate").val(modifiedDate);
		           }else{
		               $('#lockerRenewalDate').val("");
		               var finalAmt = payAmt - lockerFees;
		           }
		           
		           $('#totalPayableAmount').val(finalAmt);
		           $('#amount').val(finalAmt);
		           $('#duesAmount').val(0);
			});
		});
		
		function allotSeatInSlot(time_slot_id){
			    
			    $.ajax({
             headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
        type:'post',
        url: '/allotSeatInSlot',
        data: {time_slot_id:time_slot_id},
        // dataType: 'html',
        success: function (data) {
           $("#seatMapContainer").html(data);
        }
         }); 
			    
			}
			
		function assignedSeat(seat_id,cabin_name,time_slot_id){
             document.querySelectorAll('.seat_not_assigned').forEach(function(element) {
                    element.classList.remove('selected');
                // 	$(".selectedSeat").val('');
                }); 
            
                	$("#cabin_id").val(seat_id);
                	$("#cabin_name").val(cabin_name);
                var element = document.getElementById("seat-"+seat_id);
                element.classList.add("selected");
			 $("#time_slot_id_select").val(time_slot_id);
		}
		
		$("#confirmSeatBtn").on("click", function() {
		    var cabin_id = $("#cabin_id").val();
            var cabin_name = "S-" + $("#cabin_name").val();
            var time_slot_id = $("#time_slot_id_select").val();
            if (cabin_name == '') {
              alert("Please select a seat.");
            } else {
                $("#closeModal").click();
                 
                $("#selectedSeat_"+ time_slot_id).val(cabin_name);
                $("#cabin_seat_id_"+ time_slot_id).val(cabin_id);
            }
        });

       function allotLocker() {
            $.ajax({
                 headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url: '/allotLocker',
          //  data: {time_slot_id:time_slot_id},
            // dataType: 'html',
            success: function (data) {
               $("#LockerContainer").html(data);
            }
             }); 
            
        }
        
        function assignedLocker(library_locker_id,locker_name){
                	$("#library_locker_id").val(library_locker_id);
                	$("#library_locker_name").val(locker_name);
                    $(".closeModalLocker").click();
		}
		
		$(document).ready(function() {
		    
		    $('#library_amount,#discountValue,#locker_fees_amount').on('input', function() {
                calculateDiscountAndDues();
            });
            
            $('#locker_fees_amount').change(function(){
               var values = $(this).val();
               if(values == ""){
                   $(this).val(0);
               }
            });
		    
		    
         $('#discountType').change(function() {
            // When the discount type changes, update discount amount and recalculate the dues
            const discountType = document.getElementById("discountType").value;
            const discountValueInput = document.getElementById("discountValue");

            if (discountType === "") {
                discountValueInput.readOnly = true;
                discountValueInput.value = "0";
            } else {
                discountValueInput.readOnly = false;
            }

            calculateDiscountAndDues();
         });
         
function calculateDiscountAndDues() {
            var totalAmount = parseFloat($('#library_amount').val());
            var discountType = $('#discountType').val();
            var discountValue = parseFloat($('#discountValue').val());
            var totalPaid = parseFloat($('#amount').val());
            
            if($('#locker_fees').prop('checked')){
                var lockerAmt = parseFloat($('#locker_fees_amount').val());
            }else{
                var lockerAmt = 0;
            }
            // alert(totalAmount + " " + discountType + " " + discountValue + " " + totalPaid);
            
            
                if(discountValue > totalAmount){
                    alert("Invalid Calculation");
                    $('#discountValue').val(0);
                    $('#totalPayableAmount').val(totalAmount);
                    $('#amount').val(totalAmount);
                    $('#duesAmount').val(totalAmount);
                    $('#discountAmount').val(0);
                }else{
                    if(discountType == "value"){
                        var finalAmt = (totalAmount - discountValue) + lockerAmt;
                        $('#discountAmount').val(discountValue);
                        $('#totalPayableAmount').val(finalAmt);
                        $('#amount').val(finalAmt);
                        $('#duesAmount').val(0);
                    }else{
                        var finalAmt = (totalAmount - ((totalAmount*discountValue)/100)) + lockerAmt;
                        $('#discountAmount').val((totalAmount*discountValue)/100);
                        $('#totalPayableAmount').val(finalAmt);
                        $('#amount').val(finalAmt);
                        $('#duesAmount').val(0);
                    }
                }
            
        }
        
        $('#amount').on('input', function() {
                var amount = parseFloat($(this).val());
                var totalPayableAmount = parseFloat($('#totalPayableAmount').val());
                
                var finalAmt = totalPayableAmount - amount;
                
                $('#duesAmount').val(finalAmt);
                
            });
        });
</script>

<script>
$(document).ready(function() {
   	$('#is-invalid').click(function(e){
    e.preventDefault();
    var all_filled_up = 2000;
    $('.invalid').each(function(){
        var this_value = $(this).val();
        var this_id = $(this).attr('id'); 
        if(this_value == ''){
            $('#' + this_id + '_invalid').css('display','block');
            all_filled_up = all_filled_up + ' && ' + this_value;
        }else{
            $('#' + this_id + '_invalid').css('display','none');
        }
    })
    if(all_filled_up == 2000){
        var total_amt = $('#total_amount').val();
        if(total_amt == ""){
           toastr.error('First Assign Fees');
        }else{
        $('#submit_form').trigger('submit');
        }
    }else{
        $("html, body").animate({ scrollTop: 0 }, "slow");
    }
});
});
</script>


@endsection      