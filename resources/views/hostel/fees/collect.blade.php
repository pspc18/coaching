@php
$getstudents = Helper::getstudents();
$classType = Helper::classType();
$getPaymentMode = Helper::getPaymentMode();
@endphp
@extends('layout.app')
@section('content')
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">


         <div class="card card-primary">
            <div class="card-header bg-primary">
               <h5 class="card-title">{{ __('hostel.Student Fees Pay')}}</h5>
               <div class="card-tools">
                  <a href="{{url('hostel/fees/view')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-eye"></i> {{ __('common.View')}}</a>
                  <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{ __('common.Back')}}</a>
               </div>
            </div>
            <div class="card-body">
               <div class="row">
                  <div class="col-md-4">
                     <div class="form-group">
                        <label>{{ __('hostel.Select Student')}}<font style="color:red"><b>*</b></font></label>
                        <form id="studentDetailsForm" method="post" action="{{ url('hostel/collect/fees') }}">
                           @csrf
                           <select name="student_details" id="student_details" class="form-control select2 ">
                              <option value="">{{ __('common.Select')}}</option>

                              @if(!empty($allstudents))
                              @foreach($allstudents as $value)
                              <option value="{{ $value->admission_id }}" {{ ( $value->admission_id == $serach['student_details'] ?? '' ) ? 'selected' : '' }}>{{ $value->first_name ?? ''}} {{ $value->last_name ?? ''}}</option>
                              @endforeach
                              @endif
                           </select>
                        </form>
                     </div>
                  </div>
                  
                  <div class="col-md-8"></div>
                  <div class="col-md-2 ">
                     <div class="form-group">
                        <label>{{ __('hostel.Student Name')}}</label>
                        <input type="text" name="student_name" placeholder="{{ __('hostel.Student Name')}}" placeholder="Student Name" id="student_name" class="form-control" readonly="" value="{{$data->first_name ?? ''}} {{ $data->last_name ?? ''}}">
                        <input type="hidden" name="mobile" id="mobile" value="{{$data->mobile ?? ''}} ">
                        <input type="hidden" name="email" id="email" value="{{$data->email ?? ''}} ">
                     </div>
                  </div>
                  <div class="col-md-2">
                     <div class="form-group">
                        <label>{{ __('hostel.Student Father Name')}}</label>
                        <input type="text" name="father_name" placeholder="{{ __('hostel.Student Father Name')}}" id="father_name" class="form-control" readonly="" value="{{$data->father_name ?? ''}}">
                     </div>
                  </div>
                  <div class="col-md-2 ">
                     <div class="form-group">
                        <label>{{ __('hostel.Room Category')}}</label>
                        <input type="text" name="room_category" placeholder="Room Category" id="room_category" class="form-control" readonly="" value="{{$data->room_category ?? ''}}">
                     </div>
                  </div>

                  
                  <div class="col-md-2 ">
                     <div class="form-group">
                        <label>{{ __('hostel.Admission No.')}}</label>
                        <input type="text" name="HostelNo" id="HostelNo" class="form-control" readonly="" value="{{$data->admissionNo ?? ''}}">
                     </div>
                  </div>
                  <div class="col-md-2 ">
                     <div class="form-group">
                        <label>{{ __('hostel.Room Name')}}</label>
                        <input type="text" name="room_name" id="room_name" class="form-control" readonly="" value="{{$data->room_name ?? ''}}">
                     </div>
                  </div>

                  
               </div>
            </div>
         </div>
         @if(!empty($data))
         <form id="quickForm" method="post" action="{{ url('hostel/fees/pay') }}">
            @csrf
            <input type="hidden" name="admission_id" value="{{$data['admission_id'] ?? ''}}" readonly class="form-control">
            <input type="hidden" name="hostel_assign_id" value="{{$data['id'] ?? ''}}" readonly class="form-control">
            <div class="card card-primary" id="payment_detail">
               <div class="card-header">
                  <h5 class="card-title">{{ __('hostel.Payment Detail')}}</h5>
               </div>
               <div class="card-body">
                  <div class="row table-responsive">
                     <div class="col-md-12">
                        <div class="row">
                           <div class="col-md-2">
                              <div class="form-group">
                                 <label>{{ __('hostel.Receipt No')}}</label>
                                 <input type="number" name="receipt_no" placeholder="" value="{{$BillCounter ?? ''}}" readonly class="form-control">
                              </div>
                           </div>
                            @php
                                if (!empty($data)) {
                                    $registrationDate = $data->hostel_renewal_date;
                                    $regDate = Carbon\Carbon::parse($registrationDate);
                                    if (!empty($regDate)) {
                                        $dateObj = $regDate;
                                    } else {
                                        $dateObj = Carbon\Carbon::now();
                                    }
                            
                                    $modifiedDate = $dateObj->addDays(30)->toDateString();
                                } else {
                                    $dateObj = Carbon\Carbon::now();
                                    $modifiedDate = $dateObj->addDays(30)->toDateString();
                                   
                                }
                            @endphp


                           <div class="col-md-2" >
                    			<div class="form-group">
                        			    <label>Renewal Date</label>
                            			<div class="input-group">
                							    <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i class="fa fa-calendar"></i>
                                                    </span>
                                                </div>
                        				<input type="date" class="form-control" id="hostel_renewal_date" name="hostel_renewal_date" value="{{ $modifiedDate ?? '' }}">
                                        </div>
                    			</div>
                	        </div>
                           
                	        <div class="col-md-3">
						    <div class="form-group">
                                <label >Total Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                         <input class="form-control @error('amount') is-invalid @enderror" type="number" name="hostel_amount" id="hostel_amount" value="{{$data->hostel_fees ?? '0'}}"> 
                                        @error('amount')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
							</div>
                	        </div>
                           <div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountType">Discount Type:</label>
                                    <select class="form-control" id="discountType" name="discountType" >
                                        <option value="">Select Discount Type</option>
                                        <option value="value">Flat Discount</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="discountValue">Discount Value:</label>
                                    <input type="number" class="form-control" id="discountValue" name="discountValue" value="0" readonly="">
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            
                            <div class="col-md-3">
                              <div class="form-group">
                                 <label>{{ __('hostel.Discount Remark')}}</label>
                                 <input type="text" name="discount_remark" placeholder="{{ __('hostel.Discount Remark')}}" value="" id="discount_remark" class="form-control">
                              </div>
                           </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-3">
                            <label for="totalPayableAmount">Total Payable Amount:</label>
                               <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="totalPayableAmount" id="totalPayableAmount" readonly="" value="{{$data->hostel_fees ?? '0'}}">
                                    <!-- <span class="input-group-addon">.00</span> -->
                                </div>
                            </div>
                        <div class="col-md-3">
                             <label for="totalPayableAmount"> Payable Amount:</label>
                            <div class="input-group">
                           
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="text" name="amount" value="{{$data->hostel_fees ?? '0'}}" id="amount" autocomplete="off" data-gtm-form-interact-field-id="2">
                                </div>
                            </div>
                         <div class="col-md-3">
                            <div class="form-group">
                             <label>{{ __('hostel.Payment Mode')}}</label>
                             <select class="form-control" id="payment_mode_id" name="payment_mode_id" onchange="payment_mode_function(this.value);" required>

                                @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                                @endif
                             </select>
                            </div>
                       </div>
                        <div class="col-md-3">
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
                        
                       <div class="col-md-4  payment_info_class" style="display: none;">
                          <div class="form-group">
                             <label>{{ __('hostel.Payment Info') }}</label>
                             <input type="text" name="payment_info" id="payment_info" value="" class="form-control">
                          </div>
                       </div>
                           
                       <div class="col-md-12">
                          <center><button type="submit" class="btn btn-success pay">{{ __('common.Submit')}}</button></center>
                       </div>
                    </div>
                     </div>
                  </div>
               </div>
            </div>

         </form>
         <div class="card card-primary">
            <div class="card-header">
               <h5 class="card-title">{{ __('hostel.Student Fees Detail') }}</h5>
            </div>
            <div class="card-body">
               <div class="row table-responsive" id="fees_list_details">
                  <div class="col-md-12">
                     <table class="table table-bordered table-striped dataTable no-footer" role="grid" style="margin-left: 0px;">
                        <thead>
                           <th>{{ __('common.SR.NO')}}</th>
                           <th>{{ __('common.Date')}}</th>
                           <th>{{ __('Total Amount')}}</th>
                           <th>{{ __('Paid Amount')}}</th>
                           <th>{{ __('hostel.Discount') }}</th>
                           <th>{{ __('Due Amount')}}</th>
                        </thead>
                        @if(!empty($feesDetail))
                        @php
                        $i = 1;
                        @endphp
                        @foreach($feesDetail as $detail)
                        <tr role="row">
                           <td><a href="{{url('hostel_invoice')}}/{{ $detail->invoice_no }}/{{ $detail->admission_id }}" target="blank" target="blank" title="Fees Print">{{ $detail->invoice_no ?? '' }}</a></td>
                           <td>{{ date('d-m-Y', strtotime($detail->created_at)) ?? '' }}</td>
                           <td>{{ $detail->total_amount ?? '' }}</td>
                           <td>{{ $detail->paid_amount ?? '' }}</td>
                           <td>{{ $detail->discount ?? '' }}</td>
                           <td>{{ $detail->due_amount ?? '' }}</td>
                        </tr>
                        @endforeach
                        @endif
                     </table>
                  </div>
               </div>
            </div>
         </div>
         @endif
      </div>
   </section>
</div>
<script>
   $(document).ready(function() {

      $('#student_details').change(function() {
         var student_details = $(this).val();
         if (student_details != '') {
            $('#studentDetailsForm').trigger('submit');
         } else {
            window.location.href = 'fees';
         }
      })
   });


   function payment_mode_function(value) {
      if (value == 1) {
         $(".payment_info_class").hide();
         $("#payment_info").val('');
         $("#office_account_info").val('');

         $("#office_account_info").attr('required', false);
      } else {
         $(".payment_info_class").show();

         $("#office_account_info").attr('required', true);
      }
   }
</script>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

    
<script>
   $('#payment_mode_id').on('change', function() {

      if (this.value == 9) {

         var total_amount = jQuery('#total_paid').val();

         // console.log(result);
         var options = {
            "key": "{{ Config::get('app.razorpay_key') }}",
            "amount": total_amount * 100,

            "currency": "INR",
            "name": "Rukmani Software",
            "description": "Live Transaction",
            "image": "https://www.rukmanisoftware.com/public/assets/img/header-logo.png",
            "handler": function(response) {
               $("#transaction_id").val(response.razorpay_payment_id);
            }
         };

         var rzp1 = new Razorpay(options);
         rzp1.open();

      }

   });

   
</script>

<script>
		$(document).ready(function() {
		    
		    $('#hostel_amount,#discountValue,#locker_fees_amount').on('input', function() {
		        
                calculateDiscountAndDues();
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
            var totalAmount = parseFloat($('#hostel_amount').val());
            var discountType = $('#discountType').val();
            var discountValue = parseFloat($('#discountValue').val());
            var totalPaid = parseFloat($('#amount').val());

                if(discountValue > totalAmount){
                    alert("Invalid Calculation");
                    $('#discountValue').val(0);
                    $('#totalPayableAmount').val(totalAmount);
                    $('#amount').val(totalAmount);
                    $('#duesAmount').val(totalAmount);
                    $('#discountAmount').val(0);
                }else{
                    if(discountType == "value"){
                        var finalAmt = (totalAmount - discountValue);
                        $('#discountAmount').val(discountValue);
                    }else{
                        var finalAmt = (totalAmount - ((totalAmount*discountValue)/100));
                        $('#discountAmount').val((totalAmount*discountValue)/100);
                    }
                    
                    
                    if (isNaN(finalAmt)) {
                        $('#totalPayableAmount').val(0);
                        $('#amount').val(0);
                        $('#duesAmount').val(0);
                    }else{
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




@endsection