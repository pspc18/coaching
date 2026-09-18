@php
$classType = Helper::classType();

$filteredNames = $dataview->filter(function ($item) {
    return $item->fees_type === 'installment';
})->pluck('name')->toArray();

@endphp


@extends('layout.app') 
@section('content')

<div class="content-wrapper">
<section class="content pt-3">
      <div class="container-fluid">
        <div class="row">   
  <div class="col-md-4 pr-0 ">
            <div class="card card-outline card-orange mr-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('fees.Add Fees Group') }}</h3>
            <div class="card-tools">
            </div>
            
            </div>                 
                <form id="quickForm" action="{{ url('feesGroup') }}" method="post">
                @csrf
                <div class="row m-2">
                        <div class="col-md-12">
                			<label style="color:red;">{{ __('messages.Name') }}*</label>
                			<input class="form-control @error('name') is-invalid @enderror"  type="text" name="name" id="name" placeholder="{{ __('messages.Name') }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror                    			
                    	</div>
                        <div class="col-md-12">
                            <div class="form-group mt-2">
                                <label for="refund_fees">Refund Fees</label>
                                <input type="checkbox"  id="refund_fees_value" value="yes" onchange="updateRefundFees(this)" >
                            </div>
                    	</div>    
                        <input type="hidden" id="fees_refund" name="fees_refund" value="no">                	
                </div>
 
                <div class="row m-2">
                    <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">{{ __('messages.submit') }} </button>
                    </div>
                </div>
                </form>
                  <!-- <div class="separator">
                        <span class="separator-text">OR</span>
                    </div>
                <div class='row p-2'>
                    <div class='col-md-12 text-center'>
                        <p style='font-size:12px' >Click on the below button for configure the school fee payment plan by setting up an installment structure. </p>
                        <button type="button" class="btn-xs btn btn-primary" data-toggle="modal" data-target="#installmentModal">
                            Set Installment Structure
                        </button>
                    </div>
                </div> -->
            </div>          
        </div>
        
         <div class="col-md-8 pl-0">
            <div class="card card-outline card-orange ml-1">
             <div class="card-header bg-primary">
            <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Fees Full Payment List') }} </h3>
            <div class="card-tools">
            <!--<a href="{{url('students/add')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-plus"></i> Add</a>-->
            <a href="{{url('fee_dashboard')}}" class="btn btn-primary  btn-sm" ><i class="fa fa-arrow-left"></i>{{ __('messages.Back') }} </a>
            </div>
             </div>  
              <div class="row m-2">
           
                    <div class="col-md-12">
                       <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
                          <thead>
                          <tr role="row">
                              <th>{{ __('messages.Sr.No.') }}</th>
                              <th>{{ __('messages.Name') }}</th>
                              <th>{{ __('Fees Refund') }}</th>
                              
                              <th>{{ __('messages.Action') }}</th>
                             
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1
                                @endphp
                               @foreach ($dataview->where('fees_type', 'full') as $item)
                                <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['name'] ?? '' }}</td>
                                        <td>{{ $item['fees_refund'] ?? '' }}</td>
                                        
                                        <td>
                                            
                                            <a href="{{ url('feesGroupEdit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs tooltip1 {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" title1="Edit"><i class="fa fa-edit"></i></a> 
                                           
                                            <a href="javascript:;" data-id='{{$item['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3 tooltip1 {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}" title1="delete"><i class="fa fa-trash-o"></i></a>
                                           
                                        </td>
                                       
                                    </tr>
                           @endforeach
                        @endif
                          </tbody>
                          </table>
                	</div>
                	        <div class="col-md-12">
                    <p class="note_text text-danger">
                        <b>Note :</b> You can't delete the fees group until it is no longer in use.
                    </p>
                </div>
                </div> 
                  <div class="row m-2 d-none">
                    <div class="col-md-12">
                <h3 class="card-title"><i class="fa fa-money"></i> &nbsp;{{ __('Fees Installment List') }} </h3>
                
                </div>
                </div>
              <div class="row m-2 d-none">
                    <div class="col-md-12">
                       <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
                          <thead>
                          <tr role="row">
                              <th>{{ __('messages.Sr.No.') }}</th>
                              <th>{{ __('messages.Name') }}</th>
                             
                              <th>{{ __('messages.Action') }}</th>
                             
                          </thead>
                          <tbody id="">
                          
                          @if(!empty($dataview))
                                @php
                                   $i=1
                                @endphp
                              @foreach ($dataview->where('fees_type', 'installment') as $item)
                                <tr>
                                        <td>{{ $i++ }}</td>
                                        <td>{{ $item['name'] ?? '' }}</td>
                                      
                                        <td>
                                            
                                            <a href="{{ url('feesGroupEdit') }}/{{ $item['id'] ?? '' }}" class="btn btn-primary  btn-xs" title="Edit"><i class="fa fa-edit"></i></a> 
                                           
                                            
                                            @php
                                               $isDeleteAllowed1 = DB::table('fees_detail')->where('fees_group_id',$item->id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->count();
                                               $isDeleteAllowed2 = DB::table('fees_assign_details')->where('fees_group_id',$item->id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->count();
                  
                                            @endphp
                                            
                                            @if(($isDeleteAllowed1+$isDeleteAllowed2) == 0)
                                            <a href="javascript:;" data-id='{{$item['id'] }}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData btn btn-danger btn-xs ml-3" title="delete"><i class="fa fa-trash-o"></i></a>
                                            @endif
                                            
                                            
                                        </td>
                                       
                                    </tr>
                           @endforeach
                        @endif
                          </tbody>
                          </table>
                	</div>
                	        <div class="col-md-12">
                    <p class="note_text text-danger">
                        <b>Note :</b> You can't delete the fees assigned to classes until it is no longer in use.
                    </p>
                </div>
                </div>
            </div>          
        </div>
        
       
    </div>  
</div>
</section>
</div>

 <!-- The Modal -->
<div class="modal fade" id="installmentModal" tabindex="-1" role="dialog" aria-labelledby="installmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form action='{{url("createFeesInstallment")}}' id="createFeesInstallment" method='Post'>
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="installmentModalLabel">Installment Structure</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="row m-2">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="numInstallments">Number of Installments</label>
                                    <input type="number" min="1" value="1" class="form-control" id="numInstallments">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="installmentPrefix">Installment Name Prefix</label>
                                    <input type="text" class="form-control" id="installmentPrefix" value="" placeholder='Installment Prefix'>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="text-white">Preview</label><br>
                                    <button type="button" class="btn btn-primary" id="previewBtn">Preview</button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div id="errorNotification" class="alert alert-danger" style="display: none;font-size:12px"></div>
                            </div>
                            <div class="col-md-12">
                                <!-- Table to Display Installment Structure -->
                                <div id="installmentTableContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
<style>
    .padding_table thead tr{
    background: #002c54;
    color:white;
}
    
.padding_table th, .padding_table td{
     padding:5px;
     font-size:14px;
}
.separator {
        text-align: center;
        margin: 20px 0;
        border-bottom: 1px solid #ddd;
        line-height: 0.1em;
    }

    .separator-text {
        background-color: #fff;
        padding: 0 10px;
        color: #777;
    }
</style>

<script>
    function updateRefundFees(checkbox) {
    // Update hidden input to send value "yes" or "no"
    if (checkbox.checked) {
        document.getElementById('fees_refund').value = 'yes';
    } else {
        document.getElementById('fees_refund').value = 'no';
    }
}
</script>
<script>
    $(document).ready(function(){
        $('#select_all').click(function(){
            if($(this).prop('checked')){
                $('.select_checkbox').prop('checked',true);
            }else{
                $('.select_checkbox').prop('checked',false);
            }  
        });
        
        $(document).on('click','.select_checkbox',function(){
            var total_checkbox_count = $('.select_checkbox').length;
            var total_checked_checkbox_count = $('.select_checkbox:checkbox:checked').length;
            if(total_checkbox_count == total_checked_checkbox_count){
                $('#select_all').prop('checked',true);
            }else{
                $('#select_all').prop('checked',false);
            }
        });
        
        /*$('#createFeesInstallment').on('submit', function(event) {
            event.preventDefault();
            
            var installment_length = $('.installment-name').length;
            
            if(installment_length == 0){
                toastr.error('Please assign Installment');
            }else{
                $('#createFeesInstallment').trigger('submit');
            }
        });*/
    });
</script>


<script>
  $('.deleteData').click(function() {
  var delete_id = $(this).data('id'); 
  
  $('#delete_id').val(delete_id); 
  } );
</script>

<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('feesGroupDelete') }}" method="post">
              	 @csrf
      <div class="modal-body">
              <input type=hidden id="delete_id" name=delete_id>
              <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }}  ?</h5>
      </div>
      <div class="modal-footer">
                    <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
                    <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
         </div>
       </form>
    </div>
  </div>
</div>


<!--
<script>
 let hasError = false;
const installmentNamesToCheck = @json($filteredNames);
document.getElementById('previewBtn').addEventListener('click', function() {
    // Get values from inputs
    const totalAmount = parseInt(document.getElementById('totalAmount').value);
  
    if(isNaN(totalAmount)){
        toastr.error("Please Enter Amount");
        $('#totalAmount').focus();
        return;
    }
    
    const numInstallments = parseInt(document.getElementById('numInstallments').value);
    const installmentFrequency = parseInt(document.getElementById('frequency').value);
    let dueDay = parseInt(document.getElementById('due_date_on_every').value);
    const installmentAmount = Math.floor(totalAmount / numInstallments);
    const remainder = totalAmount % numInstallments;
    const fullMonthList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    // Predefined array of installment names to check against
     // Example values, update as needed

    // Clear previous error message
    const errorNotification = document.getElementById('errorNotification');
    errorNotification.style.display = 'none';
    errorNotification.innerHTML = '';

    // Create table
    let tableHTML = `
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Installment Name</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Due Date</th>
                    <th>Fine[In Percentage %]</th>
                </tr>
            </thead>
            <tbody>
    `;

   

    for (let i = 1; i <= numInstallments; i++) {
        let amount = installmentAmount;
        if (i <= remainder) {
            amount += 1;
        }
        
        // Determine the selected month based on frequency
        let selectedMonthIndex = ((i - 1) * installmentFrequency) % 12;
        let month = fullMonthList[selectedMonthIndex];

        // Calculate due date
        let year = new Date().getFullYear();
        let monthIndex = selectedMonthIndex + 1; // JavaScript months are 0-11
        
        // Get the last day of the month
        let nextMonth = new Date(year, monthIndex, 1);
        nextMonth.setDate(0); // Set date to the last day of the previous month
        let lastDayOfMonth = nextMonth.getDate();

        // Ensure dueDay does not exceed the last day of the month
        if (dueDay > lastDayOfMonth) {
            dueDay = lastDayOfMonth;
        }

        let monthStr = monthIndex.toString().padStart(2, '0'); // Ensure month is two digits
        let dueDate = `${year}-${monthStr}-${dueDay.toString().padStart(2, '0')}`; // Ensure day is two digits

        let monthOptions = '';
        fullMonthList.forEach((month, index) => {
            let selected = index === selectedMonthIndex ? 'selected' : '';
            monthOptions += `<option value="${month}" ${selected}>${month}</option>`;
        });

        // Check if the installment name matches any value in the array
        let installmentName = `Installment ${i}`;
        let rowClass = '';
        if (installmentNamesToCheck.includes(installmentName)) {
            rowClass = 'class="bg-danger"';
            hasError = true;
        }

        tableHTML += `
            <tr ${rowClass}>
                <td>${i}</td>
                <td><input type="text" class="form-control installment-name" name="installment_name[]" value="${installmentName}"></td>
                <td><input type="text" class="form-control installment-amount" name="installment_value[]" value="${amount}"></td>
                <td>
                    <select class="form-control" name="installment_month[]">
                        ${monthOptions}
                    </select>
                </td>
                <td><input type="date" class="form-control" name="installment_due_date[]" value="${dueDate}"></td>
                <td><input type="number" min='0' value='0' max='100'class="form-control" name="installment_fine[]" placeholder="Enter fine"></td>
            </tr>
        `;
    }

    tableHTML += `
            </tbody>
        </table>
    `;

    // Append table to container
    document.getElementById('installmentTableContainer').innerHTML = tableHTML;

    // Show error message if any match is found
    if (hasError) {
      errorNotification.innerHTML = `Note: One or more installment names match the restricted list. Please review the highlighted rows.<br>
Caution: Proceeding will override the existing data with the new entries.`;
        errorNotification.style.display = 'block';
    }
});

$(document).ready(function() {
  $(document).ready(function() {
      $( "#installmentModal" ).on( "focusout", ".installment-name", function() {
        hasError =false;
    $('.installment-name').each(function() {
   
            // Get the value of the input field
            let installmentName = $(this).val();
    
 if (installmentNamesToCheck.includes(installmentName)) {
          $(this).closest('tr').addClass('bg-danger');
         
            hasError = true;
        }
        else{
             $(this).closest('tr').removeAttr('class');
             
        }
        
    });
    
     if (hasError) {
        errorNotification.innerHTML = `Note: One or more installment names match the restricted list. Please review the highlighted rows.\n
Caution: Proceeding will override the existing data with the new entries.`;
        errorNotification.style.display = 'block';
    }
    else
    {
          errorNotification.style.display = 'none';
    }
    });
});
});

</script>
-->
<script>
document.getElementById('previewBtn').addEventListener('click', function() {
    // Get the number of installments and prefix
    const numInstallments = parseInt(document.getElementById('numInstallments').value);
    const installmentPrefix = document.getElementById('installmentPrefix').value;
    
    if (isNaN(numInstallments) || numInstallments <= 0) {
        toastr.error("Please Enter a Valid Number of Installments");
        $('#numInstallments').focus();
        return;
    }

    if (!installmentPrefix) {
        toastr.error("Please Enter an Installment Name Prefix");
        $('#installmentPrefix').focus();
        return;
    }

    // Create table
    let tableHTML = `
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Sr. No</th>
                    <th>Installment Name</th>
                </tr>
            </thead>
            <tbody>
    `;

    for (let i = 1; i <= numInstallments; i++) {
        tableHTML += `
            <tr>
                <td>${i}</td>
                <td><input type="text" class="form-control installment-name" name="installment_name[]" value="${installmentPrefix}"></td>
            </tr>
        `;
    }

    tableHTML += `
            </tbody>
        </table>
    `;

    // Append table to container
    document.getElementById('installmentTableContainer').innerHTML = tableHTML;
});
</script>
<script>
        $(document).ready(function() {
        $('#selected_class, #admissionNo').on('change focusout', function() {
      
                var classTypeId = $('#selected_class').val();
                var admissionNo = $('#admissionNo').val();

                if(classTypeId != "" || admissionNo != ""){ 
                    $.ajax({
                        headers: {
    					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
    				},
                        url: '/getStudentsList',  // Replace with your actual route
                        method: 'Post',
                        data: {
                                admissionNo:admissionNo,
                            class_type_id: classTypeId
                        },
                        success: function(response) {
                            $('#tbody_modification').html(response);
                        },
                        error: function(xhr) {
                            console.log('An error occurred:', xhr);
                        }
                    }); 
                }else{
                    toastr.error("Please fill admission No. Or class to search data");
                    $('#tbody_modification').html("");
                }
            });
        });
        
        </script>
       
<script>
        document.addEventListener("DOMContentLoaded", function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

<style>
.tooltip {
    z-index: 1050 !important;
}
</style>
@endsection      