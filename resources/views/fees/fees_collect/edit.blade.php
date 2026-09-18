@php
   $getFeesType = Helper::getFeesType();
   $getPaymentMode = Helper::getPaymentMode();
@endphp  

@extends('layout.app') 
@section('content')
    
    
<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">    
        <div class="card card-outline card-orange">
                     <div class="card-header bg-primary">
                    <h3 class="card-title"><i class="fa fa-edit"></i> &nbsp; {{ __('fees.Edit Collected Fees') }}</h3>
                    <div class="card-tools">
                    <a href="{{url('fees/index')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-eye"></i> {{ __('View') }} </a>
                     <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i> {{ __('common.Back') }} </a>
                    </div>
                    
                    </div>              
             <div class="card-body">
                 <form id="quickForm" action="{{ url('#') }}/{{$data['id']}}" method="post">
                      @csrf
                  <table class="_table" id="tableId">
                    <thead>
                      <tr>
                        <th class="text-danger">{{ __('fees.Fees Type') }}*</th>
                        <th class="text-danger">{{ __('common.Amount') }}*</th>
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
                            <select name="fees_type_id[]" id="fees_type_id" class="form-control select2 @error('fees_type_id') is-invalid @enderror"  value="{{ old('fees_type_id') }}" required>
                                <option value="">{{ __('common.Select') }}</option>
                             @if(!empty($getFeesType)) 
                                  @foreach($getFeesType as $type)
                                     <option value="{{ $type->id }}" {{ ($type->id == old('fees_type_id')) ? 'selected' : '' }}>{{ $type->name ?? ''  }}</option>
                                  @endforeach
                              @endif
                            </select>         
                        </td>
                        <td>
                          <input type="text" class="form-control amount pay_amt" onblur="calculateSum()" placeholder="{{ __('common.Amount') }}" id="amount" name="amount[]" onkeypress="javascript:return isNumber(event)" required>
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
                                <label class="text-danger"><b>{{ __('fees.Payment Mode') }}*</b></label>
                                <select class="form-control select2" id="payment_mode_id" name="payment_mode_id" required>
                                    @if(!empty($getPaymentMode))
                                        @foreach($getPaymentMode as $value)
                                        <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                        @endforeach
                                    @endif  
                                </select>
                          </td>
                          <td>
                                <label class="text-danger"><b>{{ __('fees.Total Amount') }}*</b></label>
                                <input type="text" class="form-control" placeholder="Total Amount" id="pay_amt" name="pay_amt" value="" readonly>
                          </td>  
                          <td>
                                <label class="text-danger"><b>{{ __('fees.Transaction Id') }}*</b></label>
                                <input type="text" class="form-control" placeholder="{{ __('fees.Transaction Id') }}" id="transaction_id" name="transaction_id" value="" readonly>
                          </td>                           
                      </tr>    
                  </table>
              <div class="row m-2">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary ">{{ __('common.Update') }}</button>
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


function calculateSum() {
    var sum = 0;

    $(".amount").each(function() {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });

    $("#pay_amt").val(sum.toFixed(2));

}
</script> 
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
@endsection                