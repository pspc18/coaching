@php
$role = Helper::roleType();
$getPermission = Helper::getPermission();
$actionPermission = Helper::actionPermission();
$getStudentSession = DB::table('sessions')->whereIn('id', $sessionIds)->get();
@endphp
@extends('layout.app')
@section('content')
<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card card-outline card-orange">
                        <div class="card-header bg-primary">
                            <h3 class="card-title"><i class="fa fa-code-fork"></i> &nbsp;{{ __('Fees History') }}</h3>
                             <div class="card-tools">
                                 @if(Session::get('role_id') == 3)
                                <a href="{{url('dashboard')}}" class="btn btn-primary  btn-xs "><i class="fa fa-arrow-left"></i> <span class="" >{{ __('Back') }} </span></a>
                                 @endif
                            </div>
                        </div>
                        
                            <section class="content">
                                <div class="container-fluid">
                                    
                                    <div class="row">
                                   <div class="col-md-12">
                                        <div class="listing_tab tabs-custom">
                                            <ul class="nav-tabs">
                                                <li class="get_data active" data-title="fees">
                                                    <a href="#fees">Fees</a>
                                                </li>
                                                <li class="get_data" data-title="payment_history">
                                                    <a href="#payment_history">Payment History</a>
                                                </li>
                                                <li class="get_data" data-title="online_pay">
                                                    <a href="#online_pay">Online Pay</a>
                                                </li>
                                            </ul>
                                        </div>
                                
                                        <div class="tabData w-100" id="tab_fees" style="display:none;">
                                        <form action="{{ url('fees_history') }}" method="post">
                                        @csrf
                                        <div class="row pt-2">
                                            @if(!empty($getStudentSession))
                                            @foreach($getStudentSession as $stuSession)
                                                <div class="col-md-2 col-4">
                                                    <button type="submit" name="session_id" value="{{ $stuSession->id ?? '' }}" class="btn @if($stuSession->id == $ActiveSession_id) btn-primary @else btn-light @endif">{{ $stuSession->from_year ?? '' }} - {{ $stuSession->to_year ?? '' }}</button>
                                                </div>
                                            @endforeach
                                            @endif
                                        </div>
                                    </form>
                                            <div class="col-md-12">
                                                <div class="card-body">
                                                      <table class="table">
                                                        <thead>
                                                            <tr class="sky_tr">
                                                                <th>#</th>
                                                                <th>Fees Type</th>
                                                                <th>Due Date</th>
                                                                <th>Status</th>
                                                                <th>Amount</th>
                                                                <th>Discount</th>
                                                                <th>Fine</th>
                                                                <th>Paid</th>
                                                                <th style="text-align: right;">Balance</th>
                                                            </tr>
                                                        </thead>
    
                                                        <tbody>
                                                            @if(!empty($getFees))
                                                            @php
                                                                $i = 1;
                                                                $grand_total = 0;
                                                                 $Paids = 0;
                                                                 $Discount  = 0;
                                                                 $Fine  = 0;
                                                                 $balances = 0;
                                                                 $fine_amt = 0;
                                                            @endphp
                                                                @foreach($getFees as $item)
                                                                   <tr>
                                                                       @php
                                                                      
                                                                       $pad = App\Models\FeesDetail::where('fees_type',0)->where('status',0)
                                                                       ->where('admission_id',Session::get('id'))->where('fees_group_id',$item->fees_group_id)
                                                                       ->sum('total_amount');
                                                                       $discount = App\Models\FeesDetail::select('fees_detail.*')->where('fees_type',0)->where('admission_id',Session::get('id'))->where('fees_group_id',$item->fees_group_id)
                                                                       ->sum('discount');
                                                                      
                                                                       $balance  = $item->fees_group_amount-$pad;
                                                                       @endphp
                                                                        <td>{{ $i++ }}</td>
                                                                        <td>{{ $item->group_name ?? '' }}</td>
                                                                        <td>
                                                                            @if(!empty($item->installment_due_date)) 
                                                                                {{ date('d-M-Y', strtotime($item->installment_due_date)) }}
                                                                                @php
                                                                                if($item->installment_due_date > date('Y-m-d')){
                                                                                $fine_amt = $balance/100*$item->installment_fine;
                                                                                }
                                                                                @endphp
                                                                            @endif
                                                                            
                                                                        </td>
                                                                        <td>@if($item->fees_group_amount > $pad )<span class="label1 label-danger-custom ">Unpaid</span> @else <span class="label1 label-success-custom ">Total Paid</span>  @endif</td>
                                                                        <td>{{ $item->fees_group_amount ?? '0' }}</td>
                                                                        <td>{{ $item->discount ?? '0' }}</td>
                                                                        <td>{{ $fine_amt ?? '0' }}</td>
                                                                        <td>{{$pad ?? '0'}} </td>
                                                                         <td style="text-align: right;">{{$balance ?? ''}}</td>
                                                                         
                                                                         @php
                                                                         $grand_total += $item->fees_group_amount;
                                                                         $Paids += $pad;
                                                                         $Discount  += $item->discount;
                                                                         $Fine  += $fine_amt;
                                                                         $balances += $balance;
                                                                         @endphp
                                                                   </tr>
                                                                  
                                                                @endforeach
                                                                <tr>
                                    								<div class="row">
                                                            <div class="col-6">
                                            
                                                              <div class="table-responsive">
                                                                  
                                                                <table class="table">
                                                                  <tbody>
                                                                <tr>
                                                                    <th colspan="3" style="text-align: right;font-weight: normal;"><strong>Grand Total : </strong> {{$grand_total ?? ''}} </th>
                                                                    
                                                                 </tr>
                                                                <tr>
                                                                    <th colspan="3" style="text-align: right;font-weight: normal;"><strong>Paid  :</strong> {{$Paids ?? ''}} </th>
                                                                 </tr>
                                                                <tr>
                                                                    <th colspan="3" style="text-align: right;font-weight: normal;"><strong>Discount   :</strong> {{$Discount ?? ''}} </th>
                                                                 </tr>
                                                                <tr>
                                                                    <th colspan="3" style="text-align: right;font-weight: normal;"><strong>Fine  :</strong> {{$Fine ?? ''}} </th>
                                                                 </tr>
                                                                <tr>
                                                                    <th colspan="3" style="text-align: right;font-weight: normal;"><strong>Balance  :</strong> {{$balances ?? ''}}</th>
                                                                 </tr>
                                                               
                                                                <input type="hidden"id="balance" value="{{$balances ?? ''}}">
                                                                </tbody></table>
                                                              </div>
                                                            </div>
                                                            <!-- /.col -->
                                                          </div>
                                                                </tr>
                                                                @else
                                                                <tr class="text-center">
                                                                    <td colspan="12"><b>!! NO DATA FOUND !!</b></td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                 
                                                </div>
                                            </div>
                                         <div class="tabData w-100" id="tab_online_pay" style="display:none;">
                                         <div class="container">
                                                    <form>
                                                        <div class="row">
                                                        <!-- <div class="col-md-6">
                                                            <label for="feesType">Fees Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="feesType" required>
                                    <option value="" selected>Select</option>
                                    </select>
                                </div> -->
                                <div class="col-md-6">
                                    <label for="amount">Amount <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" id="amount" placeholder="Enter Amount" required>
                                </div>
                                </div>
                                <div class="row mt-3">
                                <div class="col-md-12 html">
                                    
                                </div>
                                <div class="col-md-6">
                                    <label for="paymentMethod">Payment Method <span class="text-danger">*</span></label>
                                    <select class="form-control" id="paymentMethod" required>
                                    <option value="" selected>Select Payment Method</option>
                                    <!-- Add options here -->
                                    </select>
                                </div>
                                </div>
                                <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-credit-card"></i> Fees Pay Now
                                    </button>
                                </div>
                                </div>
                            </form>
                            </div>

                                     
                                      
                                        </div>
                                        <div class="tabData w-100" id="tab_payment_history" style="display:none;">
                                            <div class="col-md-12">
                                                <div class="card-body">
                                                      <table class="table">
                                                        <thead>
                                                            <tr class="sky_tr">
                                                                <th>#</th>
                                                                <th>Invoice No</th>
                                                                <th>Fees Type</th>
                                                                <th>Date</th>
                                                                <th>Method</th>
                                                                <th>Discount</th>
                                                                <th>Fine</th>
                                                                <th>Amount</th>
                                                                <th>Payment Status</th>
                                                            </tr>
                                                        </thead>
    
                                                        <tbody>
                                                            @if(!empty($getPaidFees))
                                                            @php
                                                                $i = 1;
                                                                
                                                            @endphp
                                                                @foreach($getPaidFees as $item2)
                                                                   <tr>
                                                                       @php
                                                                            $fee_detail_id = explode(',',$item2->fees_details_id);
                                                                           $head_names = '';
                               
                                                                            $head_names = DB::table('fees_detail')
                                                                                ->leftJoin('fees_group', 'fees_detail.fees_group_id', '=', 'fees_group.id')
                                                                                ->whereIn('fees_detail.id', $fee_detail_id)
                                                                                ->whereNull('fees_detail.deleted_at')
                                                                                ->pluck('fees_group.name') 
                                                                                ->implode(',');        
                                                                     
                                                                       @endphp
                                                                        <td>{{ $i++ }}</td>
                                                                        <td>
                                                                               <form target='_blank'action="{{ url('printFeesInvoice') }}" method="post">
                                                                                     @csrf
                                                                                     
                                                                                     <input type='hidden' name='fees_details_invoice_id' value='{{$item2->id}}' />
                                                                                     <button class='btn btn-xs btn-primary'>
                                                                                    {{ $item2->invoice_no ?? '' }}
                                                                                    
                                                                                    </button>
                                                                                    
                                                                                </form>
                                                                            </td>
                                                                        <td>{{ $head_names ?? '' }}</td>
                                                                        <td>
                                                                            @if(!empty($item2->payment_date)) 
                                                                                {{ date('d-M-Y', strtotime($item2->payment_date)) }}
                                                                            @endif
                                                                            
                                                                        </td>
                                                                        <td>{{$item2->payment_mode ?? ''}}</td>
                                                                        <td>{{ $item2->discount ?? '0' }}</td>
                                                                        <td>{{ $item2->total_fine ?? '0' }}</td>
                                                                        <td>{{ $item2->amount ?? '0' }}</td>
                                                                        <td>
                                                                            @if($item2->status == 0)
                                                                                <span style="color: green;">Received</span>
                                                                            @elseif($item2->status == 1)
                                                                                <span style="color: #fd7e14;">Pending</span>
                                                                            @else
                                                                               <span style="color: red;">Cancelled</span>
                                                                            @endif
                                                                        </td>
                                                                                                                                                 
                                                                   </tr>
                                                                  
                                                                @endforeach
                                                               
                                                                @else
                                                                <tr class="text-center">
                                                                    <td colspan="12"><b>!! NO DATA FOUND !!</b></td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                                 
                                                </div>
                                            </div>
                                           
                                      
                                </div>
                                </div>
                                </div>
                             </div>
                            </section>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function(){
        var length = $('.get_data').length;
        for(var i= 0; i < length; i++){
            var title = $('.get_data').eq(0).data('title');
            $('.get_data').eq(0).addClass('active');
        }
        
        $('#tab_' + title).show();
        
        $('.get_data').click(function(){
            var title = $(this).data('title');
            $('.tabData').hide();
            $('.get_data').removeClass('active');
            
            $(this).addClass('active');
            $('#tab_' + title).show();
        }); 
    });

</script>

<style>
.table th, .table td {
  padding: 4px 8px;
}
.tabs-custom .nav-tabs {
    position: relative;
    display: flex;
    list-style: none;
    padding: 0;
    margin: 0;
}
.label-success-custom {
    border: #47a447 1px solid;
    color: #47a447;
    
}
.label-danger-custom {
    border: #d2322d 1px solid;
    color: #d2322d;
}
.label1 {
     display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: bold;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}

.tabs-custom .nav-tabs > li {
    position: relative;
    margin-right: 20px;
}

.tabs-custom .nav-tabs > li a {
    text-decoration: none;
  padding: 3px 19px;
  display: inline-block;
  position: relative;
  color: #333;
  font-weight: bold;
  transition: all 250ms ease;
  font-size: 13px;
}

/*.tabs-custom .nav-tabs > li.active a {
    color: #ffbd2e;
}*/
.nav-tabs {
  border-bottom: none;
}
.tabs-custom .nav-tabs > li.active:before {
    content: '';
    height: 4px;
    width: 8px;
    display: block;
    position: absolute;
    bottom: -5px;
    left: 50%;
    border-radius: 0 0 8px 8px;
    transform: translateX(-50%);
    background: #ffbd2e;
}

.tabs-custom .nav-tabs > li.active {
    border-bottom: 2px solid #ffbd2e;
}

    .listing_tab ul{
        padding-left: 0px;
        margin-bottom: 0px;
        list-style: none;
        display: flex;
        align-items: center;
    }
    
    .padding_table td,
    .padding_table th{
        padding: 10px;
    }
    
  
    
    .listing_tab{
        margin: 10px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid #c6c6c6;
       
    }
    
    /*.listing_tab ul li{
        background: lightgray;
        padding: 10px;
        margin: 0px 10px;
        border-radius: 4px;
        cursor:pointer;
        font-size:16px;
        font-weight:400;
    }*/
    
   
    
    .listing_tab ul li:first_child{
        margin-left:0px;
    }
    .sky_tr {
  background: #e6e6e659;
  color: black;
}
</style>


<script>
    $(document).ready(function() {

    $('#amount').on('keyup', function(event) {
        var amount = Number($(this).val());
        var balance = Number($('#balance').val());
        if(balance >= amount){
        $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                    },
                    url: '/fees_check',  // Replace with your actual route
                    method: 'POST',
                    data: {
                        amount: amount,
                    },
                    success: function(response) {
                       $('.html').html(response.data);

                    },
                    error: function(xhr) {
                        console.log('An error occurred:', xhr);
                    }
                }); 
            }else{
                toastr.error('Amount cannot be greater than total pending.' + balance);

                Number($(this).val(''));
                $('.html').html('');
            }
    });

});
</script>
@endsection