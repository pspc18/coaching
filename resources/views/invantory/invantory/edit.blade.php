
@extends('layout.app') 
@section('content')

<div class="content-wrapper">

   <section class="content pt-3">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12 col-md-12">    
    <div class="card card-outline card-orange">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-address-book-o"></i> &nbsp;{{ __(' Inventory Edit') }} </h3>
            <div class="card-tools">
            <a href="{{url('invantory_view')}}" class="btn btn-primary  btn-sm"><i class="fa fa-eye"></i>{{ __('common.View') }} </a>
            <a href="{{url('invantory_dashboard')}}" class="btn btn-primary  btn-sm"><i class="fa fa-arrow-left"></i> {{ __('messages.Back') }}</a>
            </div>
            
            </div>        

            
            <form id="form-submit-edit" action="{{ url('invantory_edit') }}/{{$data->id ?? '' }}" method="post" enctype="multipart/form-data">
            @csrf
                <div class="row m-2">
                        <div class="col-md-2">
                        <div class="form-group ">
                            <label> Date</label>
                             <input type="date" class="form-control @error('date') is-invalid @enderror" tabindex="1" id="date" name="date" value="{{ date('Y-m-d') }}" value="{{$data->date ?? ''}}">
            				
                        </div> 
                    </div>
                     <div class="col-md-2">
                        <div class="form-group ">
                        <label for="inputEmail3" style="color:red;"> Invoice/Bill No.*</label>
                         <input type="text" class="form-control @error('invoice_no') is-invalid @enderror" autofocus="autofocus" id="invoice_no"  name="invoice_no"  value="{{$data->invoice_no ?? ''}}" placeholder="Invoice No." >
        				
                        </div> 
                    </div>
                
                    <div class="col-md-3">
                        <div class="form-group">
                            <label style="color:red;">To (Party Name)*</label>
                            <span class="input-group-append">
                                <input id="party_name1" type="text" class="form-control"  name="party_name"  value="{{$data['party_name'] ?? ''}}" placeholder="Party Name" style="height: 35px;" >
                               
                            </span>		
                        </div>
                    </div>
                    <div class="col-md-3">
                            <div class="form-group ">
                                <label style="color:red;"> Mobile*</label>
                                 <input type="text"  class="form-control @error('mobile') is-invalid @enderror" id="mobile"  name="mobile" value="{{$data['mobile'] ?? ''}}" placeholder="Mobile"  onkeypress="javascript:return isNumber(event)">
                              
                            </div>
                    </div>
                    <div class="col-md-2">
                            <div class="form-group ">
                                <label> GSTIN</label>
                                 <input type="text"  class="form-control @error('gstin') is-invalid @enderror" id="gstin" tabindex="1" name="gstin" value="{{$data['gstin'] ?? '' }}" placeholder="GSTIN"  >
                               
                            </div>
                    </div>
                </div>
                  <table class="table table-bordered mb-2 mt-3">
                <thead>
                    <tr>
                      <th>
                       Item Name &nbsp;</th>
                      
                      <th style="width: 8pc;">Quantity</th>
                      <th style="width: 8pc;">Amount</th>
                      <th style="width: 8pc;">Tax(%)</th>
                      <th>Total Amount</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody id ="tbodynew">
                      @php
                      $count = $dataDetail->count();
                      @endphp
                       @if(!empty($dataDetail))
                            @php
                                $i=1;
                            @endphp
                            @foreach ($dataDetail as $key=>$item)
                      <tr class="tr_clone">
                    <td style="width: 22pc;">
                            <input id="item_name_{{$item['id']}}" type="text" class="form-control item_name" name="item_name" onkeyup="checkScore(this.value,{{$item['id'] ?? ''}})"  value="{{$item['inventory_item_name'] ?? ''}}" placeholder="item Name" >
                	   </div>
                	   <input  type="hidden" id="item_id1_{{$item['id']}}" name="inventory_item_id[]" class="form-control" value="{{$item['inventory_item_id'] ?? ''}}">
                	   <input  type="hidden" id="item_id1_{{$item['id']}}" name="inventory_detail_id[]" class="form-control" value="{{$item['id'] ?? ''}}">
                    </td>
                    <td>
                        <div class="form-group" style="width: 8pc;">
                            <input name="qty[]" id="qty_{{$item['id']}}" placeholder="Quantity" class="form-control qty"  onblur="calcSum(this.value,{{$item['id']}})" maxlength="100" type="text"  value="{{$item['qty'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                	   </div>
                    </td>
                   <td>
                        <div class="form-group" style="width: 8pc;">
                            <input name="amount[]" id="amount_{{$item['id']}}" placeholder="Amount" class="form-control" onkeyup="calcSum(this.value,{{$item['id']}})" maxlength="100" type="text"  value="{{$item['amount'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                	   </div>
                    </td>
                    <td>
                        <div class="form-group" style="width: 8pc;">
                            <input name="tax[]" id="tax_{{$item['id']}}" placeholder="Tax" class="form-control tax"  onblur="calcSum(this.value,{{$item['id']}})" maxlength="100" type="text"  value="{{$item['tax'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                	   </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input name="total_amount[]"  id="total_amount_{{$item['id']}}" placeholder="Total Amount" class="form-control tolamount"  maxlength="100" type="text"  value="{{$item['total_amount'] ?? ''}}" onkeypress="javascript:return isNumber(event)">
                	   </div>
                    </td>
                    
                   <td style="width: 51px; cursor: pointer;"> 
                <div class="col-sm-3" id="add">
                <input  type="button" onclick="addElement_room();" value="" title="Add More Product" class="addmoreprodtxtbx" style="color:#6445d2;" id="button"/>
               
								<a href="javascript:;" data-id='{{$item['id']}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData" ><i class="fa fa-trash-o text-danger"></i></a> 
							  
      </div>
                  </td>
                    </tr>
                        @endforeach
                    @endif
                    
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
					 <label for="qty">Total Quantity</label>
					<input type="text" class="form-control" id="Quantity" tabindex="1" readonly placeholder="Total Quantity" name="total_qty"   value="{{$data->total_qty ?? ''}}">
				</div>
			</div>
			<div class="col-md-2">
                <div class="form-group">
					 <label for="netamount_amt">Net Amount</label>
					<input type="text" class="form-control" id="net_amount" readonly tabindex="1" placeholder="Net Amount" name="net_amount" value="{{$data->total_amount ?? ''}}">
				</div>
			</div>
               <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-primary btn-submit">{{ __('common.submit') }}</button><br><br>
               </div>
    </form>
        </div>
</div>
</div>
</div>
</section>
    
</div>


<div class="modal" id="Modal_id">
	<div class="modal-dialog">
		<div class="modal-content" style="background: #555b5beb;">
			<div class="modal-header">
				<h4 class="modal-title text-white">{{ __('common.Delete Confirmation') }}</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
			</div>
			<form action="{{ url('delete_inventory_detail') }}" method="post"> 
			    @csrf
				<div class="modal-body">
					<input type=hidden id="delete_id" name=delete_id>
					<input type=hidden  name="id" value="{{$data->id ?? '' }}">
					<h5 class="text-white">{{ __('common.Are you sure you want to delete') }} ?</h5> </div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('common.Close') }}</button>
					<button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('common.Delete') }}</button>
				</div>
			</form>
		</div>
	</div>
		</div>

    <script>
      
      
       function addElement_room(){
	var SITEURL  = "{{ url('/') }}";
		var div=document.getElementById('maindiv_room');
		
		var num=Number(document.getElementById('value_room').value)+Number(100);
		document.getElementById('value_room').value=num;
		var num1=Number(document.getElementById('total_room').value)+Number(100);
		document.getElementById('total_room').value=num1;
		var heightchange=Number(42)*(Number(num)-Number(1))+Number(110)+Number(15);
		//alert(heightchange);
		$("#main_room").css('height',heightchange);
		var newdiv = document.createElement('div');
	  	var divIdName = 'append_'+num;
	   	var contents ='';
		newdiv.setAttribute('id',divIdName); 
		contents='<tr class="tr_clone"><div class="row form-group ml-1"><input  type="hidden" id="item_id1_'+num+'" name="inventory_item_id[]" class="form-control"><div class="form-group"><input id="item_name_'+num+'" required type="text" class="form-control item-name item_name" name="item_name" value="" placeholder="item Name" style="width: 22pc;"></div><div class="form-group"><input name="qty[]" id="qty_'+num+'" onblur="calcSum(this.value,'+num+')" placeholder="Quantity" style="width: 8pc;"class="form-control quantity qty" maxlength="100" type="text"  value="{{ old('quantity') }}" onkeypress="javascript:return isNumber(event)"></div><div class="form-group"><input name="amount[]" onkeyup="calcSum(this.value,'+num+')"  id="amount_'+num+'" placeholder="Amount" class="form-control " style="width: 8pc;" maxlength="100" type="text"  value=""  onkeypress="javascript:return isNumber(event)"></div><div class="form-group" style="width: 8pc;"><input name="tax[]" id="tax_'+num+'" placeholder="Tax" class="form-control tax"  onblur="calcSum(this.value,'+num+')" maxlength="100" type="text"  value="{{ old('tax') }}" onkeypress="javascript:return isNumber(event)"></div><div class="form-group"><input name="total_amount[]"  placeholder="Total Amount" class="form-control tolamount" maxlength="100" style="width: 27pc;" type="text" id="total_amount_'+num+'" value="" onkeypress="javascript:return isNumber(event)"></div><div style="padding: 6px;" id="add"><input type="button" onclick="addElement_room();" value="" title="Add More Product" class="addmoreprodtxtbx" id="button" name="button" ><input type="button" class="removeprodtxtbx" name=delrow_'+num+' id=delrow_'+num+'  value="" onclick="removeElement_room(\'append_'+num+'\','+num+')"></div></div></tr>';
		
		newdiv.innerHTML = contents;
	  	div.appendChild(newdiv);
	  $(document).ready(function(){
		$("#item_name_"+num).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                    url: SITEURL+"/getAutoCompleteInvantoryItem",
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#item_name_'+num).val(ui.item.label); // display the selected text
                $('#item_id1_'+num).val(ui.item.item_id); // save selected id to input
                $('#amount_'+num).val(ui.item.mrp); // save selected amount to input
                return false;
            },
            focus: function(event, ui){
               $('#item_name_'+num).val(ui.item.label); // display the selected text
               $('#item_id1_'+num).val(ui.item.item_id); // save selected id to input
               $('#amount_'+num).val(ui.item.mrp); // save selected amount to input
               return false;
            },
        });

  
  
        
	});
	}
	
	
    	function removeElement_room(divNum, countNum){
		
		var d = document.getElementById('maindiv_room');
		d.removeChild(window.document.getElementById(divNum+""));
		var counterValue= Number(document.getElementById('value_room').value)-Number(1);
		document.getElementById('value_room').value=counterValue;
		var heightchange=Number(42)*(Number(counterValue)-Number(1))+Number(110)+Number(15);
		
		$("#main_room").css('height',heightchange);
  	
	}
	
	     function checkScore(val,row_id) {

       var SITEURL  = "{{ url('/') }}";
	      $("#item_name_"+row_id).autocomplete({
            source: function( request, response ) {
                $.ajax({
                    headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                    url: SITEURL+"/getAutoCompleteInvantoryItem",
                    type: 'post',
                    dataType: "json",
                    data: {
                        search: request.term
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            select: function (event, ui) {
                $('#item_name_'+row_id).val(ui.item.label); // display the selected text
                $('#item_id1_'+row_id).val(ui.item.item_id); // save selected id to input
                $('#amount_'+row_id).val(ui.item.mrp); // save selected amount to input
                return false;
            },
            focus: function(event, ui){
               $('#item_name_0').val(ui.item.label); // display the selected text
               $('#item_id1_0').val(ui.item.item_id); // save selected id to input
               $('#amount_0').val(ui.item.mrp); // save selected amount to input
               return false;
            },
        });
	     }
   
   
   function calcSum(value,row_id) {
        var qty = $('#qty_'+row_id).val();
        var amount = $('#amount_'+row_id).val();
        var tax = $('#tax_'+row_id).val();
        var taxAmt = $('#tax_amount_'+row_id).val();

         var total_amount = qty * amount;
         var taxAmount = total_amount/100*tax;
        $('#tax_amount_'+row_id).val(taxAmount);
        $('#total_amount_'+row_id).val(total_amount+taxAmount);
        
        calculateSum();
    
};
function calculateSum() {
    var sum = 0;
    var qty = 0;
    $(".tolamount").each(function() {
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });
    $(".qty").each(function() {
            if (!isNaN(this.value) && this.value.length != 0) {
                qty += parseFloat(this.value);
              
            }
        });

    $("#net_amount").val(sum.toFixed(2));
    $("#pay_amt").val(sum.toFixed(2));
    $("#Quantity").val(qty);
   
}
  </script> 


<script>
    $('.deleteData').click(function() {
	var delete_id = $(this).data('id');
	$('#delete_id').val(delete_id);
});
</script>
    <style>
    
    .form-control {
    height: 35px !important;
    }
    @media only screen and (min-width: 1220px) and (max-width:1300px)
    {
    .item-name{width: 119px !important;}
    .brand-name{width: 182px !important;}
    .quantity{width:196px !important;}
    .weightkg{width:193px !important;}
    .rate11{width:191px !important;}
    .amount123{width:196px !important;}
        
    }
    input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus, 
input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px white inset !important;
}
        .form-group {
  margin-bottom: 2px;
}
.left_b_none{
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    height: 40px;
    padding: 4px;
    line-height: 15px;
    font-size: 26px;
}
label {
  display: inline-block;
  margin-bottom: 0px;
  font-size: 14px;
}
.form-control {
  display: block;
  width: 100%;
  height: 28px;
  padding: 3px;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #495057;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  border-radius: .25rem;
  box-shadow: inset 0 0 0 transparent;
  transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.form-group {
  margin-bottom: 0px;
}
.table-bordered thead td, .table-bordered thead th {
  border-bottom-width: 2px;
  padding: 2px 0px 2px 10px;
}
.table td {
  border-bottom-width: 2px;
  padding: 2px 0px 2px 2px;
}
.border-radius{
    height:28px !important;
}
.addmoreprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('public/images/list_add.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 16px;
  width: 16px;
  
}

.removeprodtxtbx {
  background-color: #FFFFFF;
  background-image: url({{url('public/images/delete2.png')}});
  background-repeat: no-repeat;
  border: medium none;
  cursor: pointer;
  height: 15px;
  margin-left: 5px;
  width: 16px;
}
.select2-container .select2-selection--single{
     height:27px !important;
}  
/*.ui-widget-content {
  border: 1px solid #aaaaaa ;
  background: #007bff !important;
  color: #fff !important;
}*/

    </style>
      <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
@endsection      




