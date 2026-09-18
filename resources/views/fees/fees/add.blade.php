@php
   $getstudents = Helper::getstudents();
   $getSection = Helper::getSection();
   $classType = Helper::classType();
@endphp
@extends('layout.app') 
@section('content')
 <div class="content-wrapper">
    <!--<div class="panel panel-primary">
        <div class="container-fluid panel-heading">
            <div class="row">
                <div class="col-sm-8">
                <h3 class="m-2">Collect Student Fees</h3>
                </div>
                <div class="col-sm-4">
                <ol class="breadcrumb float-sm-right">
                <li class="pr-2"><a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-home"></i> Home</a></li>
                <li class="pl-2"><a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-xs"><i class="fa fa-arrow-left"></i> Back</a></li>
                </ol>
                </div>
            </div>
        <hr class="bg-primary" style="margin-top:-12px;">
        </div>
    </div>-->

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">    
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                        <h3 class="card-title"><i class="fa fa-money"></i> &nbsp; Collect Student Fees</h3>
                        <div class="card-tools">
                        <a href="{{url('fees/index')}}" class="btn btn-primary  btn-sm" title="View Fees"><i class="fa fa-eye"></i> View</a>
                        <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> Back</a>
                        </div>
                        
                        </div>        
                        <div class"card-body">
                            <form id="quickForm"  method="post" >
                                @csrf
                            <div class="row m-2">
                                <div class="col-md-2">
                            		<div class="form-group">
                            			<label>Class</label>
                            			<select class="form-control" id="class_type_id" name="class_type_id" >
                            			<option value="">Select</option>
                                         @if(!empty($classType)) 
                                              @foreach($classType as $type)
                                                 <option value="{{ $type->id ?? ''  }}" >{{ $type->name ?? ''  }}</option>
                                              @endforeach
                                          @endif
                                        </select>
                            	    </div>
                            	</div>
                            	<div class="col-md-2">
                            		<div class="form-group">
                            			<label>Section</label>
                            				<select class="form-control section_id" id="section_id" name="section_id" >
                            			   <option value="">Select</option>
                                         @if(!empty($getSection)) 
                                              @foreach($getSection as $section)
                                                 <option value="{{ $section->id ?? ''  }}" >{{ $section->name ?? ''  }}</option>
                                              @endforeach
                                          @endif
                                        </select>
                            	    </div>
                            	</div>
                                <div class="col-md-5">
                            		<div class="form-group">
                            			<label>Search By Keywords</label>
                            			<input type="text" class="form-control" id="name" name="name" placeholder="Ex. Admission No., Name, Mobile, Email, Father/ Mother Name etc."> 
                            	    </div>
                            	</div> 
                                <div class="col-md-1 ">
                            	    <div class="form-group">
                            	        <label>Search</label>
                            			<button type="button" class="btn btn-primary" onclick="SearchValue()" >Search</button>
                            	    </div>                    
                            	</div>
                            			
                            </div>
                            </form> 
                        
                            <div class="student_list_show"></div>
                            
                            <div class="student_fees_detail"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 


<script>
 
    function showData(admission_id) {
         var basurl = "{{ url('/') }}";
            $.ajax({
                     headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
                type:'post',
                url: basurl+'/student_fees_onclick',
                data: {admission_id : admission_id},
                // dataType: 'json',
                success: function (data) {
                    if(data == 0){
                        alert('Please Assign the Fees for this Class !');
                        window.location.href = "{{ url('fees_master') }}";
                    }else{
                        
                        $('.student_fees_detail').html(data);
                    }
                   
                
                }
              });
        };
        

       
   
   
   
    function SearchValue() {
         var basurl = "{{ url('/') }}";
        var class_type_id = $('#class_type_id :selected').val();
        var section_id = $('#section_id :selected').val();
        var name = $('#name').val();
        if(section_id > 0 || class_type_id > 0 || name != ''){
           $.ajax({
                 headers: {'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')},
            type:'post',
            url: basurl+'/SearchValueStd',
            data: {class_type_id:class_type_id,section_id:section_id,name:name},
             //dataType: 'json',
            success: function (data) {
               
              
                $('.student_list_show').html(data);
               
            }
          }); 
        }else{
            alert('Please put a value in minimum one column !');
        }
        
    };
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