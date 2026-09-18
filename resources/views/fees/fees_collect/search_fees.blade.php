@php
   $getFeesType = Helper::getFeesType();
   $getPaymentMode = Helper::getPaymentMode();
@endphp             
             



<div class="modal fade" data-backdrop="static" id="quickModal" tabindex="-1" data-keyboard="false"  role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" >
      <div class="modal-content">
        <div class="modal-header">
            
            <h4 class="text-center" style="width:100%;">{{__('common.Name') }}: <span id="first_name"></span></h4>   
                  <button type="button" id="closeModal"class="close" data-dismiss="modal" aria-hidden="true">x</button>
        </div>
        <form action="{{ url('student_pay_submit') }}" method="post">
            @csrf
            <div class="modal-body">
                     <input  type="hidden" id="admission_id" name="admission_id" value="" />
                     <input  type="hidden" id="email" name="email" value="" />
                     <input  type="hidden" id="class_type_id1" name="class_type_id" value="" />
                     <input  type="hidden" id="slip_no" name="slip_no" value="{{$BillCounter ?? ''}}" >

                <div class="card">
                  <table class="_table" id="tableId">
                    <thead>
                      <tr>
                        <th class="text-danger">{{__('fees.Fees Type') }}*</th>
                        <th class="text-danger">{{__('common.Amount') }}*</th>
                        <th>Note</th>
                        <th width="50px">
                          <!--<div class="action_container">-->
                          <!--  <a class="success" onclick="create_tr('table_body')">-->
                          <!--    <i class="fa fa-plus"></i>-->
                          <!--  </a>-->
                          <!--</div>-->
                        </th>
                      </tr>
                    </thead>
                    <tbody id="table_body">
                      <tr id="box2" >
                        <td>
                            <select name="fees_type_id[]" id="fees_type_id" class="form-control @error('fees_type_id') is-invalid @enderror"  value="{{ old('fees_type_id') }}" required>
                                <option value="">{{__('common.Select') }}</option>
                             @if(!empty($getFeesType)) 
                                  @foreach($getFeesType as $type)
                                     <option value="{{ $type->id }}" {{ ($type->id == old('fees_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>         
                        </td>
                        <td>
                          <input type="text" class="form-control amount pay_amt" onblur="calculateSum()" placeholder="{{__('common.Amount') }}" id="amount" name="amount[]" onkeypress="javascript:return isNumber(event)" required>
                        </td>
                        <td>
                          <textarea type="text" class="form-control" placeholder="If have any note write here..."></textarea>
                        </td>
                        <td>
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
                                <label class="text-danger"><b>{{__('fees.Payment Mode') }}*</b></label>
                                <select class="form-control" id="payment_mode_id" name="payment_mode_id" required>
                                    @if(!empty($getPaymentMode))
                                        @foreach($getPaymentMode as $value)
                                        <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                        @endforeach
                                    @endif  
                                </select>
                          </td>
                          <td>
                                <label class="text-danger"><b>{{__('fees.Total Amount') }}*</b></label>
                                <input type="text" class="form-control" placeholder="{{__('fees.Total Amount') }}" id="pay_amt" name="pay_amt" value="" readonly>
                          </td>  
                          <td>
                                <label class="text-danger"><b>{{__('fees.Transaction Id') }}*</b></label>
                                <input type="text" class="form-control" placeholder="{{__('fees.Transaction Id') }}" id="transaction_id" name="transaction_id" value="" readonly>
                          </td>                           
                      </tr>    
                  </table>
                </div>        

            </div>
        
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary"><i class="fa fa-money"></i> {{__('fees.Collddddect') }}</button>
              <button type="submit" class="btn btn-primary" name="print" value="print"><i class="fa fa-money"></i> {{__('fees.Collect & Print') }}</button>
            </div>
        </form>
      </div>
    </div>
</div>

<script>



</script> 
<script>
$(document).ready(function() {
 $("div").click(function() {
   if(this.id=="modal-close")
 {
    alert(this.id); // or alert($(this).attr('id'));
}
     
 });
});
</script>

<script>
    
$(document).ready(function() {
 
    
    count=0;
      $( ".removeprodtxtbx" ).eq( 0 ).css( "display", "none" );
    $(document).on("click", "#clonebtn", function() {
       count++;
        //we select the box clone it and insert it after the box
        $('#box2').addClass('rowTr')
        $('#box2').clone().appendTo('#table_body')
       $('.rowTr').last().addClass('rowTr1')
       //  $('#box2').find('#removerow').addClass("buttondel")
          
   
        // $('.buttondel').css('visibility', 'visible')
      
         $( ".removeprodtxtbx" ).eq( count ).css( "display", "block" );
         $( ".addmoreprodtxtbx" ).eq( count ).css( "display", "none" );
         $( ".pay_amt" ).eq( count ).val("");
          
    });
    
    $(document).on("click", "#removerow", function() {
        $(this).parents("#box2").remove();
        $('#removerow').focus();
        count--;
    });
    
      $(document).on("click", "#closeModal", function() {
$( "tr" ).remove( ".rowTr1" );
 $( ".pay_amt" ).val("");
 $( "#pay_amt" ).val("");
count=0;
    });
    
    
    
    
   
});
</script>
<script>
$('.quickCollect').click(function() {
	var admission_id = $(this).data('id');
	var first_name = $(this).data('first_name');
	var email = $(this).data('email');
	var class_type_id = $(this).data('class_type_id');

	$('#admission_id').val(admission_id);
	$('#first_name').html(first_name);
	$('#email').val(email);
	$('#class_type_id1').val(class_type_id);
});


$("#trColor tbody tr").click(function(){
   $(this).addClass('selected').siblings().removeClass('selected');    
});
</script>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>

/*$('#payment_mode_id').on('change', function() {
    
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
        
});*/
</script>

