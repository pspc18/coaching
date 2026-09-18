@extends('layout.app') 
@section('content')
@php
   $getSection = Helper::getSection();
   $classType = Helper::classType();
   $getPaymentMode = Helper::getPaymentMode();
@endphp
 <div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">    
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp; Collect Student Fees</h3>
                        <div class="card-tools">
                        <a href="{{url('fees/index')}}" class="btn btn-primary  btn-sm" title="View Fees"><i class="fa fa-eye"></i> View</a>
                        <a href="{{url('admission/index')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        
                        </div>        
                        <div class"card-body">
                        
        <div class="m-2">
            <div class="row">
                <div class="col-md-2 text-center">
                    <img src="{{ env('IMAGE_SHOW_PATH').'student_image/'.$data['stuData']['student_img'] }}" width="100" height="100" style="border:2px solid;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/student_image/profile_img.png' }}'">
                </div>
                <div class="col-md-10">
                    <table id="" class="table table-bordered table-striped dataTable dtr-inline small_td">
                            <tr role="row">
                              <td><b>Name</b></td>
                               <td>{{$data['stuData']['first_name'] ?? ''}} {{$data['stuData']['last_name'] ?? ''}}</td>
                               <td><b>Class</b></td>
                              <td>{{$data['stuData']['ClassTypes']['name'] ?? ''}} ({{$data['stuData']['Section']['name'] ?? ''}})</td>
                            </tr>
                            <tr role="row">
                              <td><b>Mobile</b></td>
                               <td>{{$data['stuData']['mobile'] ?? ''}}</td>
                               <td><b>Father Name</b></td>
                              <td>{{$data['stuData']['father_name'] ?? ''}}</td>
                            </tr>
                            <tr role="row">
                              <td><b>Total Assigned Fees</b></td>
                               <td><span class="btn btn-primary btn-xs w-100 disabled"><b>&#x20B9; &nbsp; {{$data['stuFeeAmount'] ?? ''}} /-</b></span></td>
                               <td><b>Total paid Fees</b></td>
                              <td><span class="btn btn-success btn-xs w-100 disabled"><b>&#x20B9; &nbsp; {{$data['stuPaidAmount'] ?? ''}} /-</b></span></td>
                            </tr>  
                    </table>                        
                </div>
            </div>            

        </div>
            
            <form id="quickForm" action="{{ url('student_pay_submit') }}" method="post" >
                @csrf
                <div class="row m-2">
                    <input type="hidden" id="admission_id" name="admission_id" value="{{$data['stuData']['id'] ?? ''}}">
                    <input type="hidden" id="class_type_id" name="class_type_id" value="{{$data['stuData']['class_type_id'] ?? ''}}">
                    <input type="hidden" id="section_id" name="section_id" value="{{$data['stuData']['section_id'] ?? ''}}">
                    <input type="hidden" id="email" name="email" value="{{$data['stuData']['email'] ?? ''}}">
    
    
            <table class="table table-bordered mb-2 mt-3">
                <thead>
                    <tr>
                      <th>Fees Type</th>
                      <th>Select Category</th>
                      <th>Quantity</th>
                      <th>Amount</th>
                      <th>Discount</th>
                      <th>Total Amount</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id ="tbodynew">
                    <tr class="tr_clone">
                        <td>
                            <div class="form-group">
                                <input id="fees_type_0" type="text" class="form-control fees_type" name="fees_type[]"  value="{{old('fees_type')}}" placeholder="Fees Type" required>
                    	   </div>
                        </td>
                        <td>
                            <div class="form-group">
                				<select class="form-control" id="category" name="category[]" required>
                    			    <option value="">Select</option>
                    			    <option value="0">Main Fees</option>
                    			    <option value="Total Assigned Fees1">Other Fees</option>
                                </select>                                
                    	   </div>
                        </td>                        
                        <td>
                            <div class="form-group">
                                <input name="qty[]" id="qty_0" placeholder="Quantity" class="form-control cal qty" onblur="calcSum(this.value,0)" maxlength="100" type="text"  value="{{ old('quantity') ?? '1' }}">
                    	   </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input name="amount[]"  id="amount_0" placeholder="Amount" class="form-control amount" onkeyup="calcSum(this.value,0)" maxlength="100" type="text"  value="{{ old('amount') }}" required>
                    	   </div>
                        </td>
                        <td>
                            <div class="form-group">
                                <input name="discount[]"  id="discount_0" placeholder="Discount" class="form-control cal discount" onkeyup="discount(this.value,0)" maxlength="100" type="text"  value="{{ old('discount') }}">
                    	   </div>
                        </td>                    
                        <td>
                            <div class="form-group">
                                <input name="total_amount[]" id="total_amount_0" placeholder="Total Amount" class="form-control tolamount" maxlength="100" type="text"  value="" tabindex="1" readonly>
                    	   </div>
                        </td>
                       <td style="width: 51px; cursor: pointer;"> 
                       <div class="col-sm-3" id="add">
                            <input  type="button" onclick="addElement_room();" value="" title="Add More Product" class="addmoreprodtxtbx" style="color:#6445d2;" id="button"/>
                        </div>
                      </td>
                    </tr>
                 </tbody>
            </table>
            <div class="row">
            <div class="col-md-12">
                <div id="maindiv_room">
    		        <div id="append_1">	
    			        <div id="capacity_1"></div>
    		        </div>	
    	        </div>
    	    </div>
	        </div>
	<input type="hidden" name="total_room" id="total_room" value="1">
	<input type="hidden" name="value_room" id="value_room" value="1">
            <div class="row m-1">
								<div class="col-md-2">
                                    <div class="form-group">
										 <label for="netamount_amt">Net Amount</label>
										<input type="text" class="form-control" id="net_amount" readonly tabindex="1" placeholder="Net Amount" name="net_amount"required>
									</div>
								</div>
								<div class="col-md-2">
                                    <div class="form-group">
										 <label for="qty">Total Quantity</label>
										<input type="text" class="form-control" id="Quantity" tabindex="1" readonly placeholder="Total Quantity" name="total_qty" required  value="{{$totalQty ?? ''}}">
									</div>
								</div>
								<div class="col-md-2">
                                    <div class="form-group">
                                        <label for="distotal">Discount on Total</label>
										<input type="text" class="form-control discountTotal"  tabindex="1" id="discountTotal" placeholder="Discount Total" name="discountTotal" maxlength="100" readonly>
										
									</div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="pay">Pay Final</label>
										<input type="text" class="form-control" id="pay_amt" tabindex="1" placeholder="Pay Final" name="pay_amt" required readonly>
                                    </div>
                                </div>
								
							</div>    
   
                    <div class="col-xs-3 col-sm-3 col-md-3 col-lg-2">
                    	<label style="color: red;">Payment Mode*</label>
                            <div class="input select">
                                <select name="payment_mode_id" id="payment_mode_id" class="form-control @error('payment_mode_id') is-invalid @enderror"  value="{{ old('payment_mode_id') }}" required>
                                    <option value="">Select</option>
                                 @if(!empty($getPaymentMode)) 
                                      @foreach($getPaymentMode as $type)
                                         <option value="{{ $type->id }}" {{ ( $type->id == $data['stuPaidAmount']['payment_mode_id'] ? 'selected' : '' ) }}>{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                  @endif
                                </select>
                                @error('payment_mode_id')
                        			<span class="invalid-feedback" role="alert">
                        					<strong>{{ $message }}</strong>
                        			</span>
                        		@enderror
                            </div>  
                    </div>                 
    
                    
                </div>
            	<div class="row m-2">
                    <div class="col-md-12 text-center mt-4">
                    <button  type="submit" class="btn btn-primary">Pay Now </button>
                    <!--<a  class="btn btn-success btn-sm" onclick="pay_now()"/>pay Now </a>-->
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





<script>
    
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
		contents='<tr class="tr_clone"><div class="row pl-1"><div class="form-group pr-2 pl-2"><input id="fees_type_'+num+'" type="text" class="form-control item-name fees_type" name="fees_type[]" value="" placeholder="Fees Type" style="width: 200px;" required></div><div class="form-group pr-2"><select class="form-control" id="select" name="category[]" style="width:130px;" required><option value="">Select</option><option value="0">Main Fees</option><option value="1">Other Fees</option></select></div><div class="form-group pr-2"><input name="qty[]" id="qty_'+num+'" onblur="calcSum(this.value,'+num+')" placeholder="Quantity" style="width: 200px;"class="form-control quantity qty" maxlength="100" type="text"  value="1" readonly></div><div class="form-group pr-2"><input name="amount[]"  placeholder="Amount" class="form-control amount" onkeyup="calcSum(this.value,'+num+')" maxlength="100" style="width: 200px;" type="text" id="amount_'+num+'" value="{{ old('amount') }}" required></div><div class="form-group pr-2"><input name="discount[]"  style="width: 200px;" placeholder="Discount" class="form-control cal discount" maxlength="100" onkeyup="discount(this.value,'+num+')" type="text" id="discount_'+num+'" value="{{ old('discount') }}"></div><div class="form-group pr-2"><input name="total_amount[]" id="total_amount_'+num+'" placeholder="Total Amount" class="form-control tolamount" style="width: 200px;"maxlength="100" type="text"  value="" tabindex="1" readonly></div><div style="padding: 6px;" id="add"><input type="button" onclick="addElement_room();" value="" title="Add More Fees" class="addmoreprodtxtbx" id="button" name="button" ><input type="button" class="removeprodtxtbx" name=delrow_'+num+' id=delrow_'+num+'  value="" onclick="removeElement_room(\'append_'+num+'\','+num+')"></div></div></tr>';
		
		newdiv.innerHTML = contents;
	  	div.appendChild(newdiv);

	}
	
    function removeElement_room(divNum, countNum){
		
		var d = document.getElementById('maindiv_room');
		d.removeChild(window.document.getElementById(divNum+""));
		var counterValue= Number(document.getElementById('value_room').value)-Number(1);
		document.getElementById('value_room').value=counterValue;
		var heightchange=Number(42)*(Number(counterValue)-Number(1))+Number(110)+Number(15);
		
		$("#main_room").css('height',heightchange);
  	
	}



function calcSum(value,row_id) {
   var qty = $('#qty_'+row_id).val();
      
        var amount = $('#amount_'+row_id).val();
       
         var total_amount = qty  * amount;
       
       
      
        $('#total_amount_'+row_id).val(total_amount);
        
        calculateSum();
    
};
function discount(value,row_id) {
   var qty = $('#qty_'+row_id).val();
       var qty = $('#qty_'+row_id).val();
        var amount = $('#amount_'+row_id).val();
       
         var total_amount =  amount-value;
       
        $('#total_amount_'+row_id).val(total_amount);
     /*  if( value <=  amount){
     
       }else{
           var total_amount = qty  * amount;
        $('#total_amount_'+row_id).val(total_amount);
           $('#discount_'+row_id).val('');
           alert('Discount Cannot be Higher than Amount');
       }*/
        calculateSum();
    
};



function calculateSum() {
    var sum = 0;
    var qty = 0;
    var discount = 0;
    var amount_sum =0;
    
    $(".tolamount").each(function() {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });
    $(".amount").each(function() {
        if (!isNaN(this.value) && this.value.length != 0) {
            amount_sum += parseFloat(this.value);
        }
    });    
    $(".qty").each(function() {
            if (!isNaN(this.value) && this.value.length != 0) {
                qty += parseFloat(this.value);
              
            }
        });
        
    $(".discount").each(function() {
            if (!isNaN(this.value) && this.value.length != 0) {
                discount += parseFloat(this.value);
              
            }
        });

    $("#net_amount").val(amount_sum.toFixed(2));
    $("#pay_amt").val(sum.toFixed(2));
    $("#Quantity").val(qty);
     $("#discountTotal").val(discount);
   
}
</script> 

               	   
<style>
.addmoreprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/list_add.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 16px;
  width: 16px;
  
}
.removeprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('https://saleanalysics.rukmanisoftware.com/public/images/delete2.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 15px;
  margin-left: 5px;
  width: 16px;
}
</style>


@endsection            