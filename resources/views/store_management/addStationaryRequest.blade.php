@php
  $classType = Helper::classType();
  $getSetting = Helper::getSetting();

  $storeItems = DB::table('store_items')->where('qty','>',0)->whereNull('deleted_at')->get();
  @endphp
@extends('layout.app') 
@section('content')

<style>
       .border_none {
            border: none;
        }
        .bg_color_heading {
            background-color: #f1f1f1;
        }
        @media print {
            button {
                display: none;
            }
            body {
                margin: 0;
                padding: 20px;
                font-family: Arial, sans-serif;
            }
            .page {
                width: 100%;
                border: 1px solid #000;
                padding: 20px;
                box-sizing: border-box;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            .bg_color_heading td {
                background-color: #f1f1f1;
                font-weight: bold;
            }
            .border_none {
                border: none;
            }
            .no-print {
                display: none;
            }
        }
     
   </style>

<div class="content-wrapper">

   <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">      
    <div class="card card-outline card-orange">
        <div class="card-header bg-primary">
        <h3 class="card-title"><i class="fa fa-th-large"></i> &nbsp;{{ __('Add Stationary Request') }} </h3>
        <div class="card-tools">
        <!--<a href="{{url('Fees/add')}}" class="btn btn-primary  btn-sm" title="Add Fees"><i class="fa fa-plus"></i>{{ __('common.Add') }}</a>-->
        <a href="{{url('viewStoreRequest')}}" class="btn btn-primary  btn-sm" title="View"><i class="fa fa-eye"></i> {{ __('View Stationary Bills') }} </a>
        </div>
        
        </div>  
        
                   <form id="quickForm" action="{{ url('addStationaryRequest') }}" method="post" >
                @csrf 
                <input type='hidden' value='search' name='submit' />
                    <div class="row m-2">

                        
            		<div class="col-md-4">
            			<div class="form-group"> 
            				<label>{{ __('Search By Admission Number') }}</label>
            				<input type="text" class="form-control" id="admissionNo" name="admissionNo" placeholder="Type Admission No" value="{{$search['admissionNo'] ?? ''}}">
            		    </div>
            		</div>                     	
                        <div class="col-md-1 ">
                             <label for="" style="color: white;">Search</label>
                    	    <button type="submit" class="btn btn-primary" >{{ __('messages.Search') }}</button>
                    	</div>
                    			
                    </div>
                </form>        
                
                
               @if($search['admissionNo'] != '')
                  @php 
          $admissionNo = DB::table('admissions')->where('admissionNo',$search['admissionNo'])->whereNull('deleted_at')->first();
           $class = DB::table('class_types')->where('id', $admissionNo->class_type_id ?? '')->whereNull('deleted_at')->first();
            @endphp
            
              @if(!empty($admissionNo))
              
              <input type='hidden' value='{{$admissionNo->id ?? ""}}' name='admission_id' id='admission_id' />
              <input type='hidden' value='{{$admissionNo->class_type_id ?? ""}}' name='class_type_id' id='class_type_id' />
             <div class='page'>
<table style="border-bottom: 0px solid;width:100%;padding:10px" >
			<tbody >
					<tr> 
      <td class="border_none" rowspan='2' width='25%'>
          <img src="{{ env('IMAGE_SHOW_PATH').'setting/left_logo/'.$getSetting->left_logo }}" style="width: 150px;height: 150px;">
          </td>
      <td class="border_none"   width='50%' style="font-size:20px;text-align:center;"><!--<span><strong>{{$getSetting['name'] ?? ''}}</strong></span>--></td>
      <td class="border_none" width='25%'> 
      </td>
    
   </tr>
	<tr style="text-align:center;">
	    
      <td class="border_none" width='50%'  style="text-align:center;">
       <span style="font-size:20px;text-align:center;"><strong>{{$getSetting['name'] ?? ''}}</strong></span>
      <p style="margin-bottom: 10px;"><b >Address </b> {{$getSetting['address'] ?? ''}}</p>
      <!--<p ><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}}  &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}</p>-->
    </td>
      <td class="border_none" width='25%'></td>
      </tr>

  </tbody>
  </table> 

   <table width="100%" style="border-collapse: collapse; border: 1px solid #000;">
        <tbody>
            
         
             <tr>
                <th style="border: 1px solid #000; padding: 8px;">Admission No.</th>
                <td style="border: 1px solid #000; padding: 8px;">{{ $admissionNo->admissionNo ?? 'N/A' }}</td>
                <td class="border_none" style="border: none;"></td>
                <td class="border_none" style="border: none;"></td>
            </tr>
            <tr>
                <th style="border: 1px solid #000; padding: 8px;">Name</th>
                <td style="border: 1px solid #000; padding: 8px;">{{ $admissionNo->first_name ?? 'N/A' }}</td>
                <th style="border: 1px solid #000; padding: 8px;">Father's Name</th>
                <td style="border: 1px solid #000; padding: 8px;">{{ $admissionNo->father_name ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th style="border: 1px solid #000; padding: 8px;">Class</th>
                <td style="border: 1px solid #000; padding: 8px;">{{ $class->name ?? '' }}</td>
                <th style="border: 1px solid #000; padding: 8px;">Mobile</th>
                <td style="border: 1px solid #000; padding: 8px;">{{ $admissionNo->mobile ?? 'N/A' }}</td>
            </tr>
           
        </tbody>
    </table>
    <div style='text-align:center;padding:5px'>
 <button onclick="addItem()" class='btn-btn-xs btn-primary no-print'>Add Item</button>
</div>
    <table width="100%" style="border-collapse: collapse; border: 1px solid #000;">
        <thead>
            <tr>
                <th style="border: 1px solid #000; padding: 8px;">Item Name</th>
                <th style="border: 1px solid #000; padding: 8px;">Qty</th>
                <th style="border: 1px solid #000; padding: 8px;">Amount</th>
                <th class="no-print" style="border: 1px solid #000; padding: 8px;">Action</th>
            </tr>
        </thead>
        <tbody id="itemTable">
       
        </tbody>
        <tfoot>
            <tr class="bg_color_heading">
                <td colspan="1" style="border: 1px solid #000;"></td>
                <td style="border: 1px solid #000; padding: 8px;text-align:right">Total Amount</td>
                <td id="totalAmount" style="border: 1px solid #000; padding: 8px;">₹ 0</td>
            </tr>
        </tfoot>
    </table>
       <div style="margin-top: 20px;">
        <p><strong>Instructions on School Cash Memo:</strong></p>
        <ol>
            <li>Please retain this memo for your records.</li>
             <li>This memo acknowledges the student's request for the following item(s): School Supplies (e.g., Books, Stationery).</li>
            <li>If you have any questions regarding this transaction, please contact [{{$getSetting['mobile'] ?? ''}}].</li>
            <li>Thank you for your support of [{{$getSetting['name'] ?? ''}}].</li>
        </ol>
           <hr style="border-top: 1px solid #000; margin: 20px 0;">
        <p style='text-align:center'><strong>This memo is electronically generated and no signature is required.</strong></p>
    </div>
</div>
<button  class='btn btn-primary' onclick="paymentArea()">Save and Print</button>

@else
<div class='page'>
    
    <div class='text-center text-danger'>
!!!    No Data Found !!!
    </div>
    </div

@endif
@endif

</div>           
                        
                  
    </div>
  </div>
</div>
</section>
</div>

<!-- Modal -->
<div class="modal" id="amountModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Enter Amount</h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="number" id="amountInput" class="form-control" placeholder="Enter amount">
        <small id="errorText" class="text-danger" style="display: none;">Amount exceeds pending value total!</small>
      </div>
      <div class="modal-footer d-flex justify-content-between">
     <div id='pendingValueTotal'>
         
     </div>
     <div>
        <button type="button" class="btn btn-primary" onclick="printBill()">Submit</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</div>
       
<script>

 var pendingValueTotal = 0;
    function updateItem(select) {
        const row = select.closest('tr');
        const price = select.options[select.selectedIndex].getAttribute('data-price');
        const validate_qty = select.options[select.selectedIndex].getAttribute('data-validate_qty');
        const qty = row.querySelector('input').value;
        row.querySelector('td:nth-child(3)').innerText = '₹' + qty * price;
        row.querySelector('input[type="number"]').setAttribute('data-validate_qty', validate_qty);
        updateTotal();
    }
    
    

    function updateTotal() {
        
        
        
        const rows = document.querySelectorAll('#itemTable tr');
        let total = 0;
        rows.forEach(row => {
            const qty = row.querySelector('input').value;
             const price = row.querySelector('select').options[row.querySelector('select').selectedIndex].getAttribute('data-price');
          const validate_qty = row.querySelector('input[type="number"]').getAttribute('data-validate_qty');
          if(parseInt(qty) > parseInt(validate_qty) )
          {
          toastr.error('Quantity cannot exceed '+validate_qty+'.');
           row.querySelector('input').value = parseInt(qty)-1  ;
           row.querySelector('td:nth-child(3)').innerText = '₹' + qty * price;
           
           updateTotal();
           return;
          }
           
            total += qty * price;
               row.querySelector('td:nth-child(3)').innerText = '₹' + qty * price;
        });
        document.getElementById('totalAmount').innerText = `₹ ${total}`;
        // const formatter = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' });
        // const amountInWords = new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(total);
        // document.getElementById('amountInWords').innerText = amountInWords;
        pendingValueTotal = parseInt(total);
       
    }

  const storeItems = @json($storeItems);

        function addItem() {
            if(storeItems.length == 0){
                toastr.error("Items Not Found");
            }else{
            const table = document.getElementById('itemTable');
            const row = table.insertRow();
            let options = '';
            storeItems.forEach(item => {
                options += `<option value="${item.id}" data-validate_qty="${item.qty}" data-price="${item.rate}">${item.name}</option>`;
            });
            
            // alert(JSON.stringify(storeItems));

            row.innerHTML = `
                <td style="border: 1px solid #000; padding: 8px;">
                    <select onchange="updateItem(this)">
                        ${options}
                    </select>
                </td>
                <td style="border: 1px solid #000; padding: 8px;"><input type="number" data-validate_qty="${storeItems[0].qty}" value="1" min="1" onchange="updateTotal(this)"></td>
                <td style="border: 1px solid #000; padding: 8px;">₹ ${storeItems[0].rate}</td>
                <td class="no-print" style="border: 1px solid #000; padding: 8px;"><button onclick="removeItem(this)">Remove</button></td>
            `;
            updateTotal();
        }
    }

    function removeItem(button) {
        const row = button.closest('tr');
        row.parentNode.removeChild(row);
        updateTotal();
    }
    
    function paymentArea(){
          $('#amountModal').modal('show');
          
    
      
       $('#pendingValueTotal').html(`<h1>Total : ${pendingValueTotal}/-</h1>`);
          
    }

function printBill() {
  var enteredAmount = parseInt($('#amountInput').val());
  if(pendingValueTotal <  enteredAmount)
  {
      toastr.error('Amount not to be exceeded Rs.'+ pendingValueTotal+'.');
      return
  }
     $('#amountModal').modal('hide');
   
        const items = [];
        const rows = document.querySelectorAll('#itemTable tr');
        rows.forEach(row => {
            const select = row.querySelector('select');
            const input = row.querySelector('input');
            if (select && input) {
                items.push({
                    item: select.options[select.selectedIndex].value,
                    quantity: input.value,
                    price: select.options[select.selectedIndex].getAttribute('data-price')
                });
            }
        });
        
        if(items.length != 0){

var class_type_id = $('#class_type_id').val();
var admission_id = $('#admission_id').val();

            // Send the data via AJAX to the Laravel route
            $.ajax({
                url: '/addStationaryRequest', // Update this with the correct route URL
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Add the CSRF token for Laravel
                    items: items,
                    admission_id:admission_id,
                    class_type_id:class_type_id,
                    enteredAmount:enteredAmount
                },
                success: function(response) {
                    console.log('Data saved successfully', response);
    
                    // Proceed with printing after successful save
                    const items = document.querySelectorAll('#itemTable tr');
                    items.forEach(item => {
                        const select = item.querySelector('select');
                        const input = item.querySelector('input');
                        const tdSelect = document.createElement('td');
                        const tdInput = document.createElement('td');
    
                        if (select) {
                            tdSelect.innerText = select.options[select.selectedIndex].text;
                            tdSelect.style.border = '1px solid #000';
                            tdSelect.style.padding = '8px';
                            item.replaceChild(tdSelect, select.closest('td'));
                        }
                        if (input) {
                            tdInput.innerText = input.value;
                            tdInput.style.border = '1px solid #000';
                            tdInput.style.padding = '8px';
                            item.replaceChild(tdInput, input.closest('td'));
                        }
    
                        item.querySelector('.no-print').style.display = 'none';
                    });
    
                    const printContents = document.querySelector('.page').innerHTML;
                    const originalContents = document.body.innerHTML;
    
                    document.body.innerHTML = printContents;
                    window.print();
                    document.body.innerHTML = originalContents;
                   window.location.href= '/addStationaryRequest'
                },
                error: function(error) {
                    console.error('Error saving data', error);
                    toastr.error('There was an error saving the data. Please try again.');
                }
            });
        }else{
            toastr.error('Please Select at least one item');
        }
    }

    // Initial call to set the amount in words for the preloaded items
    updateTotal();
</script>

@endsection 