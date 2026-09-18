 @php
$getMonth = Helper::getMonth();
$getPaymentMode = Helper::getPaymentMode();
use Carbon\Carbon;
@endphp             
        
 
 <div class="row">
     <div class="col-12 col-md-12 text-center"> <h3>Electricity Bill Pay</h3></div>
      <div class="col-12 col-md-12">
            <form action="{{ url('hostel/fees/electricity/pay') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="card trans_card _table table-striped table-bordered" id="tableId">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                  @php      
                                 
                                    $value = Helper::getBillDetails($data['stuData']['date'],$data['stuData']['end_date'],$data['stuData']['hostel_assign_id'] ,$data['stuData']['admission_id'] ,$hostel_room_id,$floor_id,$building_id,$hostel_id,$data['stuData']['id']); 
                                    //dd($data);
                                @endphp

                                 <input type="hidden" name="admission_id" id="admission_id" value ="{{$data['stuData']['admission_id'] ?? ''}}"  />
                                 <input type="hidden" name="hostel_assign_id" id="hostel_assign_id" value ="{{$data['stuData']['id'] ?? ''}}"  />

                                <div class="col-md-3">
                                     <label>{{ __('hostel.Select Month') }}</label>
                    				<select class=" form-control month_id" id="month_id" name="month_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                         @if(!empty($getMonth)) 
                                          @foreach($getMonth as $key=> $type)
                                             <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                                </div>
                               
                               
                                <div class="col-md-3">
                                    <label class="">{{ __('Last Month Meter Unit') }}</label>
                                    <input type="text" class="form-control last_meter_unit" id='last_meter_unit' placeholder="Last Month Meter Unit" value='0'name="last_meter_unit" readonly required>
                                </div>
                                <div class="col-md-3">
                                    <label class="">{{ __('This Month Meter Unit') }}</label>
                                    <input type="text" class="form-control this_meter_unit" id='this_meter_unit' placeholder="This Month Meter Unit" value=''name="this_meter_unit"  required>
                                </div>
                                <div class="col-md-3">
                                    <label class="">{{ __('Unit') }}</label>
                                    <input type="text" class="form-control meter_unit" id='meter_unit' placeholder="Meter Unit" value=''name="meter_unit" readonly required>
                                </div>
                                <div class="col-md-3">
                                    <label class="">{{ __('Per Unit Rate') }}</label>
                                    <input type="text" class="form-control per_unit_rate" id='per_unit_rate' placeholder="Per Unit Rate" value=''name="per_unit_rate" onkeypress="javascript:return isNumber(event)"  required>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="text-danger">{{ __('Amount') }}*</label>
                                       <input type="text" class="form-control" name="pay_amount" id="pay_amount" placeholder="Amount" value="" onkeypress="javascript:return isNumber(event)"  readonly required>
                                </div>
                                <div class="col-md-3">
                                    <label>{{ __('Last Month Date') }}</label>
                                       <input type="date" class="form-control" name="last_month_date" id="last_month_date" >
                                </div>
                                <div class="col-md-3">
                                    <label>{{ __('This Month Date') }}</label>
                                       <input type="date" class="form-control" name="this_month_date" id="this_month_date" >
                                </div>
                                
                                 <div class="col-md-3">
                                     <label>{{ __('Payment Mode') }}</label>
                    				<select class=" form-control payment_mode_id" id="payment_mode_id" name="payment_mode_id">
                                        <option value="">{{ __('common.Select') }}</option>
                                         @if(!empty($getPaymentMode)) 
                                          @foreach($getPaymentMode as $key=> $itme)
                                             <option value="{{ $itme->id ?? ''  }}" >{{ $itme->name ?? ''  }}</option>
                                          @endforeach
                                      @endif
                                    </select>
                                </div>
								
								<div class="col-md-3">
									<lable>{{ __('Attachocument') }}</lable>
									<div class="input file form-control">
										<input type="file" name="electricitybil_img" id="electricitybil_img" value="{{old('electricitybil_img')}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
								          
									</div>
								</div>
								
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <!--<button type="submit" class="btn btn-primary" data-status="1"><i class="fa fa-money"></i> Add</button>-->
                        <button type="submit" class="btn btn-success"><i class="fa fa-money"></i> Collect</button>
                    </div>
                </div>
            </div>
         
                                    
                           
                            </form>
          </div>
     
                 
                </div>
               
          
            
            <script>
      function calculateSum() {
    var sum = 0;

    $(".amount").each(function() {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });

    $("#pay_amt").val(sum.toFixed(2));

}      
 function addElement_room(){
	    var SITEURL  = "{{ url('/') }}";
		var div=document.getElementById('maindiv_room');
		
		var num=Number(document.getElementById('value_room').value)+Number(1);
		document.getElementById('value_room').value=num;
		var num1=Number(document.getElementById('total_room').value)+Number(1);
		document.getElementById('total_room').value=num1;
		var heightchange=Number(42)*(Number(num)-Number(1))+Number(110)+Number(15);
		//alert(heightchange);
		$("#main_room").css('height',heightchange);
		var newdiv = document.createElement('div');
	  	var divIdName = 'append_'+num;
	   	var contents ='';
		newdiv.setAttribute('id',divIdName);
		contents='<tr class="tr_clone"><div class="row pl-1"><div class="form-group pr-2"><select class="form-control" id="select" onchange="categorys(this.value,'+num+')" name="category[]" style="width:130px;" required><option value="">Select</option> @if(!empty($data['FeesMaster'])) @foreach($data['FeesMaster'] as $fees_master) <option value="{{ $fees_master->id }}" {{ ( $fees_master->id == $data['stuData']['section_id'] ? 'selected' : '' ) }}>{{ $fees_master['FeesType']['name']   ?? ''  }}</option> @endforeach @endif <option value="Total Assigned Fees1">Other Fees</option></select></div><div class="form-group pr-2 pl-2" style="display: none;"><input id="fees_name_'+num+'" type="text" class="form-control item-name fees_name" name="fees_name[]" value="" placeholder="Fees Name" style="width: 200px;" required></div><div class="form-group pr-2"><input name="qty[]" id="qty_'+num+'" onblur="calcSum(this.value,'+num+')" placeholder="Quantity" style="width: 200px;"class="form-control quantity qty" maxlength="100" type="text"  value="1" readonly></div><div class="form-group pr-2"><input name="amount[]"  placeholder="Amount" class="form-control amount" onkeyup="calcSum(this.value,'+num+')" maxlength="100" style="width: 200px;" type="text" id="amount_'+num+'" value="{{ old('amount') }}" required></div><div class="form-group pr-2"><input name="discount[]"  style="width: 200px;" placeholder="Discount" class="form-control cal discount" maxlength="100" onkeyup="discount(this.value,'+num+')" type="text" id="discount_'+num+'" value="{{ old('discount') }}"></div><div class="form-group pr-2"><input name="total_amount[]" id="total_amount_'+num+'" placeholder="Total Amount" class="form-control tolamount" style="width: 200px;"maxlength="100" type="text"  value="" tabindex="1" readonly></div><div style="padding: 6px;" id="add"><input type="button" onclick="addElement_room();" value="" title="Add More Fees" class="addmoreprodtxtbx" id="button" name="button" ><input type="button" class="removeprodtxtbx" name=delrow_'+num+' id=delrow_'+num+'  value="" onclick="removeElement_room(\'append_'+num+'\','+num+')"></div></div></tr>';
		
		newdiv.innerHTML = contents;
	  	div.appendChild(newdiv);

	}
	
            </script>
            
   <div class="modal fade" id="electricstatus" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Status conformation</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id">
            <input type="hidden" id="status">
            <input type="hidden" id="hostel_assign_id">
        <p>Are you sure you want to change status ?</p>
        </div>
        <div class="modal-footer">
          <button type="button" onclick="changeStatus()" class="btn btn-primary" data-dismiss="modal">Change</button>
          <button type="button" id="closemodal" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
           
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
   $(document).ready(function() {
    function updateTotal() {
        var meterUnit = parseFloat($('#meter_unit').val());
        var perUnitRate = parseFloat($('#per_unit_rate').val());
        
        if (isNaN(meterUnit) || isNaN(perUnitRate)) {
            $('#pay_amount').val('');
            return;
        }
        var total = meterUnit * perUnitRate;
        $('#pay_amount').val(total);
    }

    $('#meter_unit, #per_unit_rate').on('blur input', updateTotal);
});


$(document).ready(function() {
    // Function to calculate the difference
    function calculateMeterUnit() {
        var lastUnit = parseFloat($('#last_meter_unit').val()) || 0; // Get last month's unit (default to 0 if empty)
        var thisUnit = parseFloat($('#this_meter_unit').val()) || 0; // Get this month's unit (default to 0 if empty)
        
        // Calculate the difference
        var meterUnit = thisUnit - lastUnit;
        
        // Set the result in the 'Meter Unit' field
        $('#meter_unit').val(meterUnit);
    }

    // Trigger calculation when values change
    $('#last_meter_unit, #this_meter_unit').on('input', function() {
        calculateMeterUnit();
    });
});
</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!--    <script>-->
<!--      var $jq = jQuery.noConflict();-->
<!--$jq(document).ready(function(){-->
<!--    $jq$('.btn-primary').click(function() {-->
<!--        var baseurl = "{{ url('/') }}";-->
<!--        var status = $(this).data('status');-->
        <!--alert(state_id);-->
<!--        $jq.ajax({-->
<!--            headers: {'X-CSRF-TOKEN': $jq('meta[name="csrf-token"]').attr('content')},-->
<!--            url: baseurl + '/statesChange/' + status,-->
<!--            success: function(data){-->
<!--                console.log($data);-->
                
<!--            }-->
<!--        });-->
<!--    });-->
<!--});-->

<!--    </script>-->


<!--// <script>-->

<!--// $('#payment_mode_id').on('change', function() {-->
    
<!--//     if(this.value == 9){-->
        
<!--// var total_amount=jQuery('#pay_amt').val();-->
    
<!--//                   // console.log(result);-->
<!--//                   var options = {-->
<!--//                         "key": "{{ Config::get('app.razorpay_key') }}", -->
<!--//                         "amount": total_amount*100, -->
                       
<!--//                         "currency": "INR",-->
<!--//                         "name": "Rukmani Software",-->
<!--//                         "description": "Live Transaction",-->
<!--//                         "image": "https://www.rukmanisoftware.com/public/assets/img/header-logo.png",-->
<!--//                         "handler": function (response){-->
<!--//                          $("#transaction_id").val(response.razorpay_payment_id);  -->
<!--//                         }-->
<!--//                     };-->
                    
<!--//                     var rzp1 = new Razorpay(options);-->
<!--//                     rzp1.open();-->
                    
<!--// }-->
        
<!--// });-->
<!--// </script>-->
<script>
    $(document).ready(function(){
        $('.statusChange').click(function(){
           var status = $(this).data('status');
           var id = $(this).data('id');
           var hostel_assign_id = $(this).data('hostel_assign_id');
           $('#status').val(status);
           $('#id').val(id);
           $('#hostel_assign_id').val(hostel_assign_id);
       });
    });
</script>
<script>
    $('#month_id').on('change', function(e){
                var baseurl = "{{ url('/') }}";
            	var month_id = $(this).val();
            	var admission_id = $('#admission_id').val();
                $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            	  url: baseurl + '/unitData/' + month_id + '/' + admission_id, 
            	  success: function(data){
            			$("#last_meter_unit").val(data);
            	  }
            	});
            	
            });
</script>
<style>
._table {
    width: 100%;
    border-collapse: collapse;
}

._table :is(th, td) {
    padding: 8px 10px;
}
.success {
    background-color: #24b96f !important;
}
.danger {
    background-color: #ff5722 !important;
}
.action_container>* {
    border: none;
    outline: none;
    color: #fff;
    text-decoration: none;
    display: inline-block;
    padding: 8px 14px;
    cursor: pointer;
    transition: 0.3s ease-in-out;
}
textarea {
    height: calc(2.25rem) !important;
}
</style>

<style>
 .addmoreprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/list_add.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 16px;
  margin-top:4px;
  width: 16px;
}

.removeprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/delete2.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 15px;
 
   margin:4px 0 0 0 !important;
  width: 16px;
 
}
</style>
