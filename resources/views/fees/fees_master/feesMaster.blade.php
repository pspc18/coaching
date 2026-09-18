@php
$getFeesGroup = Helper::getFeesGroup();
$classType = Helper::classType();

$filteredData = $dataview;

@endphp
@extends('layout.app')
@section('content')

<div class="content-wrapper">

  <section class="content pt-3">
      
      <div class="container-fluid">
    <div class="row align-items-center">
      <div class="col-md-4">
        <nav aria-label="breadcrumb">
    <ol class="breadcrumb p-0" style='margin-top:5px'>
      <li class="breadcrumb-item"><a href="{{url('/')}}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{url('fee_dashboard')}}">Fees Management</a></li>
      <li class="breadcrumb-item active" aria-current="page">Fees Master</li>
    </ol>
  </nav>
      </div>
      <div class="col-md-8 text-md-right">
        <button style='margin-top:-11px' class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#students_list_modal">Student Fee Assign</button>
        <button style='margin-top:-11px' id="fees_modification_btn" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fees_modification">Student Fee Modification</button>
        <!--<button style='margin-top:-11px' class="btn btn-primary">Modify Fees</button>-->
        <!--<button style='margin-top:-11px' class="btn btn-primary">Modify Fees</button>-->
      </div>
    </div>
  </div>


    <div class="container-fluid">
      <div class="row">
      
           
          <div class="card card-outline w-100">
            
            
              <div class="row m-2">
                   
                    <div class="col-md-10">
                       <div class="card">
                           <div class="card-body pl-0 pr-0 pt-2 pb-2">
                                               <div class="col-md-12 text-left"> 
                                            <p class="text-danger font-weight-bold ">Full Payment assign to {{ __('common.Class') }} :-</p>
                                        </div>
                                        <form  class="col-md-12" id="quickForm" action="{{ url('feesMasterAdd') }}" method="post">
                              @csrf
                                <div class="col-md-12">
                                  <div class="form-group">
                                    <label style="color:red;">{{ __('common.Class') }}*</label>
                                    <select class="form-control select2 @error('class_type_id') is-invalid @enderror " id="class_type_id" name="class_type_id" required>
                                      <option value="">{{ __('messages.Select') }}</option>
                                      @if(!empty($classType))
                                      @foreach($classType as $type)
                                      <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                      @endforeach
                                      @endif
                                    </select>
                                    @error('class_type_id')
                                    <span class="invalid-feedback" role="alert">
                                      <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                  </div>
                                </div>
                                <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped dataTable dtr-inline padding_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><input type="checkbox" id="select_group" checked for="select_group"> <label for="select_group">{{ __('Select All') }}</label></th>
                                                <th>{{ __('fees.Fees Group') }}*</th>
                                                <th>{{ __('messages.Amount') }}*</th>
                                                <th>{{ __('Due Date') }}</th>
                                                <th>{{ __('Editable') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($getFeesGroup))
                                                @php $i=1; @endphp
                                                @foreach ($getFeesGroup as $type)
                                                    <tr>
                                                        <td> {{ $i++ }}</td>
                                                        <td> <input type="checkbox" class="group_checkbox" id="fees_group_id" name="fees_group_id[]" value="{{ $type->id ?? ''  }}" checked></td>
                                                        <td>{{ $type->name ?? ''  }}</td>
                                                        <td>
                                                            <input class="form-control amount_0 @error('amount') is-invalid @enderror" type="text" name="amount[{{ $type->id ?? ''  }}]" id="amount" placeholder="Amount" value="0" onkeypress="javascript:return isNumber(event)">
                                                        </td>
                                                        <td>
                                                            <input class="form-control" type="date" name="installment_due_date[{{ $type->id ?? ''  }}]">
                                                        </td>
                                                        <td>
                                                            <input class="form-control change_box" data-amount_id="0" type="checkbox" name="editable[{{ $type->id ?? ''  }}]" id="editable">
                                                            <input type="hidden" name="editable_value[{{ $type->id ?? ''  }}]" class="close_edited_value" id="editable_value" value="0">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                  <div class="col-md-12">
                                <div class="col-md-12 text-center">
                                  <button type="submit" class="btn btn-primary">{{ __('messages.submit') }} </button>
                                </div>
                              </div>
                              </form>
                           </div>
                       </div>
                    </div>
                    
                    <div class="col-md-6 d-none">
                        <div class="card">
                            <div class="card-body pl-0 pr-0 pt-2 pb-2">
                                <form  class="col-md-12" id="installment_form">
                    <div class="col-md-12 text-left"> 
                        <p class="text-danger font-weight-bold ">Installment Payment assign to class :-</p>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="totalAmount">Amt of Installment</label>
                                <input type="text" class="form-control" id="totalAmount" name='total_amount' placeholder="Enter total amount">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                          <div class="form-group">
                                <label style="color:red;">{{ __('common.Class') }}*</label>
                                <select class="form-control" id="installment_class_type_id select2" name="installment_class_type_id">
                                  <option value="">{{ __('messages.Select') }}</option>
                                  @if(!empty($classType))
                                  @foreach($classType as $type)
                                  <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                                  @endif
                                </select>
                          </div>
                        </div>
                        
                        <!--<div class="col-md-3">
                            <div class="form-group">
                                <label for="numInstallments">Installment Frequency</label>
                                <input type="number" min="1" value='1' max='12' class="form-control" id="frequency">
                            </div>
                        </div>-->
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="numInstallments">Due Date On Every</label>
                               <select class="form-control " name="due_date_on_every" id='due_date_on_every'>
									<!--<option value="">{{ __('common.Select') }}</option>-->
								
									@for($i=1; $i < 32; $i++)
									 <option value="{{ sprintf('%02d', $i) }}" {{ sprintf('%02d', $i) == "05" ? 'selected' : '' }}>{{ sprintf('%02d', $i) }}</option>
									@endfor
								
								</select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="text-white">Preview</label><br>
                                <button type="button" class="btn btn-primary" id="previewBtn">Preview</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-12">
                        <div id="errorNotification" class="alert alert-danger" style="display: none;font-size:12px"></div>
                    </div>
                
                    <div id="installments_data" style="display:none;">
                        <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped dataTable dtr-inline padding_table">
            <thead>
                <tr>
                    <th><input type='checkbox' id="select_all" {{ count($feesGroupInstallmentsList) == 0 ? 'disabled' : '' }} /></th>
                    <th>Installment Name</th>
                    <th>Amount</th>
                    <th>Month</th>
                    <th>Due Date</th>
                    <th>Fine[In Percentage %]</th>
                </tr>
            </thead>  
            <tbody id="academicCalendar">
                @if(count($feesGroupInstallmentsList) != 0)
                    @php
                       $i=1;
                    @endphp
                    @foreach ($feesGroupInstallmentsList as $key => $item)
                        <tr>
                                <td><input type='checkbox' name="installmentRow[]" class="select_checkbox" value="{{ $item->id }}" /></td>
                                <td>
                                    {{$item['name'] ?? ''}}
                                    <input type="hidden" class="form-control installmentName install" id="installment_name_{{$item->id}}" value="{{ $item['name'] ?? '' }}">
                                    <input type="hidden" class="form-control installmentId" id="installment_id_{{$item->id}}" value="{{$item['id'] ?? ''}}">
                                </td>
                                <td><input type="text" id="installment_amount_{{ $item->id }}" class="form-control installment-amount amountInstallment"></td>
                                <td>
                                    <select class="form-control installmentMonth" id="installment_month_{{ $item->id }}">
                                        <option value="Jan" selected="">Jan</option>
                                        <option value="Feb">Feb</option>
                                        <option value="Mar">Mar</option>
                                        <option value="Apr">Apr</option>
                                        <option value="May">May</option>
                                        <option value="Jun">Jun</option>
                                        <option value="Jul">Jul</option>
                                        <option value="Aug">Aug</option>
                                        <option value="Sep">Sep</option>
                                        <option value="Oct">Oct</option>
                                        <option value="Nov">Nov</option>
                                        <option value="Dec">Dec</option>
                                    </select>
                                </td>
                                <td><input type="date" class="form-control installmentDueDate" id="installment_due_date_{{ $item->id }}"></td>
                                <td><input type="number" class="installmentFine" min="0" value="0" max="100" class="form-control" id="installment_fine_{{ $item->id }}" placeholder="Enter fine"></td>
                            </tr>
                    @endforeach
                    @else
                    <tr class="text-center">
                        <td class="text-danger" colspan="12">Please Create Installment First !!</td>
                    </tr>
                @endif
            </tbody>
        </table>
                	</div>
                </div>
                    </div>
                     
                
                
                
                 <div class="row m-2">
                    <div class="col-md-12 text-center">
                  <button type="button" id="installment_submit_button" style="display:none;" class="btn btn-primary">{{ __('messages.submit') }} </button>
                </div>
                        </div>
              </form>
                            </div>
                        </div>
                    </div>
              
              
              </div>
              
              <div class="card m-2">
                  <div class="card-body pl-0 pr-0 pt-2 pb-2">
                       <div class="col-md-12">
           <div class="col-md-12  text-left"> 
                <p class="text-danger font-weight-bold">Fees assigned to classes list :-</p>
            </div>
            <div class="row m-2">
                    <div class="col-md-12">
                            <div class="row">
                      
                            <div class="col-md-2">
                                <div class="form-group">
                                    <lable>{{ __('common.Class') }}</lable>
                                    <select class="form-control" id="classTypeID" name="class_type_id">
                                        <option value="">Select</option>
                                        @foreach($classType as $class)
                                        <option value="{{ $class->id }}">{{ $class->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <lable>&nbsp;</lable><br>
                                <button type="button" class="filterData btn btn-primary">Search</button>
                            </div>
                            </div>
                    </div>
              <div class="col-md-12">
                <table id="example1" class="table table-bordered table-striped dataTable dtr-inline padding_table">
                  <thead>
                    <tr role="row">
                      <th>{{ __('messages.Sr.No.') }}</th>
                      <th>{{ __('messages.Class') }}</th>
                      <th>{{ __('fees.Fees Group') }}</th>
                     
                      <th>{{ __('messages.Action') }}</th>
                      
                    </tr>
                  </thead>
                  <tbody id="">

                    @if(!empty($dataview))
                    @php
                    $i=1;
                    $masterFeesArray = [];
                    @endphp
                    @foreach ($dataview as $item)
                        @php
                            $masterFees = [];
                        @endphp
                    <tr class="all_data" data-class="{{$item->class_type_id}}">
                      <td>{{ $i++ }}</td>
                      <td>{{ $item['ClassTypes']['name'] ??'' }}</td>
                     <td>
                          @php
                            $allData = DB::table('fees_master')
                            ->select('fees_master.amount','fees_group.name as fees_group_name','fees_master.id as fees_master_id','fees_master.installment_due_date')
                            ->leftjoin('fees_group','fees_group.id','=','fees_master.fees_group_id')
                            ->select('fees_master.amount','fees_group.name as fees_group_name','fees_master.id','fees_master.fees_group_id','fees_master.installment_due_date')
                            ->where('class_type_id',$item->class_type_id)->where('fees_master.session_id',Session::get('session_id'))->whereNull('fees_master.deleted_at')->get();
                            
                          @endphp
                        @if(!empty($allData))
                        @foreach ($allData as $mydata)
                            @php
                            //dd($mydata);
                                $masterFees[] = $mydata->fees_group_name;
                            @endphp
                            <p style="margin-bottom: 0%;"> {{ $mydata->fees_group_name ?? '' }} = <span>{{ $mydata->amount ?? '' }}</span> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;Due Date = <span>{{ $mydata->installment_due_date ?? '' }}</span>
                        @php
               
                        $isDeleteAllowed1 = DB::table('fees_detail')->where('fees_group_id',$mydata->fees_group_id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->count();
                        $isDeleteAllowed2 = DB::table('fees_assign_details')->where('class_type_id',$item['ClassTypes']['id'])->where('fees_group_id',$mydata->fees_group_id)->where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->count();
                  
                     @endphp
                     
                    
                        <a href="javascript:;" data-groupname='{{$mydata->id ?? ''}}' data-bs-toggle="modal" data-bs-target="#Modal_id" class="deleteData {{ Helper::permissioncheck(11)->delete ? '' : 'd-none' }}"><i class="fa fa-remove text-danger" title="Delete"></i></a>
                   
                        </p>
                        @endforeach
                        @endif
                      </td>
                      
                      <td>
                        <a href="{{ url('feesMasterEdit') }}/{{$item['class_type_id'] ?? '' }}" class="text-success tooltip1 {{ Helper::permissioncheck(11)->edit ? '' : 'd-none' }}" title1="Edit"><i class="fa fa-edit pl-2"></i></a>
                      </td>
                     
                    </tr>
                    @php
                        $masterFeesArray[$item->class_type_id] = $masterFees;
                    @endphp
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
        </div>
                  </div>
              </div>
                 
              

            
           
          </div>
       


      </div>
    </div>
  </section>
</div>



  <!-- Modal -->
    <div class="modal fade" id="fees_modification" tabindex="-1" aria-labelledby="feesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="feesModalLabel">Modify Student Fees</h5>
                    <button type="button" class="btn btn-outline-danger btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                </div>
                <div class="modal-body">
                    <form id="feesForm" class="mb-3">
                        <div class="row">
                                                 <!-- <div class="col-md-2">
									<div class="form-group">
										<label>Admission Type(Non RTE)</label>
										<select class="form-control invalid" id="admission_type_id_modify" name="admission_type_id_modify">
										
											<option value="1">Yes</option>
											<option value="2">No</option>
										</select>
									   
									</div>
								</div> -->
                            <div class="col-md-2">
                                <label for="admissionNo" class="form-label">Admission No</label>
                                <input type="text" class="form-control" id="admission_modification" placeholder="Enter Admission No">
                            </div>
                            <div class="col-md-2">
                                <label for="class" class="form-label">{{ __('common.Class') }}</label>
                                <select class="form-control" id="class_modification" name="class_type_id">
                                    <option value="">{{ __('messages.Select') }}</option>
                                    @if(!empty($classType))
                                    @foreach($classType as $type)
                                    <option value="{{ $type->id }}">{{ $type->name ?? '' }}</option>
                                    @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label for="search" class="text-white form-label">Search</label>
                                <input type="button" class="btn btn-primary form-control" value="Search" id="searchButton"/>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class='row'> 
                    
                    <div class='col-md-12 text-danger mt-3' style='font-size:12px;line-height:2px;'>
                        <p>1. Verify if there are any payments under the current fee head. If payments exist, modifications are not allowed.</p>
                        <p>2. There is no need to manually save changes. The system will automatically update the fees when the input field loses focus.</p>
                        </div> 
                        </div>
                          <hr>
                    <div class='row'> 
                    
                    
                      
                    <div class='col-md-12'> 
                            <div  style='overflow: scroll;height:300px'>
                    <table class="table table-bordered  text-center padding_table" >
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Mobile</th>
                                <th>Fees Assign Detail</th>
                                <th style="width:70px;">Discount</th>
                                <th>Due Date</th>
                                <th style="width:80px;">Fine %</th>
                                <th style="width:80px;">Fees Refund </th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbody_modification"></tbody>
                    </table>
                    </div>
                    
                    </div>
                    <div class='col-md-5' id="feesInputsContainer"> 
                    
                    
                    </div>
                    </div>
               
                 
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
  <style>
      @media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .padding_table th, .padding_table td {
        padding: 8px 5px;
        font-size: 13px;
    }
    
    .padding_table input[type="text"],
    .padding_table input[type="date"],
    .padding_table select {
        font-size: 12px;
        padding: 5px;
    }
    
    .padding_table th:first-child,
    .padding_table td:first-child {
        min-width: 30px;
    }
    
    .padding_table th:nth-child(2),
    .padding_table td:nth-child(2) {
        min-width: 80px;
    }
    
    .padding_table th:nth-child(3),
    .padding_table td:nth-child(3) {
        min-width: 100px;
    }
    
    .padding_table th:nth-child(4),
    .padding_table td:nth-child(4) {
        min-width: 80px;
    }
    
    .padding_table th:nth-child(5),
    .padding_table td:nth-child(5) {
        min-width: 100px;
    }
    
    .padding_table th:nth-child(6),
    .padding_table td:nth-child(6) {
        min-width: 70px;
    }
}
  </style>
  

<style> 
    .padding_table thead tr{
    background: #002c54;
    color:white;
}
    
.padding_table th, .padding_table td{
     padding:5px;
     font-size:14px;
     vertical-align: inherit;
}


</style>
<!--<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>-->

 <script>
        $(document).ready(function() {
            $('#searchButton').click(function() {
                var admissionNo = $('#admission_modification').val();
                var classTypeId = $('#class_modification').val();
                var admission_type_id_modify = $('#admission_type_id_modify').val();

                $.ajax({
                    headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
                    url: '/feesModification',  // Replace with your actual route
                    method: 'Post',
                    data: {
                        admissionNo: admissionNo,
                        class_type_id: classTypeId,
                        admission_type_id_modify:admission_type_id_modify
                    },
                    success: function(response) {
                        $('#tbody_modification').html(response);
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr);
                    }
                }); 
            });
            
            $('#tbody_modification').on('click', '.delete_assigned', function() {
         
                var fees_assign_detail_id = $(this).data('detail_id');
                
                var currentTd = $(this);

                $.ajax({
                    headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
                    url: '/deleteAssignedFees',  // Replace with your actual route
                    method: 'POST',
                    data: {
                        fees_assign_detail_id: fees_assign_detail_id,
                      
                    },
                    success: function(response) {
                     currentTd.closest('td').remove();
                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr);
                    }
                }); 
            });
   
            
            
         $('#tbody_modification').on('focusout', '.fees_assign_detail', function() {
            var fees_assign_detail_id = $(this).data('detail_id');
            var value = $(this).val();
            var old_value = $(this).data('old_value');
            var field = $(this).attr('name');
       // alert(field);
            var currentTd = $(this);
            var parentTr = currentTd.closest('tr');

            function compareValues(value1, value2) {
                // Check if both values are valid dates
                const date1 = Date.parse(value1);
                const date2 = Date.parse(value2);
                
                
                if(value2 == 0){
                     return true;
                }else if (!isNaN(date1) && !isNaN(date2)) {
                    // Both values are valid dates
                    return date1 !== date2;
                }
            
                // Check if both values are numbers (integer or float)
                const num1 = parseFloat(value1);
                const num2 = parseFloat(value2);
                
                if (!isNaN(num1) && !isNaN(num2)) {
                    // Both values are numbers
                    return num1 !== num2;
                }
            
                // If they are not both dates or both numbers, they are not equal
                return false;
            }
            
           
if(field == 'fees_group_amount'){
    var pay_fees = $(this).data('pay_fees');

    if(value < pay_fees){
        toastr.error('The student has already paid an amount of Rs '+pay_fees);
        $(this).val(old_value);
       return
    }

}

    if (compareValues(value, old_value) ) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/updateAssignedFees',  // Replace with your actual route
            method: 'POST',
            data: {
                fees_assign_detail_id: fees_assign_detail_id,
                value: value,
                field: field
            },
            success: function(response) {
                toastr.success('Fees Updated Successfully');
                currentTd.data('old_value', value);
                currentTd.val(value);
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
});
            
        
            
            
        });

  $('#tbody_modification').on('click', '.add-btn', function() {
                var row = $(this).closest('tr');
                var name = row.find('td:nth-child(1)').text();
                var mobile = row.find('td:nth-child(2)').text();
                var feesDetail = `
                    <div class="fees-detail">
                        <h5>Fees for ${name} (${mobile})</h5>
                        <div class="mb-3">
                            <label for="feesName" class="form-label">Fees Name</label>
                            <input type="text" class="form-control" name="feesName[]" placeholder="Enter Fees Name">
                        </div>
                        <div class="mb-3">
                            <label for="feesAmount" class="form-label">Fees Amount</label>
                            <input type="text" class="form-control" name="feesAmount[]" placeholder="Enter Fees Amount">
                        </div>
                    </div>
                `;
                $('#feesInputsContainer').append(feesDetail);
            });
      
        function submitFeesModification() {
            // Handle the save changes button click event here
            console.log('Save changes clicked');
        }
    </script>

<script>

  
  $(document).ready(function(){
     $('.filterData').click(function(){
        var classId = $('#classTypeID').find(':selected').val();
        var elements = $('.all_data');
        var count = elements.length;
        
        for (var i = 0; i < count; i++) {
            if(classId != ""){
            var class_type_id = elements.eq(i).data('class');
            if(class_type_id == classId){
                elements.eq(i).show();
            }else{
                elements.eq(i).hide();
            }
            }else{
                elements.eq(i).show();
            }
        }
     }); 
  });
</script>

<script>
$(document).ready(function(){
   $(document).on('click','.change_box',function(){
       var $row = $(this).closest('tr'); // Get the current row
       var amountField = $row.find('.amount_0'); // Find the amount field in the current row
       if($(this).prop('checked')){
           $(this).siblings('input').val(1);
           amountField.val(0);
           amountField.attr('type','hidden');
       }else{
           $(this).siblings('input').val(0);
           amountField.val(0);
           amountField.attr('type','text');
       }
   }); 
});

/*$(document).ready(function(){
   $(document).on('click','.change_box',function(){
       var amount_id = $(this).data('amount_id');
       if($(this).prop('checked')){
           $(this).siblings('input').val(1);
           $('.amount_' + amount_id).val(0);
           $('.amount_' + amount_id).attr('type','hidden');
       }else{
           $(this).siblings('input').val(0);
           $('.amount_' + amount_id).val(0);
           $('.amount_' + amount_id).attr('type','text');
       }
   }); 
});*/
</script>

<script>
    $('.deleteData').click(function() {
    var delete_id = $(this).data('groupname');

    $('#delete_id').val(delete_id);
  });
</script>
<!-- The Modal -->
<div class="modal" id="Modal_id">
  <div class="modal-dialog">
    <div class="modal-content" style="background: #555b5beb;">

      <div class="modal-header">
        <h4 class="modal-title text-white">{{ __('messages.Delete Confirmation') }}</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
      </div>

      <form action="{{ url('feesMasterDelete') }}" method="post">
        @csrf
        <div class="modal-body">
          <input type=hidden id="delete_id" name=delete_id>
          <h5 class="text-white">{{ __('messages.Are you sure you want to delete') }} ?</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default waves-effect remove-data-from-delete-form" data-bs-dismiss="modal">{{ __('messages.Close') }}</button>
          <button type="submit" class="btn btn-danger waves-effect waves-light">{{ __('messages.Delete') }}</button>
        </div>
      </form>
    </div>
  </div>
</div>





<div class="modal fade" id="students_list_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
        
            <!-- Modal Header -->
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Assign Installment Payment to Students for the selected {{ __('common.Class') }}</h4>
            </div>
            <form id="assignFeesMultiple" action="{{ url('assignFeesMultipleStudents') }}" method="POST">
            @csrf    
            <div class="modal-body">
                <div class="col-md-12">
                    <div class="row">
                                                 <!-- <div class="col-md-2">
									<div class="form-group">
										<label>Admission Type(Non RTE)</label>
										<select class="form-control invalid" id="admission_type_id" name="admission_type_id">
										
											<option value="1">Yes</option>
											<option value="2">No</option>
										</select>
									   
									</div>
								</div> -->
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>{{ __('common.Class') }}</label>
                                <select class="form-control" id="bulk_class_type_id" name="class_type_id">
                                  <option value="">{{ __('messages.Select') }}</option>
                                  @if(!empty($classType))
                                  @foreach($classType as $type)
                                  <option value="{{ $type->id }}">{{ $type->name ?? ''  }}</option>
                                  @endforeach
                                  @endif
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Admission No</label>
                                <input type="text" class="form-control" placeholder="Admission No" name="admissionNo" id="bulk_admission_no">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fees Master</label>
                                <select class="form-control select2" multiple id="bulk_fees_master_ids" name="fees_master_ids[]" required>
                                  <!--<option value="">{{ __('messages.Select') }}</option>-->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                        
                <div class="col-md-12 overflow_scroll">
                    <table class="table table-bordered  text-center padding_table" >
                        <thead>
                            <tr>
                                <th><input type='checkbox' id="all_students" /></th>
                                <th>Name</th>
                                <th>Admission No</th>
                                <th>Mobile</th>
                                <th>Father</th>
                                <th>Assigned Fees</th>
                              
                            </tr>
                        </thead>
                        <tbody id="tbody_students_list"></tbody>
                    </table>
                </div>
                
                <div class="col-md-12">
                    <div class="note_text note">
                        <p>1. If any selected fee head is already assigned to a student, the system will skip that head and assign the remaining heads.</p>
                        <p>2. To modify a student's assigned fees, go to the fee modification area.</p>
                    </div>
                </div>
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
            </form>
        </div>
    </div>
</div>

<style>
.note{
    background-color: #9e9e9e5e;
    border-radius: 4px;
    padding: 10px;
}

.note p{
    color: red;
    font-size:12px;
    font-weight: 400;
    margin-bottom:0px;
}
.overflow_scroll{
    height:300px;
    overflow-y:scroll;
}
</style>


<script>
    function getStudents(class_type_id,bulk_admission_no,admission_type_id){
         $('#tbody_students_list').html('');
        $.ajax({
            headers: {
			    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
		    },
            url: '/getStudentsList',  // Replace with your actual route
            method: 'Post',
            data: {
                admissionNo:bulk_admission_no,
                class_type_id:class_type_id,
                admission_type_id:admission_type_id
            },
            success: function(response) {
                $('#tbody_students_list').html(response);
                $('#all_students').prop('checked',false);
                $('#bulk_class_type_id').val(class_type_id);
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
    
    function getMasterData(class_type_id){
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/getMasterData',  // Replace with your actual route
            method: 'POST',
            data: {
                class_type_id: class_type_id
            },
            success: function(response) {
                var masterData = [];
                if(response.length != 0){
                    $('#bulk_fees_master_ids').html("");
                        
                    for(var i = 0; i < response.length; i++){
                        var code = '<option value="'+ response[i].id +'">'+ response[i].fees_group_name +'</option>'; // Corrected quote
                        masterData.push(code); 
                    }
                    if(masterData.length > 0){
                     $('#bulk_fees_master_ids').html(masterData.join(''));   
                    }
                    
                     
                }
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }
        
        
const installmentNamesToCheck = @json($feesGroupInstallmentsList);

document.getElementById('previewBtn').addEventListener('click', function() {
    let hasError = false;

    // Get values from inputs
    const totalAmount = parseInt(document.getElementById('totalAmount').value);
    // const installmentFrequency = parseInt(document.getElementById('frequency').value);
    const installmentFrequency = 1;
    const classTypeId = document.getElementById('installment_class_type_id').value;
    let dueDay = parseInt(document.getElementById('due_date_on_every').value);
    
    const numInstallments = $('.select_checkbox:checkbox:checked').length;
    const installmentNamesToCheckLength = installmentNamesToCheck.length;
    const installmentAmount = Math.floor(totalAmount / numInstallments);
    const remainder = totalAmount % numInstallments;
    const fullMonthList = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    if(installmentNamesToCheckLength === 0){
        toastr.error("Please Create Installment First !!");
        return;
    }

    if (classTypeId === "") {
        toastr.error("Please Select Class");
        $('#installment_class_type_id').focus();
        return;
    }
    
    if (isNaN(totalAmount)) {
        toastr.error("Please Enter Amount");
        $('#totalAmount').focus();
        return;
    }
    
    $('#installments_data').show();

    const errorNotification = document.getElementById('errorNotification');
    errorNotification.style.display = 'none';
    errorNotification.innerHTML = '';
    
    const checkedCheckboxes = $('.select_checkbox:checkbox:checked');
    
    $('.amountInstallment').val("");
    $('.installmentMonth').val("Jan");
    $('.installmentDueDate').val("");
    $('.installmentFine').val(0);
    
    for (let i = 0; i < numInstallments; i++) {
        let amount = installmentAmount;
        if (i < remainder) {
            amount += 1;
        }

        let selectedMonthIndex = (i * installmentFrequency) % 12;
        let month = fullMonthList[selectedMonthIndex];

        let year = new Date().getFullYear();
        let monthIndex = selectedMonthIndex + 1;

        let nextMonth = new Date(year, monthIndex, 1);
        nextMonth.setDate(0);
        let lastDayOfMonth = nextMonth.getDate();

        if (dueDay > lastDayOfMonth) {
            dueDay = lastDayOfMonth;
        }

        let monthStr = monthIndex.toString().padStart(2, '0');
        let dueDate = `${year}-${monthStr}-${dueDay.toString().padStart(2, '0')}`;

        let installmentName = `Installment ${i + 1}`;
        let rowClass = '';

        if (installmentNamesToCheck.includes(installmentName)) {
            rowClass = 'class="bg-danger"';
            hasError = true;
        }
        
        var row_id = checkedCheckboxes.eq(i).val();
        
        $('#installment_amount_' + row_id).val(amount);
        $('#installment_due_date_' + row_id).val(dueDate);
        $('#installment_month_' + row_id).val(month);
    }

    if (hasError) {
        errorNotification.innerHTML = `Note: One or more installment names match the restricted list. Please review the highlighted rows.<br>
        Caution: Proceeding will override the existing data with the new entries.`;
        errorNotification.style.display = 'block';
    }
});

</script>

<script>
$(document).ready(function(){
    var formSubmit = true;
    
    $('#students_list_modal').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#select_all').click(function(){
        if($(this).prop('checked')){
            $('.select_checkbox').prop('checked',true);
            $('#installment_submit_button').show();
        } else {
            $('.select_checkbox').prop('checked',false);
            $('#installment_submit_button').hide();
        }
        
        $('#previewBtn').click();
    });
    
    $(document).on('click', '.select_checkbox', function(){
        $('#previewBtn').click();
        var total_checkbox_count = $('.select_checkbox').length;
        var total_checked_checkbox_count = $('.select_checkbox:checkbox:checked').length;
        if(total_checkbox_count === total_checked_checkbox_count){
            $('#select_all').prop('checked',true);
            $('#installment_submit_button').show();
        } else {
            $('#select_all').prop('checked',false);
            $('#installment_submit_button').hide();
        }
        
        if(total_checked_checkbox_count === 0){
            $('#installment_submit_button').hide();
        } else {
            $('#installment_submit_button').show();
        }
    });
    
    $('#installment_submit_button').click(function(){
        formSubmit = true;
        $('.amountInstallment, .installmentName, .installmentId, .installmentMonth, .installmentDueDate, .installmentFine').removeAttr('name');
        var total_checked_checkbox_count = $('.select_checkbox:checkbox:checked').length;
        const checkedCheckboxes = $('.select_checkbox:checkbox:checked');
        
        const checkboxes = document.querySelectorAll('.select_checkbox');

        let checkedValues = [];
        
        checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
            const tr = checkbox.closest('tr');
            const installElements = tr.querySelectorAll('.install');
            Array.from(installElements).forEach(element => {
              checkedValues.push(element.value);
            });
        }
        });
        
        var installMentClass = $('#installment_class_type_id').val();
        
        var masterFeesArray = @json($masterFeesArray);
        var installmentArray = masterFeesArray[installMentClass];
          
        var matchedValues = checkedValues.filter(function(value) {
            return $.inArray(value, installmentArray) !== -1;
        });
        
        if(matchedValues != ""){
            formSubmit = false;
        }
        
        for(var l = 0; l < total_checked_checkbox_count; l++){
            var row_id = checkedCheckboxes.eq(l).val();
            
            $('#installment_amount_' + row_id).attr('name', 'installment_value[]');
            $('#installment_due_date_' + row_id).attr('name', 'installment_due_date[]');
            $('#installment_month_' + row_id).attr('name', 'installment_month[]');
            $('#installment_name_' + row_id).attr('name', 'installment_name[]');
            $('#installment_id_' + row_id).attr('name', 'installment_id[]');
            $('#installment_fine_' + row_id).attr('name', 'installment_fine[]');
        }
        
        var formData = $('#installment_form').serialize();
        
        if(formSubmit){
        $.ajax({
            headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            url: '/createFeesInstallmentClassWise',  // Replace with your actual route
            method: 'POST',
            data: JSON.stringify(formData),
            success: function(response) {
                toastr.success("Fees Master Created Successfully");
                window.location.reload();
                // if(response.entry == true){
                //     toastr.success("Fees Master Created Successfully");
                //     var classTypeId = response.class_type_id;
                //     getStudents(classTypeId, null);
                //     setTimeout(function() {
                //          $('#students_list_modal').modal('show');
                //     }, 800);
                // }else{
                //     toastr.success("Fees Master Created Successfully");
                // }
            },
            error: function(xhr) {
                console.log('An error occurred:', xhr);
            }
        });
    }else{
        toastr.error('Verify if there are any payments under the current fee head. If payments exist, modifications are not allowed.');
    }
            
    });
});


    $(document).ready(function(){
        $('#all_students').click(function(){
            if($(this).prop('checked')){
                $('.student_select_checkbox').prop('checked',true);
            }else{
                $('.student_select_checkbox').prop('checked',false);
            }  
        });
        
        $(document).on('click','.student_select_checkbox',function(){
            var total_checkbox_count = $('.student_select_checkbox').length;
            var total_checked_checkbox_count = $('.student_select_checkbox:checkbox:checked').length;
            if(total_checkbox_count == total_checked_checkbox_count){
                $('#all_students').prop('checked',true);
            }else{
                $('#all_students').prop('checked',false);
            }
        });
        
        $('#admission_type_id').change(function() {
            
            $('#bulk_class_type_id').trigger('change');
        });
        $('#bulk_class_type_id').change(function() {
           var class_type_id = $('#bulk_class_type_id').val();
           var bulk_admission_no = $('#bulk_admission_no').val();
           var admission_type_id = $('#admission_type_id').val();
            
            if(class_type_id == ""){
                toastr.error('plaase Select Class');
                $('#tbody_students_list').html("");
                $('#bulk_fees_master_ids').html("");
            }else{
                getStudents(class_type_id,bulk_admission_no,admission_type_id);       
                getMasterData(class_type_id);
            }
        });
        $('#bulk_admission_no').blur(function() {
           var class_type_id = $('#bulk_class_type_id').val();
           var bulk_admission_no = $('#bulk_admission_no').val();
             var admission_type_id = $('#admission_type_id').val();
            getStudents(class_type_id,bulk_admission_no,admission_type_id);   
        });
    });
</script>

<script>
    $(document).ready(function(){
        $('#assignFeesMultiple').on('submit', function(event){
           event.preventDefault();
           
           var checkedCount = $('.student_select_checkbox:checkbox:checked').length;
           
           if(checkedCount == 0){
               toastr.error("Please select students");
           }else{
               document.getElementById('assignFeesMultiple').submit();
           }
           
       }); 
    });

    function updateRefundFees(checkbox, id) {
    const hiddenInput = document.getElementById('refund_fees_value_' + id);
    if (checkbox.checked) {
        hiddenInput.value = 'yes';
    } else {
        hiddenInput.value = 'no';
    }
    saveRefundFees(id, hiddenInput.value); // Call the save function
}

// Move saveRefundFees outside $(document).ready() to make it accessible globally
function saveRefundFees(id, value) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: '/updateAssignedFees', // Replace with your actual route
        method: 'POST',
        data: {
            fees_assign_detail_id: id,
            value: value,
            field: 'fees_refund'
        },
        success: function(response) {
            toastr.success('Fees Updated Successfully');
        },
        error: function(xhr) {
            console.log('An error occurred:', xhr);
        }
    });
}
$(document).ready(function() {
    // Select All Checkbox Functionality
    $("#select_group").on("change", function() {
        $(".group_checkbox").prop("checked", $(this).prop("checked"));
    });

    // Individual Checkbox Functionality
    $(".group_checkbox").on("change", function() {
        if ($(".group_checkbox:checked").length === $(".group_checkbox").length) {
            $("#select_group").prop("checked", true);
        } else {
            $("#select_group").prop("checked", false);
            
        }
    });
});


</script>
@endsection