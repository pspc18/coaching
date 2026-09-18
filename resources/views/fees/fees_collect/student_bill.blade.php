 @php
$getFeesType = Helper::feesType();
$getPaymentMode = Helper::getPaymentMode();
$firstAmount = $data['FeesAssign']->total_amount;
$fees_assign_details = DB::table('fees_assign_details')
            ->where('session_id',$data['session_id'])
            ->where('branch_id',Session::get('branch_id'))
            ->where('fees_assign_id',$data['FeesAssign']->id)
            ->whereNull('deleted_at')->get();
//dd($feesDetails);         
$session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
@endphp             
        
<style>
    .centered{
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .bordered{
        border: 2px solid #002c54;
        height: 140px !important;
        width: 135px;
        padding: 5px;
        background: #e5ecff;
    }
    
     .stu_img{
        width:100%;
        height:100%;
     }
     
     .tabs_listing{
         list-style:none;
         display:flex;
         align-items:center;
         /*justify-content:center;*/
         padding-left:0px;
         margin-bottom:0px;
     }
     
     .tabs_listing li{
        border: 1px solid #002c54;
        color: black;
        padding: 10px 10px;
        /*border-radius: 4px;*/
        font-weight: 600;
        font-size: 14px;
        letter-spacing: 1.4px;
        cursor: pointer;
        margin-left:10px;
        transition:0.3s;
     }
     
     .tabs_listing li:first-child{
         margin-left:0px;
     }
     
     .tabs_listing li:hover{
         background: #002c54;
         color: white;
     }
     
     .trans_card{
         box-shadow:none;
         background:none;
     }
     
     #active_li{
         background: #002c54;
         color: white;
     }
     
     .not_found_div{
         height:300px;
         display:flex;
         align-items:center;
         justify-content:center;
     }
     
        .warning_icon {
          font-size: 60px;
          color: red;
          margin-bottom: 0px;
        }
     
     .assign_note{
        margin-bottom: 0px;
        font-weight:600;
        text-transform: capitalize;
     }
</style>        
 <div>
     
 <div class="row mb-1 mt-2">
     <div class="col-12 col-md-12 text-center p-2"> <h3 class="colored_header">{{ __('fees.Fees Pay') }}</h3></div>
     <div class="col-md-12">
        <div class="card trans_card">
            <div class="card-body p-0">
                <ul class="tabs_listing">
                    @if(count($data['sessions']) != 0)
                        @php
                            $sessions = $data['sessions'];
                        @endphp
                        @foreach($sessions as $item)
                            <li class="tab" id="{{ $data['session_id'] == $item->id ? 'active_li' : '' }}" data-id="{{ $item->id ?? '' }}" data-unique_system_id="{{ $data['stuData']['unique_system_id'] ?? '' }}">{{ $item->from_year ?? '' }} - 20{{ $item->to_year ?? '' }}</li>
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </div>
 </div>
 <div class="col-md-12 not_found_div" style="display:none;">
        <div class="text-center">
            <h1 class="warning_icon"><i class="fa fa-warning"></i></h1>
            <p class="assign_note">Please Assign the Fees for this Student !</p>
        </div>
 </div>
 </div>
 <div id="notfound">
    @php
        $fees_advance = App\Models\fees\FeesAdvance::where('unique_system_id', $data['stuData']['unique_system_id'])->first();
    @endphp
 <div class="row">
            @if(!empty($fees_advance) && $fees_advance->balance > 0)
                <div class="col-12 col-md-12">
                    <h4 class="blink2" style="font-size: 1.5rem; color: #dc3545;">Advance Fees: {{ $fees_advance->balance ?? '' }}</h4>
                    <input type="hidden" name="fees_advance_balance" id="fees_advance_balance" value="{{ $fees_advance->balance ?? '' }}">
                </div>
            @endif
            @if(!empty($fees_advance) && $fees_advance->balance > 0)
                    <div class="col-12 col-md-2 p-2">
                        <label class=" d-block mb-2">{{ __('Deposit from advance fees') }}</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input pointer" type="radio"  name="yesNoAdvance" id="noOption" value="no" checked>
                            <label class="form-check-label pointer" for="noOption">No</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input pointer"  type="radio" name="yesNoAdvance" id="yesOption" value="yes">
                            <label class="form-check-label pointer" for="yesOption">Yes</label>
                        </div>
                       
                    </div>
                @endif
                <div class="p-2 col-3 col-md-2">
                    <label class="text">{{ __('Aggregate Amount') }}</label>
                    <input type='text' value='' id='aggregate_amount' class='form-control' autocomplete="off"/>
                </div>   
                <div class="p-2 col-3 col-md-2"></div>
                <div class="col-md-6">
                    <small class="text-danger">*1. To collect fees by aggregate amount, enter the amount and press Enter.<br>
                    *2. Switching between headwise and aggregate fee collection requires reselecting the student. </small>
                </div>
                   
        </div>   
 <div class="col-md-12" >
 <div class="row">
         <div class="col-12 col-md-6 p-0 pr-1">
        <div class="card">
           
            @if(!empty($data['FeesCollect']->amount))
                @php
                    $remainingAmt = ($data['FeesAssign']->total_amount - $data['FeesAssign']->total_discount) - $data['FeesCollect']->amount;
                    $firstAmount = $remainingAmt;
                   
                @endphp
                
                @else
                
                @php
                    $remainingAmt = $data['FeesAssign']->total_amount - $data['FeesAssign']->total_discount;
                    $firstAmount = $remainingAmt;
                @endphp
            @endif

            <div class="card-body"> 
                <form id='myForm' method="post" enctype="multipart/form-data">
                    @csrf
                    <input  type="hidden" id="admission_id" name="admission_id" value="{{$data['stuData']['id']}}" />
                    <input  type="hidden" id="advance_payment" name="advance_payment" value="no" />
                    <input  type="hidden" id="session_id" name="session_id" value="{{ $data['session_id'] ?? Session::get('session_id') }}" />
                    <input  type="hidden" id="email" name="email" value="{{$data['stuData']['email']}}" />
                    <input  type="hidden" id="mobile" name="mobile" value="{{$data['stuData']['mobile']}}" />
                    <input  type="hidden" id="name" name="name" value="{{$data['stuData']['first_name']}}" />
                    <input  type="hidden" id="class_type_id1" name="class_type_id" value="{{$data['stuData']['class_type_id']}}" />
                     <input type="hidden" name="slip_no"  value="{{sprintf('%004s', $data['BillCounter']['counter']+1)  ?? ''}}" >
                <div id='add_head_row'>
                <div class=" text-right d-flex" style='justify-content: end; align-items: end;'>
                    <p class="mb-0" style="font-size: 18px;"> Receipt No.:<span class='text-danger'>{{sprintf('%004s', $data['BillCounter']['counter']+1)  ?? ''}}</span></p>
                </div>
                 <div class="row m-2" id='head_row'>
                 <div class="col-md-4 col-3"> {{ __('Select Head') }}*</div>      
                 <div class="col-md-3 col-3"> {{ __('common.Amount') }}* </div>      
                 <div class="col-md-3 col-3"> {{ __('Discount') }} </div>      
                 <div class="col-md-2 col-3"> {{ __('Fine') }} </div>      
                 @if(!empty($fees_assign_details))
                        @foreach($fees_assign_details as $key=>$fees)
                        @php
                                $feesGroup  = DB::table('fees_group')->whereNull('deleted_at')->where('id',$fees->fees_group_id)->first();
                                $result = DB::table('fees_detail')
                                        ->selectRaw('SUM(total_amount) AS paid, SUM(installment_fine) AS paid_fine')
                                        ->whereNull('deleted_at')
                                        ->where('admission_id', $fees->admission_id)
                                        ->where('fees_group_id', $feesGroup->id)
                                        ->whereIn('status',[0,1])
                                        ->first();

                                $paid = $result->paid;
                                $paid_fine = $result->paid_fine;
                         $paids  = $paid+$paid_fine;
                                @endphp 
                                @if($fees->fees_group_amount > $paids)  
                      <div class="col-md-4  col-3">
                            <input type="checkbox" class="selected_head pointer" id="checkbox_{{$key}}" name="selected_head[]" data-fees_assign_detail_id='{{$fees->id}}' value="{{$feesGroup->id}}">
                               <label for="checkbox_{{$key}}" class="pointer"> {{ $feesGroup->name ?? '' }}</label>
                        </div>
                        
                      
                        <div class="col-md-3 col-3">
                     
                            <input type="tel" class="form-control  amount_get aggregate_{{$feesGroup->id}}"  placeholder="{{ __('common.Amount') }}" id="amount_{{$fees->id}}" name="amount[]" onkeypress="javascript:return isNumber(event)"  required>

                        </div>
                        <div class="col-md-3 col-3">
                            <input type="tel" class="form-control discounts"  placeholder="{{ __('Discount') }}" id="discount_{{$fees->id}}" name="discount_amount[]" onkeypress="javascript:return isNumber(event)" >
                        </div>
                        <div class="col-md-2 col-3">
                            <input type="text" class="form-control fine_amount" id='fine_{{$fees->id}}' value='0'name="fine[]" onkeypress="javascript:return isNumber(event)"  required>
                        </div>
                        @endif
                        
                        @endforeach
                        @endif  
                    </div>
                </div>
                <hr>

                <div class="row m-2">
                    
                    <div class="col-md-3">
                        <label class="text-danger">{{ __('fees.Payment Mode') }}*</label>
                        <select class="form-control" id="payment_mode_id" name="payment_mode_id" required>
                            @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                                <option value="{{ $value->id }}">{{ $value->name ?? ''}}</option>
                                @endforeach
                            @endif  
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control" id='payment_status'>
                            <option value="0">Payment Received</option>
                            <option value="1">Payment Pending</option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label>Total</label>
                        <input type="text" class="form-control" readonly value="0" placeholder="Total Amount" id="total_amount" name="total_amount">
                    </div>
                    <div class="col-md-3">
                        <label>Total Fine</label>
                        <input type="text" class="form-control" readonly value="0" placeholder="Total Amount" id="total_fine" name="total_fine">
                    </div>
                        
                    <div class="col-md-3" id="transition_id_input" style="display:none;">
                        <label>Transaction Id</label>
                        <input type="text" class="form-control" placeholder="Transaction Id" id="transition_id" name="transition_id">
                    </div> 
                    
                    <div class="col-md-4" id="bank_name_input" style="display:none;">
                        <label>Bank Name</label>
                        <input type="text" class="form-control" placeholder="Bank Name" id="bank_name" name="bank_name">
                    </div>
                       <div class="col-md-4 " style="display: none;" id="cheque_number_id">
                        <label>Cheque Number</label>
                        <input type="text" name="cheque_number" id="cheque_number" value="" class="form-control">
                        </div>
                        <div class="col-md-4 " style="display: none;" id="cheque_date_id">
                        <label>Cheque Date</label>
                        <input type="date" name="cheque_date" id="cheque_date" value="" class="form-control">
                        </div>
                        <div class="col-md-4" style="display: none;" id="payment_receipt_id">						
                          <label>Payment Receipt/Cheque</label>
                          <input type="file"  accept=".gif, .jpg, .jpeg, .png, .pdf, .doc" class="form-control" name="payment_receipt" id="payment_receipt">
        				</div> 
                    <div class="col-md-4">
                        <label class="text-danger">{{ __('Payment Date') }}*</label>
                        <input type='date' class='form-control' name='date' value="{{date('Y-m-d')}}" />
                    </div>
                    <div class="col-md-4">
                        <label class="text-danger">{{ __('Offline Receipt No') }}*</label>
                        <input type='text' id='offline_receipt_no'class='form-control' name='offline_receipt_no'  value=""  placeholder="Offline Receipt No" required> 
                    </div>
                    <div class="col-md-8">
                        <label>{{ __('Remark') }}</label>
                        <input type='text' id='other_fee_remark'class='form-control' name='other_fee_remark'  value=""  placeholder="Remark"/> 
                    </div>
                    
                    <div class="col-md-3 mt-2">
                        <input type='hidden' id='discount_given'class='form-control' name='discount_given' value="" readonly />
                    </div>
                    
                   
                </div>
                <hr />
                    <div class="row m-2">
                    <div class="col-md-12 text-left">
                        <span ><h3>
                            Aggregate : ₹<span id="aggregate">0</span>
                        <br><span class='text-danger'>Discount  : ₹ <span id="d_given">0</span></span><br>
                        Grand Total : ₹<span id="g_total">0</span>
                        </h3></span>
                      
                    </div>
                    <div class="col-md-6 text-right">
                        <span><h3></h3></span>
                    </div>
                    
                     
                    </div>





                <div class="row p-2 "> 
                
                <div class="col-md-4 text-center">
                    @if(Helper::getPermisnByBranch()->whatsapp_srvc == 1)
                        <input type="checkbox" class="selected_whatsapp pointer" id="checkbox_whatsapp" name="checkbox_whatsapp"  value="1"><br>
                        <label for="checkbox_whatsapp" class="pointer">Whatsapp Mesaage</label>
                    @endif
                    </div> 
                   
                    <div class="col-md-8 text-center">
                        <button type="submit" id="collect_btn" class="btn btn-primary collect_btn_hide"><i class="fa fa-money"></i>{{ __('fees.Collect') }} </button>
                        <button type="submit" id="collect_btn"  name="print"  class="btn btn-primary collect_btn collect_btn_hide"><i class="fa fa-print"></i> {{ __('Collect & Print') }} </button>
                    </div>
                </div> 
                </form>
            </div>
            
        </div>
    </div>
    <div class="col-12 col-md-6 p-0">
        <div class="card">
            <div class="card-body padding_body">
                <p class="heading_text mb-0">Fees Structure :- </p>
               
                    <table id='fee_structure'class="table table-bordered padding_table" style="white-space:nowrap;">
                        <thead>
                            <tr>
                                <th>Fees Type</th>
                                <th>Amount</th>
                                <th>Discount</th>
                                <th>Paid</th>
                                <th class="bg-danger">Paid Fine</th>
                                <th>Pending</th>
                                <th>Due Date</th>
                                <th class="bg-danger">Fine</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @php
                                $total_amount = 0;
                                $total_discount = 0;
                                $total_paid = 0;
                                $total_paid_fine = 0;
                                $total_pending_fine = 0;
                                $total_pending = 0;
                                
                                $headArray = [];
                            @endphp
                            @foreach($fees_assign_details as $fees)
                            
                            
                            @php
                            
                                $feesGroup = DB::table('fees_group')->whereNull('deleted_at')->where('id',$fees->fees_group_id)->first();
                                    $discount = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('discount');
                                  
                            @endphp
                            <div class="col-md-12">
                                <tr id="group_{{$feesGroup->id ?? ''}}" class='group_group' >
                                    <td style="word-break: break-word !important;white-space: break-spaces;">{{ $feesGroup->name ?? '' }}</td>
                                    <td>₹ {{ $fees->fees_group_amount ?? '' }}</td>
                                    <td>₹ {{ ($fees->discount ?? 0  ) + $discount}}</td>
                                    <td>
                                        @if(!empty($feesGroup))
                                            @php
                                                $paid = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('total_amount');
                                                $paid_fine = DB::table('fees_detail')->whereNull('deleted_at')->where('admission_id',$fees->admission_id)->where('fees_group_id',$feesGroup->id)->whereIn('status',[0,1])->sum('installment_fine');
                                               @endphp                              
                                        @endif
                                        
                                        ₹ {{($paid-$discount ?? '')}} 
                                    </td>
                                    <td class="text-danger">₹ {{ number_format($paid_fine ?? 0, 2) }}</td>
                                    
                                    @php
                                        $pending_amount = (($fees->fees_group_amount) - ($fees->discount)) - ($paid);
                                    @endphp
                                    
                                    
                                    @php
                                    $headArray[] = ['pending_by_group_id'=>$feesGroup->id ?? '','pending'=>($pending_amount ?? 0)]
                                    @endphp
                                
                                <td id="pending_by_group_id_{{$fees->id ?? ''}}" class="{{ $pending_amount == 0 ? 'bg-success ' : '' }}"
                                
                                data-pending_amount='{{$pending_amount ?? "0"}}'
                                data-fine="{{ ($pending_amount != 0 && isset($fees->installment_due_date) && $fees->installment_due_date < date('Y-m-d')) ? $fees->installment_fine : 0 }}"
                                
                                >₹ {{$pending_amount ?? "0"}}</td>
                                        
                                        
                                        <td class="{{ ($pending_amount != 0 && isset($fees->installment_due_date) && $fees->installment_due_date < date('Y-m-d')) ? 'bg-danger' : '' }}">
                                            <input type="date" class="{{ $pending_amount == 0 ? 'fees_assign_detail' : '' }}" name="installment_due_date"  data-detail_id="{{ $fees->id ?? '' }}" data-old_value="{{ $fees->installment_due_date ?? '' }}" {{ $pending_amount == 0 ? 'disabled' : '' }} value="{{ $fees->installment_due_date ?? '' }}">
                                        </td>
                                        
                                        <td class="text-danger">₹  {{ ($pending_amount != 0 && isset($fees->installment_due_date) && $fees->installment_due_date < date('Y-m-d')) ? ($pending_amount * $fees->installment_fine ?? 0)/100 : 0 }}</td>
                                    @php
                                        $total_amount += $fees->fees_group_amount ?? 0;
                                        $total_discount += ($fees->discount ?? 0)+($discount);
                                        $total_paid += ($paid ?? 0)-($discount);
                                        $total_paid_fine += $paid_fine ?? 0;
                                        $total_pending_fine += ($pending_amount != 0 && isset($fees->installment_due_date) && $fees->installment_due_date < date('Y-m-d')) ? ($pending_amount * $fees->installment_fine ?? 0)/100 : 0;
                                    @endphp
                                </tr>
                            </div>
                            
                          
                            @endforeach
                             
                        </tbody>
                        
                        <tfoot class="tfoot_tr">
                            <tr>
                                <td>Total</td>
                                <td>₹ {{ $total_amount ?? '' }}</td>
                                <td>₹ {{ $total_discount ?? '' }}</td>
                                <td>₹ {{ $total_paid ?? '' }}</td>
                                <td class="bg-danger">₹ {{ number_format($total_paid_fine ?? 0, 2) }}</td>
                                <td id='validate_pending' data-pending="{{ (($total_amount ?? '0') - ($total_discount ?? '0')) - ($total_paid ?? '0') }}">₹ {{ (($total_amount ?? '0') - ($total_discount ?? '0')) - ($total_paid ?? '0') }}</td>
                         <td></td>
                         <td class="bg-danger">₹ {{ $total_pending_fine ?? '' }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>        
</div>

<div class="row">
 <div class="col-12 col-md-12"> 
<div class="card mt-3">
    <div class="card-body">
        <table class="table table-bordered small_td p-3 " id="trColor">
                        <thead class='bg-primary'>
                            <tr>
                                <!--<th>{{ __('common.Date') }}</th>-->
                                <th>{{ __('Head Name') }}</th>
                                <th>{{ __('fees.Receipt No.') }}</th>
                                <th>{{ __('Offline Receipt No.') }}</th>
                                <th>Payment Date</th>
                                <th>{{ __('common.Amount') }}</th>
                                <th>{{ __('Discount') }}</th>
                                <th>Fine</th>
                                <th>{{ __('fees.Payment Mode') }}</th>
                                <th>Bank Name</th>
                                <th>Transaction Id</th>
                                <th>Payment Status</th>
                                <th>{{ __('common.Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                         @if(!empty($data['FeesDetailsInvoices']))
        @foreach($data['FeesDetailsInvoices'] as $val)
        
        @php
        $fee_detail_id = explode(',',$val->fees_details_id);
       $head_names = '';
       if(!empty($fee_detail_id))
       {
       
        $head_names = DB::table('fees_detail')
    ->leftJoin('fees_group', 'fees_detail.fees_group_id', '=', 'fees_group.id')
    ->whereIn('fees_detail.id', $fee_detail_id)
    ->whereNull('fees_detail.deleted_at')
    ->pluck('fees_group.name') 
    ->implode(',');
        $head_total = DB::table('fees_detail')
    ->whereIn('fees_detail.id', $fee_detail_id)
    ->whereNull('fees_detail.deleted_at')->sum('paid_amount');
        $discount = DB::table('fees_detail')
    ->whereIn('fees_detail.id', $fee_detail_id)
    ->whereNull('fees_detail.deleted_at')->sum('discount');
        $head_fine_total = DB::table('fees_detail')
    ->whereIn('fees_detail.id', $fee_detail_id)
    ->whereNull('fees_detail.deleted_at')->sum('installment_fine');
   
       }
      
        @endphp
            <tr>
                <td>{{$head_names ?? ''}}</td>
                <td>
                    <form target='_blank'action="{{ url('printFeesInvoice') }}" method="post">
                    @csrf
                            
                    <input type='hidden' name='fees_details_invoice_id' value='{{$val->id}}' />
                    <button class='btn btn-xs btn-primary'>
                        {{ $val->invoice_no ?? '' }}  
                    </button>
                        
                    </form>
                </td>
                <td>{{ $val->offline_receipt_no ?? '' }}</td>
                <td>{{ !empty($val->payment_date) ? date('d-m-Y', strtotime($val->payment_date)) : '' }}</td>
                <td>{{$head_total ?? 0}}</td>
                <td>{{$discount ?? 0}}</td>
                <td>{{$head_fine_total ?? 0}}</td>
                <td>
                   
                   
                     @if(!empty($getPaymentMode))
                                @foreach($getPaymentMode as $value)
                              @if($value->id == $val->payment_mode)
                    {{ $value->name ?? '' }}
                    
                    @endif
                                @endforeach
                            @endif  
                    
                </td>
                <td>{{$val->bank_name ?? '-'}}</td>
                <td>{{$val->transaction_id ?? '-'}}</td>
                <td>
                    @if($val->status == 0)
                        <span style="color: green;">Received</span>
                    @elseif($val->status == 1)
                        <span style="color: #fd7e14;">Pending</span>
                    @else
                       <span style="color: red;">Cancelled</span>
                    @endif
                </td>

                <td>
                     @if(Session::get('role_id') == 1)
                  @if($val->status != 2)             
                     <button class="btn btn-success btn-xs whatsapp_reciept" data-session_id="{{ $val->session_id ?? '' }}"   data-admission_id="{{ $val->admission_id ?? ''}}"  data-fees_details_invoice_id="{{ $val->id }}"  data-toggle="modal"  data-target="#whatsapp_modal"> <i class="fa fa-whatsapp"></i></button>
                    <button class="btn btn-danger btn-xs revert_fees" data-session_id="{{ $val->session_id ?? '' }}"  data-admission_id="{{ $val->admission_id ?? ''}}" data-id='{{ $val->id }}' data-toggle="modal" data-target="#revert_modal"><i class="fa fa-undo"></i></button>@endif
                  @endif
                </td>
            </tr>
        @endforeach 
        
        @endif
                            
                        </tbody>
                </table>
    </div>
</div>
</div>
</div>
</div>
</div>
<div class="modal fade" id="revert_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Revert Fees Confirmation</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        
        <form id="revert_fees_form" method="post">
      	    @csrf
            <div class="modal-body">
                <input type="hidden" id="admissionId" name="admission_id">
                <input type="hidden" id="fees_invoice_id" name="fees_invoice_id">
                <input type="hidden" id="sessionID_" name="session_id">
                <h5>{{ __('fees.Are you sure you want to revert fees ? This action is irreversible.') }}</h5>
            </div>
        
            <div class="modal-footer">
                <button type="button" id="hide_modal" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger">Return</button>
            </div>
        </form>
      </div>
    </div>
  </div>


<!-- Loading screen modal -->
<div class="modal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="w-100">
      <div class="modal-body text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only text-white">Collecting fees please wait...</span>
        </div>
        <p class="mt-2 text-white">Collecting fees please wait...</p>
      </div>
    </div>
  </div>
</div>

 <!-- Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Change</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Do you really want to change the due date?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelChange" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmChange">Confirm</button>
                </div>
            </div>
        </div>
    </div>


<div class="modal fade" id="whatsapp_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
        
            <div class="modal-header">
                <h4 class="modal-title">Send Receipt On Whatsapp</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <form id="Send_whatsapp_reciept" method="POST" action="{{ url('sendReceiptOnWhatsapp') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="hidden" name="fees_details_invoice_id" id="whatsapp_fees_details_invoice_id">
                    </div>
                    <h5>Are you sure you want to send the receipt on WhatsApp ?</h5>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-submit" >Send</button>
                </div>
            </form>
        </div>
    </div>
</div>




 <style>
    /* Centering the loader */
    .loader {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      z-index: 1050; /* Make sure this is higher than the modal backdrop */
    }
    
  </style>




         <script src="{{URL::asset('public/assets/school/js/form/form_save.js')}}"></script>
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
         


<!-- ================= Receipt Error Modal ================= -->

<div id="offlineReceiptErrorModal" class="offline-receipt-modal">

    <div class="offline-receipt-modal-box">

        <div class="offline-receipt-modal-header">

            <h5>Receipt Number Error</h5>

            <span id="offlineReceiptErrorClose"
                  class="offline-receipt-modal-close">
                &times;
            </span>

        </div>

        <div class="offline-receipt-modal-body">
            <div id="offlineReceiptErrorMessage" style="margin-bottom: 15px; font-weight: bold;">
            </div>

            <!-- Student Details Section -->
            <div id="offlineReceiptStudentDetails" style="display: none; color: #333; font-size: 15px;">
                <p><strong>Admission No. :</strong> <span id="err_admission_no"></span></p>
                <p><strong>Student Name :</strong> <span id="err_student_name"></span></p>
                <p><strong>Father Name :</strong> <span id="err_father_name"></span></p>
                <p><strong>Mobile :</strong> <span id="err_mobile"></span></p>
                <p><strong>Class :</strong> <span id="err_class"></span></p>
                <p><strong>Offline Receipt No. :</strong> <span id="err_receipt_no"></span></p>
            </div>
        </div>

        <div class="offline-receipt-modal-footer">

            <button type="button"
                    id="offlineReceiptErrorCloseBtn"
                    class="btn btn-secondary">
                Close
            </button>

        </div>

    </div>

</div>


<style>

.offline-receipt-modal {
    display: none;

    position: fixed;
    z-index: 99999999;

    left: 0;
    top: 0;

    width: 100%;
    height: 100%;

    background: rgba(0, 0, 0, 0.60);

    align-items: center;
    justify-content: center;
}

.offline-receipt-modal-box {
    width: 500px;
    max-width: 90%;

    background: #ffffff;

    border-radius: 6px;

    box-shadow: 0 10px 40px rgba(0,0,0,0.5);

    overflow: hidden;
}

.offline-receipt-modal-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 14px 20px;

    background: #dc3545;

    color: #ffffff;
}

.offline-receipt-modal-header h5 {
    margin: 0;
    font-size: 18px;
}

.offline-receipt-modal-close {

    font-size: 30px;

    line-height: 20px;

    cursor: pointer;

    color: #ffffff;
}

.offline-receipt-modal-body {

    padding: 25px 20px;

    font-size: 16px;

    color: #dc3545;

    font-weight: 500;
}

.offline-receipt-modal-footer {

    padding: 12px 20px;

    border-top: 1px solid #ddd;

    text-align: right;
}

</style>


<script>

$(document).ready(function () {

    /*
     * Existing #myForm ko hi use kar rahe hain.
     * Koi second AJAX request create nahi kar rahe.
     */

    $('#myForm').on('submit.receiptError', function () {

        /*
         * Existing AJAX ko interfere nahi karna.
         * Sirf uske 422 response ko catch karna hai.
         */

    });


    /*
     * Kisi bhi AJAX request me student_pay_submit ka
     * 422 response aaye to modal show karo.
     */

    $(document).ajaxError(function (event, xhr, settings) {
        if (
            settings.url &&
            (
                settings.url.indexOf('student_pay_submit') !== -1 ||
                settings.url.indexOf('studentPaySubmit') !== -1
            )
        ) {
            if (xhr.status === 422) {
                let message = 'Something went wrong. Please try again.';

                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                $('#offlineReceiptErrorMessage').text(message);

                // Agar backend se student data aaya hai, toh usko show karwa do
                if (xhr.responseJSON && xhr.responseJSON.data) {
                    let sData = xhr.responseJSON.data;
                    $('#err_admission_no').text(sData.admission_no);
                    $('#err_student_name').text(sData.student_name);
                    $('#err_father_name').text(sData.father_name);
                    $('#err_mobile').text(sData.mobile);
                    $('#err_class').text(sData.class_name);
                    $('#err_receipt_no').text(sData.offline_receipt_no);
                    
                    $('#offlineReceiptStudentDetails').show();
                } else {
                    // Agar data nahi aaya (kisi aur validation issue ki wajah se), toh details chhupa do
                    $('#offlineReceiptStudentDetails').hide();
                }

                $('#offlineReceiptErrorModal').css({
                    'display': 'flex'
                });
            }
        }
    });
    /*
     * Close - X button
     */

    $(document).on(
        'click',
        '#offlineReceiptErrorClose',
        function () {

            $('#offlineReceiptErrorModal').css(
                'display',
                'none'
            );

        }
    );


    /*
     * Close button
     */

    $(document).on(
        'click',
        '#offlineReceiptErrorCloseBtn',
        function () {

            $('#offlineReceiptErrorModal').css(
                'display',
                'none'
            );

        }
    );

});

</script>

<script>

   
$(document).on('click', '.whatsapp_reciept', function() {

    $('#whatsapp_fees_details_invoice_id').val($(this).data('fees_details_invoice_id'));
});
  $(document).ready(function() {
            var currentTd, fees_assign_detail_id, value, old_value, field;

            $('#fee_structure').on('change', '[name="installment_due_date"]', function() {
                currentTd = $(this);
                fees_assign_detail_id = currentTd.data('detail_id');
                value = currentTd.val();
                old_value = currentTd.data('old_value');
                field = currentTd.attr('name');



    
    
    
    function compareValues(value1, value2) {
        return String(value1 || '') !== String(value2 || '');
    }
                if (compareValues(value,old_value)) {
                    $('#confirmationModal').modal('show');
                }
            });

            $('#confirmChange').on('click', function() {
                $('#confirmationModal').modal('hide');
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: "{{ url('updateAssignedFees') }}",
                    method: 'POST',
                    data: {
                        fees_assign_detail_id: fees_assign_detail_id,
                        value: value,
                        field: field,
                        session_id: $('#session_id').val()
                    },
                    success: function(response) {
                        toastr.success('Due date has been changed successfully.');
                        currentTd.data('old_value', value);
                        currentTd.val(value);
                        
                        $('#active_li').click();
                    },
                    error: function(xhr) {
                        currentTd.val(old_value);
                        toastr.error(xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Due date could not be changed.');
                    }
                });
            });

            $('#cancelChange').on('click', function() {
                currentTd.val(old_value);
                $('#confirmationModal').modal('hide');
            });
        });
</script>
            
<script>

$(document).ready(function() {
    
  




 
    

  
  

    
    $( "#submit_form" ).on( "submit", function( event ) {
 amount = parseFloat($('#amount').val());
 
 if(amount <=0)
 {
     toastr.error('Amount must be greater than zero');
  event.preventDefault();
 }
});

});

</script>
<style>
    
    .card{
        margin-bottom:0px;
        height:100%;
    }
    
    .colored_header{
        color:red;
    }
    
    .border_box{
        border: 1px solid black;
        padding:10px;
        margin-top: 10px;
    }
    
    .heading_text{
        font-size:18px;
        font-weight:600;
        color:#dc3545;
    }
    
    .padding_body{
        padding:4px;
    }
    
    .padding_table thead tr{
        background:#1f2d3d;
        color:white;
    }

    
    .padding_table th, .padding_table td{
        padding:5px;
        font-size:14px;
    }
    
    .absolute_row{
        position: absolute;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        bottom: 0;
    }
    .rotated-text {
       transform: rotate(-90deg); /* Rotate text 90 degrees clockwise */
        white-space: nowrap; /* Prevent text from wrapping */
     
     
}

    .tfoot_tr{
        background: skyblue;
        font-weight: 600;
    }
    .receipts {
       /* margin-top:-10px;*/
       /*transform: rotate(-5deg); */
       background-color:#49bf45;
       color:#fff;
       
       /* Rotate text 90 degrees clockwise */
        
     
     
}
</style>

<script>

$(document).ready(function () {
    $('#aggregate_amount').on('keyup', function (event) {
        let aggregateAmount = parseFloat($(this).val()) || 0;

        const yesNoAdvance = document.querySelector('input[name="yesNoAdvance"]:checked');
        if (yesNoAdvance && yesNoAdvance.value === "yes") {
            let fees_advance_balance = parseFloat($("#fees_advance_balance").val()) || 0;
            if (aggregateAmount > fees_advance_balance) {
                toastr.error('The advance fee for students is Rs ' + fees_advance_balance);
                $('#aggregate_amount').val(0);
                return;
            }
        }

        $('.discount').val('');
        $('.fine_amount').val('');

        if (event.key === "Enter") {
            $('.group_group').removeClass('bg-warning');

            let total_pending = parseFloat($("#validate_pending").data('pending')) || 0;
            if (aggregateAmount > total_pending) {
                toastr.error('Amount cannot be greater than total pending.');
                $('#aggregate_amount').val(0);
                return;
            }

            let leftAmount = aggregateAmount;
            let filterArray = [];

            let data = @json($headArray);
            
            data.forEach(item => {
                if (leftAmount > 0 && item.pending > 0) {
                    filterArray.push(item);
                    leftAmount -= item.pending;
                }
            });
            let count = 1; // Initialize count properly
            if (filterArray.length > 0) {
          
                        // Uncheck all checkboxes before starting the loop
                            $('.selected_head').prop('checked', false).trigger('change');

                            filterArray.forEach((item, index) => {
                                count++;
                                let amount = parseFloat(item.pending) || 0;

                                // Find the checkbox based on value
                                let checkBox = $('.selected_head').filter('[value="' + item.pending_by_group_id + '"]');

                                if (count === 1) {
                                    checkBox.prop('checked', true).trigger('change');
                                    $("#group_" + item.pending_by_group_id).addClass('bg-warning');
                                } else {

                                    checkBox.prop('checked', false); // Explicitly uncheck previous ones

                                    // Select only the last matching checkbox dynamically
                                    $('.selected_head')
                                        .filter('[value="' + item.pending_by_group_id + '"]')
                                        .prop('checked', true)
                                        .trigger('change');
                                        $("#group_" + item.pending_by_group_id).addClass('bg-warning');

                                }
                                // Assign the remaining amount to the last item
                                if (index === filterArray.length - 1) {
                                    //alert('#amount_' + item.pending_by_group_id);
                                        $('.aggregate_' + item.pending_by_group_id).val(amount + leftAmount);
                                    }
                            });


            }
            updateTotals(); // Update totals whenever selection changes
        }
    });
});























$(document).ready(function () {

   

    $(".selected_head").on("change", function () {
        let fees_assign_detail_id = $(this).data("fees_assign_detail_id");

        if ($(this).is(":checked")) {
            $("#amount_" + fees_assign_detail_id).prop("disabled", false);
            $("#discount_" + fees_assign_detail_id).prop("disabled", false);
            $("#fine_" + fees_assign_detail_id).prop("disabled", false);

            var pending_amount = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-pending_amount'));
            var fine = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-fine'));

            if (pending_amount > 0) { 
                $('#amount_' + fees_assign_detail_id).val(pending_amount);
                $('#fine_' + fees_assign_detail_id).val((pending_amount * fine) / 100);
                $('#amount_' + fees_assign_detail_id).attr('data-fine', fine);
            }
        } else {
            $("#amount_" + fees_assign_detail_id).prop("disabled", true).val('');
            $("#discount_" + fees_assign_detail_id).prop("disabled", true).val('');
            $("#fine_" + fees_assign_detail_id).prop("disabled", true).val('0');
        }
        updateTotals(); // Update totals whenever selection changes

    });

    // Prevent negative or excessive amount
    $(".amount_get").on("input", function () {
        let fees_assign_detail_id = $(this).attr("id").split("_")[1]; // Extract ID
        let pendingAmount  = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-pending_amount'));
        let finePercentage = Number($('#amount_' + fees_assign_detail_id).attr('data-fine'));
        let discountValue = Number($('#discount_' + fees_assign_detail_id).val());
        let currentValue = Number($(this).val());

        if (currentValue < 0 || isNaN(currentValue)) {
            $(this).val(0);
        } 

        let maxAllowedAmount = pendingAmount - discountValue; // Max Amount after Discount
        if (currentValue > maxAllowedAmount) {
            $(this).val(maxAllowedAmount);
            toastr.error("Amount can't be greater than pending amount" );
        }
        let updatedFine = (Number($(this).val()) * finePercentage) / 100;
        $("#fine_" + fees_assign_detail_id).val(updatedFine.toFixed(2));


        updateTotals(); // Update totals whenever selection changes

    });

    // Adjust amount based on discount
    $(".discounts").on("input", function () {
        let fees_assign_detail_id = $(this).attr("id").split("_")[1]; // Extract ID
        let originalAmount = Number($('#pending_by_group_id_' + fees_assign_detail_id).attr('data-pending_amount'));
        let discountValue = Number($(this).val());

        if (discountValue < 0 || isNaN(discountValue)) {
            $(this).val(0);
        }

        if (discountValue > originalAmount) {
            $(this).val(originalAmount);
        }
        let amount_ = $('#amount_' + fees_assign_detail_id).val();

        let newAmount = originalAmount - discountValue;
        $("#amount_" + fees_assign_detail_id).val(newAmount < 0 ? 0 : newAmount);
        updateTotals(); // Update totals whenever selection changes

    });

    // Fine can also be entered manually. Keep the summary/invoice total in sync
    // while the user types, instead of only recalculating on head/amount changes.
    $(document).on("input", ".fine_amount", function () {
        let value = Number($(this).val());
        if (value < 0 || isNaN(value)) {
            $(this).val(0);
        }
        updateTotals();
    });
    $(".selected_head").each(function () {
        let key = $(this).data("fees_assign_detail_id");
        if (!$(this).is(":checked")) {
            $("#amount_" + key).prop("disabled", true);
            $("#discount_" + key).prop("disabled", true);
            $("#fine_" + key).prop("disabled", true);
        }
    });
});


                </script>
            
           
<script>


$('#payment_mode_id').on('change', function() {
    
    var payment_mode_id = $(this).val();
     $("#other_fee_remark").val('');
     $("#payment_status").val(0);
               $("#cheque_number_id").hide();
              $("#cheque_date_id").hide();
              $("#payment_receipt_id").hide();
              $("#payment_receipt_view_id").hide();
          if (payment_mode_id == 1) {
            $('#transition_id_input').hide();
            $('#bank_name_input').hide();
            $('#bank_name').val('');
            $('#transition_id').val('');
} else if (payment_mode_id == 2) {
    $("#cheque_number_id").show();
    $("#cheque_date_id").show();
    $("#payment_receipt_id").show();
    $("#bank_name_input").show();
    
    $("#payment_status").val(1);
    $("#other_fee_remark").val('This cheque is pending.realisation');
} else {
    $('#transition_id_input').show();
    $('#bank_name_input').show();
    $('#payment_receipt_id').show();
    $('#bank_name').val('');
    $('#transition_id').val('');
}



        
});
</script>


<script>
$(document).ready(function(){
    var BASEURL = "{{ url('/') }}";
    
    $('#revert_fees_form').submit(function(event){
        event.preventDefault();
        var formData = $('#revert_fees_form').serialize(); 
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: BASEURL + '/collect_fees_delete',
            data: formData,
            success: function(data) {
                if(data.status == 'success'){
                    $('#hide_modal').click();
                    toastr.success('Fee Revert Successfully');
                    setTimeout(function() {
                         showData(data.unique_system_id,data.session_id);
                    }, 800);
                }
            }
        });
    });    
    
    $(document).on('click','.collect_btn',function(){
      $('.collect_btn').val('print'); 
    });
    
  $("#myForm").submit(function(event){
       
    event.preventDefault();
$('#loadingModal').modal('show');
$('.collect_btn_hide').hide();
 var buttonValue = $('.collect_btn').val(); 
    var formData = new FormData($('#myForm')[0]); // Get all form data, including files
    
   
   

   $.ajax({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: BASEURL + '/student_pay_submit',
        data: formData,
        processData: false,  // Important: Don't process the data
        contentType: false,  // Important: Set content type to false, letting jQuery assign it
        success: function(data) {
            if(data.status == 'success')
            {
              
                $('#loadingModal').modal('hide');
                $('#collect_btn').show();
                $('.collect_btn').val(''); 
                  toastr.success('Fee Collected Successfully');
                  showData(data.unique_system_id,data.session_id);
                  if(buttonValue == 'print'){
                  var fees_details_invoice_id = data.fees_details_invoice_id;
                                $.ajax({
                                    headers: {
                                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                                    },
                                    type: 'post',
                                    url: BASEURL + '/printFeesInvoice',
                                    data: { fees_details_invoice_id: fees_details_invoice_id },
                                   success: function(response) {
                                        var printWindow = window.open('', '_blank');
                                        printWindow.document.open();
                                        printWindow.document.write('<html><head><title>Print Invoice</title>');
                                        printWindow.document.write('<style>body{font-size:12px;} table{font-size:12px;}</style>'); // Optional: inline styles for printing
                                        printWindow.document.write('</head><body>');
                                        printWindow.document.write(response);
                                        printWindow.document.write('</body></html>');
                                        printWindow.document.close();
                                        printWindow.onload = function() {
                                            printWindow.focus(); // Ensure the new window is focused
                                            printWindow.print(); // Print the content
                                            printWindow.close(); // Close the new window after printing
                                        };
                                    }

                                })
                  }
            }
            else
            {
                $('#collect_btn').show();
                $('#loadingModal').modal('hide');
                $('.collect_btn').val(''); 
                toastr.error('Something Went Wrong');
            }
        },
        error: function(xhr) {
            $('#loadingModal').modal('hide');
            $('.collect_btn_hide').show();
            $('.collect_btn').val('');
            let message = xhr.responseJSON && xhr.responseJSON.message
                ? xhr.responseJSON.message
                : 'Fee could not be saved. Please try again.';
            toastr.error(message);
        }
    });
  });
});
</script>

<script>
    $(document).ready(function(){
        var BASEURL = "{{ url('/') }}";
        $('.tab').click(function(){
            $('.tab').removeAttr('id');
            $(this).attr('id','active_li');
            var session_id = $(this).data('id');
            var unique_system_id = $(this).data('unique_system_id');
            
            if(session_id != ""){
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'post',
                    url: BASEURL + '/student_fees_onclick',
                    data: {unique_system_id : unique_system_id,session_id : session_id},
                    success: function(data) {
                        if(data != 0){
                            $('#student_fees_detail').html(data);
                        }else{
                            $('#notfound').hide();
                            $('.not_found_div').show();
                        }
                    }
                });
            }
        }); 

        $('input[name="yesNoAdvance"]').click(function(){
                if (this.value === "yes") {
                    $('#payment_mode_id').val(9);
                    $('#aggregate_amount').val('');
                    $('#amount_0').val(0);
                    $('#advance_payment').val('yes');
                    } else if (this.value === "no") {
                        $('#payment_mode_id').val(1);
                        $('#advance_payment').val('no');

                    }
                });


    });

    $(document).on('click','.revert_fees',function(){
       var fees_invoice_id = $(this).data('id'); 
     
       var admission_id = $(this).data('admission_id'); 
       var sessionID_ = $(this).data('session_id'); 
       
       $('#fees_invoice_id').val(fees_invoice_id);
       $('#admissionId').val(admission_id);
       $('#sessionID_').val(sessionID_);
    });

    function updateTotals() {
        let totalAmount = 0;
        let totalDiscount = 0;
        let totalFine = 0;

        $(".selected_head:checked").each(function () {
            let fees_assign_detail_id = $(this).data("fees_assign_detail_id");
            let amount = Number($("#amount_" + fees_assign_detail_id).val()) || 0;
            let discount = Number($("#discount_" + fees_assign_detail_id).val()) || 0;
            let fine = Number($("#fine_" + fees_assign_detail_id).val()) || 0;

            totalAmount += amount;
            totalDiscount += discount;
            totalFine += fine;
        });
        $("#total_amount").val(totalAmount.toFixed(2));
        $("#total_fine").val(totalFine.toFixed(2));
        $("#discount_given").val(totalDiscount.toFixed(2));

        // Update display values
        $("#aggregate").text((totalAmount+totalDiscount).toFixed(2));
        $("#d_given").text(totalDiscount.toFixed(2));
        $("#g_total").text((totalAmount ).toFixed(2)); // Grand Total Calculation
    }
</script>
