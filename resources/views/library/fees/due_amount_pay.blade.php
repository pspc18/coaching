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
                <h3 class="card-title">Pay Payment </h3>
            </div>

             <form action="{{ url('library_due_amount_pay') }}/{{$data->id}}" method="post">
                @csrf
                
                <div class="card-body">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h5 class="">Invoice Details</h5>
                        </div> 
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Name:</strong> {{$data->first_name ?? '' }}<br>
                                    <strong>Admission No:</strong> {{$data->admissionNo ?? '' }}<br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Mobile:</strong> {{$data->mobile ?? '' }}<br>
                                    <strong>Dues Amount:</strong> ₹{{$data->due_amount ?? '' }}<br>
                                </div>
                            </div>
                        </div>
                    </div>
            		<div class="row">
                        <div class="col-md-4">
						    <div class="form-group">
                                <label >Total Amount:</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                         <input class="form-control @error('amount') is-invalid @enderror" readonly="" type="number" name="library_amount" id="library_amount" value="{{$data->due_amount ?? ''}}"> 
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
                            <div class="col-md-6">
                            <label for="totalPayableAmount">Total Payable Amount:</label>
                               <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            Rs.
                                        </span>
                                    </div>
                                    <input class="form-control" type="number" name="totalPayableAmount" id="totalPayableAmount" readonly="" value="{{$data->due_amount ?? ''}}">
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
                                    <input class="form-control" type="text" name="amount"  id="amount" autocomplete="off"  value="{{$data->due_amount ?? ''}}">
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
                            
                            
                            
                            
                            
                        
                        <div class="col-md-12   text-center mt-2">
                             <button type="submit" class=" btn btn-primary ">Submit</button>
                        </div>
                        
                  </div>
                    

                </div>

               
            </form>
        </div>
    </div>

    
</div>

        </div>
    </section>
</div>


  


<script>
    		$(document).ready(function() {
		    
		    $('#library_amount,#discountValue,#locker_fees_amount').on('input', function() {
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
    
            var totalAmount = parseFloat($('#library_amount').val());
            var discountType = $('#discountType').val();
            var discountValue = parseFloat($('#discountValue').val());
            var totalPaid = parseFloat($('#amount').val());
           
        if (!isNaN(discountValue)) {
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
                        $('#totalPayableAmount').val(finalAmt);
                        $('#amount').val(finalAmt);
                        $('#duesAmount').val(0);
                    }else{
                        var finalAmt = (totalAmount - ((totalAmount*discountValue)/100));
                        $('#discountAmount').val((totalAmount*discountValue)/100);
                        $('#totalPayableAmount').val(finalAmt);
                        $('#amount').val(finalAmt);
                        $('#duesAmount').val(0);
                    }
                }
          }else{
            //$('#discountValue').val(0);
            $('#totalPayableAmount').val(totalAmount);
            $('#amount').val(totalAmount);
            $('#duesAmount').val(totalAmount);
            $('#discountAmount').val(0);  
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