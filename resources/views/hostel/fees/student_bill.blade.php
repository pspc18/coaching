 @php
   $getFeesType = Helper::getFeesType();
   $getPaymentMode = Helper::getPaymentMode();
@endphp             
        
 
 <div class="row">
     <div class="col-12 col-md-12 text-center"> <h3>Fees Pay</h3></div>
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <form action="{{ url('hostel/fees/pay') }}" method="post">
                                 @csrf
                                    <input  type="hidden" id="hostel_assign_id" name="hostel_assign_id" value="{{$data['stuData']['id']}}" />
                                    <input  type="hidden" id="email" name="email" value="{{$data['stuData']['email']}}" />
                                    <input  type="hidden" id="slip_no" name="slip_no" value="{{$data['BillCounter']['counter'] ?? ''}}" >
                                <div class"card-body">
                                    <table class="_table" id="tableId">
                                    <thead>
                                      <tr>
                                        <th class="text-danger">Fees Type*</th>
                                        <th class="text-danger">Amount*</th>
                                        <th>Note</th>
                                        <th width="50px"></th>
                                      </tr>
                                    </thead>
                                    <tbody id="table_body">
                                      <tr id="box2" >
                                        <td>
                                            <select name="fees_type_id[]" id="fees_type_id" class="form-control @error('fees_type_id') is-invalid @enderror"  value="{{ old('fees_type_id') }}" required>
                                                <option value="">Select</option>
                                             @if(!empty($getFeesType)) 
                                                  @foreach($getFeesType as $type)
                                                     <option value="{{ $type->id }}" {{ ($type->id == old('fees_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                                  @endforeach
                                              @endif
                                            </select>         
                                        </td>
                                        <td>
                                          <input type="text" class="form-control amount pay_amt" onblur="calculateSum()" placeholder="Amount" id="amount" name="amount[]" onkeypress="javascript:return isNumber(event)" required>
                                        </td>
                                        <td>
                                          <textarea type="text" class="form-control" placeholder="If have any note write here..."></textarea>
                                        </td>
                                        <td style="width: 92px;">
                                          <div class="action_container">
                                            <!--<a class="danger" onclick="remove_tr(this)">-->
                                            <!--  <i class="fa fa-close"></i>-->
                                                <input type="button" class="addmoreprodtxtbx" id="clonebtn" />
                                                <input type="button" id="removerow" class="removeprodtxtbx" />
                                            </a>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                     <tr>
                                      <td>
                                            <label class="text-danger"><b>Payment Mode*</b></label>
                                            <select class="form-control" id="payment_mode_id" name="payment_mode_id" required>
                                                @if(!empty($getPaymentMode))
                                                    @foreach($getPaymentMode as $value)
                                                    <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                                    @endforeach
                                                @endif  
                                            </select>
                                      </td>
                                      <td>
                                            <label class="text-danger"><b>Total Amount*</b></label>
                                            <input type="text" class="form-control" placeholder="Total Amount" id="pay_amt" name="pay_amt" value="" readonly>
                                      </td>  
                                      <td>
                                            <label class="text-danger"><b>Transaction Id*</b></label>
                                            <input type="text" class="form-control" placeholder="Transaction Id" id="transaction_id" name="transaction_id" value="" readonly>
                                      </td>                           
                                  </tr>    
                                </table>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-primary"><i class="fa fa-money"></i> Collect</button>
                                  <button type="submit" class="btn btn-primary" name="print" formtarget="blank" value="print"><i class="fa fa-money"></i> Collect & Print</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
               
                <div class=" card">
                    <div class="row ">
                            <div class="col-md-2 text-center">
                    <img src="{{ env('IMAGE_SHOW_PATH').'student_image/'.$data['stuData']['student_img'] }}" width="100" height="100" style="border:2px solid;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
                </div>
                            <div class="col-md-8">
                    <table id="" class="table table-bordered table-striped dataTable dtr-inline small_td">
                            <tr role="row">
                              <td><b>Name</b></td>
                               <td>{{$data['stuData']['first_name'] ?? ''}}{{$data['stuData']['last_name'] ?? ''}}</td>
                              
                            </tr>
                            <tr role="row">
                              <td><b>Mobile</b></td>
                               <td>{{$data['stuData']['mobile'] ?? ''}}</td>
                               <td><b>Father Name</b></td>
                              <td>{{$data['stuData']['f_name'] ?? ''}}</td>
                            </tr>
                            <tr role="row">
                              <td><b>Total Assigned Fees</b></td>
                               <td><span class="btn btn-primary btn-xs w-100 disabled"><b>&#x20B9; &nbsp; {{$data['stuData']['hostel_fees'] ?? ''}} /-</b></span></td>
                               <td><b>Total paid Fees</b></td>
                              <td><span class="btn btn-success btn-xs w-100 disabled"><b>&#x20B9; &nbsp; {{$data['stuPaidAmount'] ?? ''}} /-</b></span></td>
                            </tr>  
                    </table>                        
                </div>
                    </div>
                    <div class="row ">
                            <div class="col-md-12 text-center"> <h4>Fees Details</h4></div>
                </div>
                    <table class="table table-bordered small_td" id="trColor">
                                    <thead>
                                        <tr>
                                            <th>Sr.No.</th>
                                            
                                            <th>Name</th>
                                            <th>Fees Type </th>
                                            <th>Amount</th>
                                            <th>Payment Mode</th>
                                            <th>Print</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @php
                                        $i=1
                                        @endphp
                                        @foreach ($data['stuFeeDet'] as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item['name']  }} </td>
                                            <td>{{ $item['FeesType']['name']  }}</td>
                                            <td>{{ $item['amount']  }}</td>
                                            <td>{{ $item['PaymentMode']['name']  }}</td>
                                            <td>
                                                <a href="{{url('hostel_fees_print',$item->id)}}" target="blank" class="btn btn-success  btn-xs" title="Fees Print"><i class="fa fa-print"></i></a>
                                            </td>

                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
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
            
           
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>

$('#payment_mode_id').on('change', function() {
    
    if(this.value == 9){
        
var total_amount=jQuery('#pay_amt').val();
    
                  // console.log(result);
                   var options = {
                        "key": "{{ Config::get('app.razorpay_key') }}", 
                        "amount": total_amount*100, 
                       
                        "currency": "INR",
                        "name": "Rukmani Software",
                        "description": "Live Transaction",
                        "image": "https://www.rukmanisoftware.com/public/assets/img/header-logo.png",
                        "handler": function (response){
                         $("#transaction_id").val(response.razorpay_payment_id);  
                        }
                    };
                    
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                    
}
        
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
