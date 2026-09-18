@php
$getLibrary = Helper::getLibrary();
$getPaymentMode = Helper::getPaymentMode();
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
                <h3 class="card-title">Locker Assign</h3>
            </div>

             <form action="{{ url('book_locker') }}" method="post">
                @csrf
                <div class="card-body">
                <div class="row">
                	<div class="col-md-4" >
            			<div class="form-group">
                			    <label>Payment Date</label>
                    			<div class="input-group">
        							    <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fa fa-calendar"></i>
                                            </span>
                                        </div>
                				<input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}">
                            </div>
            			</div>
                	</div>
                	
                	
                    <div class="col-md-12 p-0">
                        <div class="col-md-6">
    						<div class="form-group">
    							<label>{{ __('User') }}</label>
    							<select class="form-control select2" name="library_assign_id" id="library_assign_id">
    								<option value="">{{ __('common.Select') }}</option>
    								@if(!empty($data))
    								@foreach($data as $item)
    								<option value="{{ $item->id ?? ''  }}" {{ ($item->id == old('library_assign_id')) ? 'selected' : '' }}>{{ $item->admissionNo." --". $item->first_name ?? ''  }}</option>
    								@endforeach
    								@endif
    							</select>
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
                    <button type="submit" class="btn btn-primary">Submit</button>
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
    $(document).ready(function(){
   	    $('#locker_fees').on("click", function() {
           var payAmt = parseFloat($('#totalPayableAmount').val());
           var lockerFees = parseFloat($('#locker_fees_amount').val());
           
            if ($(this).prop('checked')) {
                var finalAmt = payAmt + lockerFees;
                var dateObj = new Date();
			    var numberOfDaysToAdd = 30;
			    dateObj.setDate(dateObj.getDate() + numberOfDaysToAdd);
			    var modifiedDate = dateObj.toISOString().split('T')[0]; 
                $("#lockerRenewalDate").val(modifiedDate);
           }else{
               $('#lockerRenewalDate').val("");
               var finalAmt = 0;
           }
           
           $('#totalPayableAmount').val(finalAmt);
           $('#amount').val(finalAmt);
           $('#duesAmount').val(0);
		}); 
		
		
		
		$('#library_assign_id').change(function(){
            var library_assign_id = $(this).val();
                $('#library_locker_name').val("");
                $('#library_locker_id').val("");
                $('#locker_fees_amount').val(100);
                   
                if($('#locker_fees').prop('checked')){
                    $('#locker_fees').click();
                }
              
                if(library_assign_id != ""){
                    $.ajax({
                        headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                        type:'post',
                        url: '/get_locker_details',
                        data: {library_assign_id:library_assign_id},
                        success: function (data) {
                           if(data.length != 0){
                               $('#locker_fees_amount').val(data.locker_amount);
                               $('#library_locker_id').val(data.library_locker_id);
                               $('#library_locker_name').val(data.locker_name);
                               $('#locker_fees').click();
                               
                               if(data.locker_renewal_date != ""){
                                   var dateObj = new Date();
                               }else{
                                    var dateObj = new Date(data.locker_renewal_date);
                               }
                               var numberOfDaysToAdd = 30;
            				   dateObj.setDate(dateObj.getDate() + numberOfDaysToAdd);
            				   var modifiedDate = dateObj.toISOString().split('T')[0];
            				   $('#lockerRenewalDate').val(modifiedDate);
                           }
                        }
                    }); 
                }
                
            });
		
		
		
		
		
		
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
        
        function assignedLocker(library_locker_id){
                	$("#library_locker_id").val(library_locker_id);
                	$("#library_locker_name").val('L-'+library_locker_id);
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
		    
         
            function calculateDiscountAndDues() {
                var totalPaid = parseFloat($('#amount').val());
                
                if($('#locker_fees').prop('checked')){
                    var lockerAmt = parseFloat($('#locker_fees_amount').val());
                    $('#totalPayableAmount').val(lockerAmt);
                    $('#amount').val(lockerAmt);
                    $('#duesAmount').val(0);
                }else{
                    var lockerAmt = 0;
                    $('#totalPayableAmount').val(lockerAmt);
                    $('#amount').val(lockerAmt);
                    $('#duesAmount').val(0);
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
<style>
    
		.slots-container {
			display: flex;
			flex-wrap: wrap;
			gap: 15px;
		}

		.slot-row {
			display: flex;
			flex-basis: 100%;
			flex-wrap: wrap;
			gap: 15px;
		}

		.slot {
			background-color: #f1f1f1;
            padding: 15px;
            border-radius: 10px;
            flex-grow: 1;
            border: 1px solid #adadad;
            box-shadow: 0px 0px 2px #53535380 inset;
		}
            .reletive_div{
                position: relative;
            }
		.overflow_scroll_plans{
            height: 450px;
            overflow-x: scroll;
            border: 1px solid black;
            padding: 10px;
            margin: 20px 0px;
            border-radius:4px;
		}

		h4 {
			margin: 0;
		}

		.plan {
			  display: flex;
              align-items: center;
              margin-top: 10px;
              margin-bottom: 5px;
              background: #53a4fa36;
              padding: 6px;
              border-radius: 4px;
		}

		.plan input[type="radio"] {
			/* margin-right: 10px; */
			/* margin: 0 10px 4px 0; */
			margin: 0 10px 0px 0;
			/* display: none; */
		}

		.plan input[type="radio"]+label {
			cursor: pointer;
			user-select: none;
			margin: 0;
		}

		.plan input[type="radio"]:checked+label {
			font-weight: bold;
		}

		.price {
			margin-left: auto;
		}

		.discount {
			color: #999;
			font-size: 12px;
		}

		.slot h4 {
			font-size: 16px;
			font-weight: 700;
		}

		.slot-subscription-plans {
			background-color: #fff9c2;
		}

		.seat {
			display: inline-block;
			border: 1px solid black;
			width: 55px;
			border-radius: 8px;
			height: 61px;
			text-align: center;
			cursor: pointer;
			margin: 5px;
			position: relative;
			font-weight: 700;
			background: #d9edf7;
			background-image: url(https://www.walsisindia.com/library/images/car-seat-available.png);
			background-size: 38px;
			background-position: bottom;
			background-repeat: no-repeat;
			margin-bottom: 20px;
			user-select: none;
		}

		.seat:hover {
			background-color: #a3d977;
		}

		.seat:hover::before {
			opacity: 1;
		}

		.assigned {
			background-image: url(https://www.walsisindia.com/library/images/car-seat-occupied.png);
		}

		.color-s1 {
			color: #2196F3;
		}

		.color-s2 {
			color: yellowgreen;
		}

		.color-s3 {
			color: tomato;
		}

		/* Styling for seat map modal */
		.modal-lg {
			max-width: 80%;
		}

		.seat {
			display: inline-block;
			border: 1px solid #ccc;
			text-align: center;
			cursor: pointer;
			margin: 5px;
		}

		.seat.selected {
			background-color: #007bff;
			color: #fff;
		}

		#seatMapContainer {
			display: inline-flex;
			flex-wrap: wrap;
			justify-content: center;
		}

		.modal.in .modal-dialog {
			margin: 0 auto;
		}

		.modal-title {
			float: left;
		}

		.legend {
			display: flex;
			justify-content: center;
			margin-bottom: 10px;
		}

		.legend-item {
			display: flex;
			align-items: center;
			margin-right: 20px;
		}

		.legend-item img {
			width: 20px;
			height: 20px;
			margin-right: 5px;
		}

		.file-upload {
			position: relative;
			width: 125px;
			height: 125px;
			border: 2px dotted #ccc;
			background: #f5f5f5;
			border-radius: 10px;
			/* overflow: hidden; */
			display: inline-block;
			cursor: pointer;
		}

		.file-upload input[type='file'] {
			position: absolute;
			left: 0;
			top: 0;
			opacity: 0;
			width: 100%;
			height: 100%;
			cursor: pointer;
		}

		.file-upload img {
			width: 100%;
			height: 100%;
			object-fit: contain;
			border-radius: 10px;
		}

		.file-upload .upload-icon {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			font-size: 40px;
			color: #ccc;
		}

		.file-upload .remove-icon {
			position: absolute;
			top: -10px;
			right: -10px;
			font-size: 18px;
			color: #fff;
			background-color: #ff0000;
			border-radius: 50%;
			padding: 4px;
			cursor: pointer;
			display: none;
			z-index: 1;
			width: 25px;
			height: 25px;
			text-align: center;
			/* Ensure the remove icon is above the image */
		}

		.file-upload .remove-icon:hover {
			background-color: #cc0000;
		}

		.profile-label {
			font-size: 16px;
			color: #ccc;
			display: flex;
			flex-wrap: wrap;
			justify-content: center;
			line-height: 3;
		}

		.image-upload-justify-center {
			display: flex;
			justify-content: center;
		}

		.plan-selected {
			background-color: #c9fae5;
			border: 1px solid #c9fae5;
		}

		.locker {
			display: inline-block;
			border: 1px solid black;
			width: 55px;
			border-radius: 8px;
			height: 55px;
			text-align: center;
			cursor: pointer;
			margin: 5px;
			position: relative;
			font-weight: 700;
			background: #d9edf7;
			background-image: url(https://www.walsisindia.com/library/images/safe-available.png);
			background-size: 32px;
			background-position: bottom;
			background-repeat: no-repeat;
			margin-bottom: 20px;
			user-select: none;
		}

		.locker {
			display: inline-block;
			border: 1px solid #ccc;
			text-align: center;
			cursor: pointer;
			margin: 5px;
		}

		.locker:hover {
			background-color: #a3d977;
		}

		.locker:hover::before {
			opacity: 1;
		}

		.locker.selected {
			background-color: #007bff;
			color: #fff;
		}

		.locker.assigned {
			background-image: url(https://www.walsisindia.com/library/images/safe-occupied.png);
		}

		#LockerContainer {
			display: inline-flex;
			flex-wrap: wrap;
			justify-content: center;
		}

		.width-30p {
			width: 30%;
		}

		.display-flex {
			display: flex;
			justify-content: flex-start;
			align-items: center;
			gap: 5px;
			cursor: pointer;
		}

		.we-checkbox {
			width: 20px;
			height: 20px;
			cursor: pointer;
			margin: 0 !important;
		}

		/* Style for the legend container */
		.seat-legend-container {
			text-align: center;
			margin-bottom: 10px;
			width: 100%;
		}

		/* Style for each legend item */
		.seat-legend-item {
			display: inline-block;
			margin: 5px;
			padding: 5px 10px;
			border-radius: 5px;
			font-size: 1.4rem;
			font-weight: bold;
			color: #fff;
			background-color: #3498db;
			text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			cursor: pointer;
			transition: background-color 0.3s, transform 0.3s;
			user-select: none;
		}

		.seat-legend-item:hover {
			background-color: #2980b9;
			transform: scale(1.1);
		}

		.plan-badge {
			position: relative;
			/* background: #ebe6f2; */
			background: rgba(0, 0, 0, .075);
			padding: 5px;
			border-radius: 5px;
			margin-bottom: 5px;
		}

		.premium-badge {
			position: relative;
			background-color: #ff4465;
			color: #fff;
			padding: 5px 10px 5px 10px;
			font-size: 12px;
			font-weight: 600;
			transform: translateX(-50%);
			border-radius: 5px 0px 5px 0px;
			margin-left: -5px;
			top: -2px;
		}

		.premium-badge-1 {
			position: relative;
			background-color: #ff4465;
			color: #fff;
			padding: 5px 10px 5px 10px;
			font-size: 12px;
			font-weight: 600;
			transform: translateX(-50%);
			border-radius: 5px 0px 5px 0px;
			margin-left: -5px;
			top: -2px;
		}

		.premium-badge-2 {
			position: relative;
			background-color: #777;
			color: #fff;
			padding: 5px 10px 5px 10px;
			font-size: 12px;
			font-weight: 600;
			transform: translateX(-50%);
			border-radius: 5px 0px 5px 0px;
			margin-left: -5px;
			top: -2px;
		}


		.plan-selected .plan-badge {
			background: #a5eece;
		}

		.plan-badge .plan {
			margin-top: 5px;
		}

		.multi-plan {
			margin-left: 5px;
			margin-right: 5px;
		}

		.multi-plan-label {
			margin-bottom: 0px;
		}

		.jconfirm.jconfirm-supervan .jconfirm-box .jconfirm-buttons button {
			height: auto;
		}

		@media (min-width: 992px) {
			.jc-bs3-row {
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				margin-right: -15px;
				margin-left: -15px;
			}

			.justify-content-lg-center {
				-ms-flex-pack: center !important;
				justify-content: center !important;
			}
		}

		@media (min-width: 768px) {
			.jc-bs3-row {
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				margin-right: -15px;
				margin-left: -15px;
			}

			.justify-content-md-center {
				-ms-flex-pack: center !important;
				justify-content: center !important;
			}
		}

		@media (min-width: 576px) {
			.jc-bs3-row {
				display: flex;
				-ms-flex-wrap: wrap;
				flex-wrap: wrap;
				margin-right: -15px;
				margin-left: -15px;
			}

			.justify-content-sm-center {
				-ms-flex-pack: center !important;
				justify-content: center !important;
			}
		}
	
</style>

@endsection      