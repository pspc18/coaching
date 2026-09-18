@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);

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
                        <h3 class="card-title"><i class="fa fa-bar-chart-o"></i> &nbsp; {{__('fees.Student Fees Ledger') }}</h3>
                        <div class="card-tools">
                            @if(!empty($data['stuPaidDetail'][0]['admission_id']))
                        <a href="{{url('ledger_print')}}/{{$data['stuPaidDetail'][0]['admission_id'] ?? ''}}" class="btn btn-primary  btn-sm" title="Print" target="_blank"><i class="fa fa-print"></i> {{__('fees.Print') }}</a>
                       @endif
                        <a href="{{url('fees/ledger')}}" class="btn btn-primary  btn-sm" title="Back"><i class="fa fa-arrow-left"></i> {{__('common.Back') }}</a>
                        </div>
                        
                        </div>  
                        
                    	<div class="row m-2">
                		    <div class="col-md-12">	
                
                                <div id="printableArea" name="printableArea">
                                    @include('print_file.print_header')
                                    <table width="100%" style="border-bottom: 2px solid black;">
                                        <tr>
                                            <td width="25%"><b>{{__('common.Name') }}</b></td>
                                            <td width="25%"><b>: {{ $data['stuData']->first_name ?? '' }} {{ $data['stuData']->last_name ?? '' }}</b></td>
                                            <td width="25%"><b>{{__('common.Mobile') }}</b></td>
                                            <td width="25%"><b>: +91 {{ $data['stuData']->mobile ?? '' }}</b></td>        
                                        </tr>
                                        <tr>
                                            <td width="25%"><b>{{__('common.Fathers Name') }}</b></td>
                                            <td width="25%"><b>: {{ $data['stuData']->father_name ?? '' }}</b></td>
                                            <td width="25%"><b>{{__('common.Email') }}</b></td>
                                            <td width="25%"><b>: {{ $data['stuData']->email ?? '' }}</b></td>        
                                        </tr>
                                    </table>
                                    
                                    <table width="100%">
                                        <tr class="bg-light tr_border" >
                                            <td class="text-white"><b>Receipt No.</b></td>
                                            <td class="text-white"><b>Payment Mode</b></td>
                                            <td class="text-white"><b>Transaction Id</b></td>
                                            <td class="text-white"><b>Bank Name</b></td>
                                            <td class="text-white"><b>{{__('Fees Type') }}</b></td>
                                            <td class="text-white"><b>{{__('common.Date') }}</b></td>
                                            <td class="text-white"><b>{{__('fees.Collected') }}</b></td>
                                            @if(!empty($data['stuPaidDetail']))
                                                @php
                                                   $i=1;
                                                  $total_p_amount = 0;
                                                   $discount = $data['stuData']->total_discount ?? 0;
                                                 
                                                @endphp
                                            @foreach ($data['stuPaidDetail'] as $item)    
                                               @php
                                                    
                                                           
                                                   
                                               @endphp
                                        </tr>         
                                        <tr>
                                               <td class="text-left"><b>
                                                {{ $item->receipt_no ?? '' }}
                                                </b></td>
                                            
                                            <td><b>{{ $item['PaymentMode']['name'] ?? ''}}</b></td>
                                            <td><b>{{$item['transition_id'] ?? ''}}</b></td>
                                            <td><b>{{$item['bank_name'] ?? '-'}}</b></td>
                                            <td><b>{{$item['fees_name'] ?? ''}}</b></td>
                                            <td><b>{{date('d-M-Y', strtotime($item['date'])) ?? '' }}</b></td>
                                            <td><b>@if(!empty($item->total_amount))
                                                        ₹ {{ number_format($item->total_amount ,2) ?? '' }}
                                            
                                                    @else
                                                        -
                                                    @endif
                                            </b></td>
                                         
                                            
                                            </tr> 
                                             @php
                                             $total_p_amount +=$item->total_amount;
                                             @endphp
                                            @endforeach
                                            @endif  
                                                <tr class="tr_border">
                                                    <td><b>&nbsp;</b></td>
                                                    <td><b>&nbsp;</b></td>
                                                    <td><b>&nbsp;</b></td>
                                                    <td><b>&nbsp;</b></td>
                                                    <td><b>&nbsp;</b></td>
                                                    <td><b>Total :</b></td>
                                                  
                                                    <!--<td><b>&nbsp;{{$discount ?? ''}}</b></td>-->
                                                    <td><b>₹ {{ number_format($total_p_amount ,2) ?? '' }}</b></td>
                                                </tr>                                             
                                    </table>
                                    
                                    <table width="100%">
                                        <tr class="tr_border" >
                                            <td width="67.5%">
                
                                           @if(!empty($account->uplode_qr))
                                            <img src="{{ env('IMAGE_SHOW_PATH').'/uplode_qr/'.$account->uplode_qr }}" style="height:90px;">
                                        @endif
                            
                                        </td>
                                            <td width="32.5%">
                                                <table width="100%">
                                                    <!--<tr>
                                                        <td style="text-align:left;"><b>{{__('fees.Total Assigned Amount') }}</b></td>
                                                        <td class="text-right"><b>₹ {{ number_format($data['total_assign'] ,2) ?? '' }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>{{__('fees.Total Collected Amount') }}</b></td>
                                                        <td class="text-right"><b id="inNumber">₹ {{ number_format($total_p_amount ,2) ?? '' }}</b></td>
                                                    </tr>
                                                   <tr>
                                                        <td  ><b>{{__('fees.Total Discount') }}</b></td>
                                                        <td  class="text-right"><b>{{ $discount ?? '' }}</b></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td ><b>{{__('fees.Total Left Amount') }}</b></td>
                                                        <td class="text-right"><b>₹ {{ number_format($data['total_assign'] - $discount- $total_p_amount ,2) ?? '' }}</b></td>
                                                    </tr>  -->   
                                                    
                                                    
                                                     <tr>
                                                        <td style="text-align:left;"><b>{{__('fees.Total Assigned Amount') }}</b></td>
                                                        <td class="text-right"><b>₹ {{ number_format($data['total_assign'] ,2) ?? '' }}</b></td>
                                                    </tr>
                                                    <tr>
                                                        <td><b>{{__('fees.Total Collected Amount') }}</b></td>
                                                        <td class="text-right"><b id="inNumber">₹ {{ number_format($total_p_amount ,2) ?? '' }}</b></td>
                                                    </tr>
                                                   <tr>
                                                        <td  ><b>{{__('fees.Total Discount') }}</b></td>
                                                        <td  class="text-right"><b>{{ $discount ?? '' }}</b></td>
                                                    </tr>
                                                    
                                                    <tr>
                                                        <td ><b>{{__('fees.Total Left Amount') }}</b></td>
                                                        <td class="text-right"><b>₹ {{ number_format($data['total_assign'] - $discount- $total_p_amount ,2) ?? '' }}</b></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>  
                                    @include('print_file.print_footer')
                                </div>                
                        </div>
                    </div>
                </div>

    </div>
  </div>
</div>
</section>
</div>

<style>
    /*body {
        border: 2px solid black;
        padding: 10px;
    }*/
    .tr_border {
        border-top: 2px solid black;
        border-bottom: 2px solid black;
    }
</style>
    <script>
    var th_val = ['', 'thousand', 'Lakh', 'Crore', 'trillion'];
    // System for uncomment this line for Number of English 
    // var th_val = ['','thousand','million', 'milliard','billion'];
     
    var dg_val = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    var tn_val = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    var tw_val = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    function toWordsconver(s) {
      s = s.toString();
        s = s.replace(/[\, ]/g, '');
        if (s != parseFloat(s))
            return 'not a number ';
        var x_val = s.indexOf('.');
        if (x_val == -1)
            x_val = s.length;
        if (x_val > 15)
            return 'too big';
        var n_val = s.split('');
        var str_val = '';
        var sk_val = 0;
        for (var i = 0; i < x_val; i++) {
            if ((x_val - i) % 3 == 2) {
                if (n_val[i] == '1') {
                    str_val += tn_val[Number(n_val[i + 1])] + ' ';
                    i++;
                    sk_val = 1;
                } else if (n_val[i] != 0) {
                    str_val += tw_val[n_val[i] - 2] + ' ';
                    sk_val = 1;
                }
            } else if (n_val[i] != 0) {
                str_val += dg_val[n_val[i]] + ' ';
                if ((x_val - i) % 3 == 0)
                    str_val += 'Hundred ';
                sk_val = 1;
            }
            if ((x_val - i) % 3 == 1) {
                if (sk_val)
                    str_val += th_val[(x_val - i - 1) / 3] + ' ';
                sk_val = 0;
            }
        }
        if (x_val != s.length) {
            var y_val = s.length;
            str_val += 'point ';
            for (var i = x_val + 1; i < y_val; i++)
                str_val += dg_val[n_val[i]] + ' ';
        }
        return str_val.replace(/\s+/g, ' ');
    }
    
    var number = document.getElementById( "inNumber" ).innerText;
      var b = parseInt(number);
     var Inwords = toWordsconver(b);
     
     document.getElementById( "inWords" ).innerText=Inwords+ " " + "Only" ;
    
    //alert(Inwords);
    </script>
    
<script type="text/javascript">
function printDiv(printableArea) {
     var printContents = document.getElementById(printableArea).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}
</script>

@endsection 