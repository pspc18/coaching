@php
$getLibrary = Helper::getLibrary();
$getgenders = Helper::getgender();
$getState = Helper::getState();
$getCity = Helper::getCity();
$getCountry = Helper::getCountry();
 $getPaymentMode = Helper::getPaymentMode();
@endphp
@extends('layout.app') 
@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Change Plan</h3>
            </div>

             <form action="{{ url('change_user_plan') }}/{{ $data->id }}/{{ $data->admission_id }}" method="post">
                @csrf

                <div class="card-body">
                <div class="row">
                    <input type="hidden" placeholder="cabin_id" id="submit_cabin_id" name="submit_cabin_id">
                    <input type="hidden" placeholder="Time Slot id" id="submit_time_slot_id" name="submit_time_slot_id">
                    <input type="hidden" id="submit_renew_date" name="submit_renew_date">
            		<input type="hidden" id="cabin_id">
            		<input type="hidden" id="cabin_name">
            		<div class="row">
						<label class="control-label required-field" for="appendprepend">Select Time Slot</label>
						<div class="col-md-12">
							<div class="form-group">
								<div class="slots-container">
									<div class="row">
									     @if(!empty($time_slot))
                                          @foreach($time_slot as $type)
									            <div class="col-md-3 mt-3">
									                <div class="slot {{ in_array($type->id, $blocked_slot) ? 'disabled_box' : '' }}" id="slot_{{$type->id}}">
											<h4>{{$type->study_time ?? ''}}</h4>
											<div class="plan">
											    <input type="checkbox" name="time_slot_id" class="time_slot" {{ in_array($type->id, $blocked_slot) ? "disabled" : "" }} id="time_slot_id" value="{{ $type->id ?? ''}}"  data-price="{{$type->amount ?? ''}}" data-id="{{ $type->id ?? ''}}" >
											    <label for="time_slot_id_{{ $type->id ?? ''}}" class="ml-1"> 1 Month</label>
											    <span class="price"> <b>Rs. {{$type->amount ?? ''}}</b></span></div>
											    <div class="reletive_div">
    											    <div class="already_booked">
    											        <p>Already Booked</p>
    											    </div>
        											<div class="fillable_data">
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
                                                                    
                                                                    <input type="date" class="form-control input-sm renew_date" placeholder="Enter Renewal Date" name="renew_date" id="renew_date_{{$type->id}}">
                                                                </div>
                                                                </div>
            												</div>
            							                    </div>
            											<div class="form-group">
            												<label for="selectedSeat">Selected Seat:</label>
            												<input type="text" class="form-control selectedSeat" id="selectedSeat_{{$type->id ?? ''}}" name="" readonly="">
            												<input type="hidden" class="form-control cabin_seat_id" id="cabin_seat_id_{{$type->id ?? ''}}" name="cabin_seat_id" readonly="">
            											</div>
            											<div>
            												<button type="button" class="btn btn-primary btn-sm seat_allot" id="seat_allot_{{ $type->id ?? '' }}" disabled data-toggle="modal" data-target=".bd-example-modal-lg" onclick="allotSeatInSlot({{$type->id ?? ''}});">Allot Seat</button>
            											</div>
        							                </div>
    											</div>
    											
										</div>
									            </div>
										  @endforeach
                                       @endif													
									</div>
									
									</div>
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
    
		$(document).ready(function(e) {
		    
		    $('.time_slot').on("change", function() {
                var renewal_date =  "{{ $data->renew_date ?? ''}}";
                var time_slot_id = $(this).data("id");
                
                $('.time_slot').prop('checked',false);
                
                $('.slot').removeClass('plan-selected');
                
                $('#submit_time_slot_id').val("");
                $('#submit_renew_date').val("");
                $('#submit_cabin_id').val("");
                
                $('.plan').siblings('.form-group').children('.renew_date').val("");
                $('.renew_date').val("");
                $('.selectedSeat').val("");
                $('.cabin_seat_id').val("");
                $('#cabin_id').val("");
                $('#cabin_name').val("");
                $('.seat_allot').attr('disabled',true);
                $('.renew_date').attr('disabled',true);
                        
                $(this).prop('checked',true);
                
                var old_slot_id = parseInt('{{ $data->library_time_slot_id }}');
                var library_cabin_seat_name =  "S-{{ $data->library_cabin ?? '' }}";
                var library_cabin_seat_id =  "{{ $data->library_cabin_id ?? '' }}";
                $(this).closest('.slot').addClass('plan-selected');
                
                $('#renew_date_' + time_slot_id).val(renewal_date);
                $('#renew_date_' + time_slot_id).attr('disabled',false);
                $('#seat_allot_' + time_slot_id).attr('disabled',false);
                
                $('#submit_time_slot_id').val(time_slot_id);
                $('#submit_renew_date').val(renewal_date);
              
                if(old_slot_id == time_slot_id){
	               $('#selectedSeat_' + time_slot_id).val(library_cabin_seat_name);
                   $('#cabin_seat_id_' + time_slot_id).val(library_cabin_seat_id);
                   $('#submit_cabin_id').val(library_cabin_seat_id);
		       }
			});
			
			$('.renew_date').change(function(){
			   $('#submit_renew_date').val($(this).val());
			});
			
		        
		    $('.time_slot').each(function(){
		       var slot_id = parseInt('{{ $data->library_time_slot_id }}');
		       var curruntSlotId = parseInt($(this).val());
	           var library_cabin_seat_name =  "S-{{ $data->library_cabin ?? '' }}";
               var library_cabin_seat_id =  "{{ $data->library_cabin_id ?? '' }}";
               var time_slot_id = $(this).data("id");
                    
                        
		       
		       if(slot_id == curruntSlotId){
		           $('#selectedSeat_' + time_slot_id).val(library_cabin_seat_name);
                   $('#cabin_seat_id_' + time_slot_id).val(library_cabin_seat_id);
		           $(this).click();
		       }
		       
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
                $('#submit_cabin_id').val(cabin_id);
            }
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
			gap: 10px;
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
		
		.disabled_box{
		    background-color: darkgray;
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