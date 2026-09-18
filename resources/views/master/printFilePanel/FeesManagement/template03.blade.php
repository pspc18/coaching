@php
    $getSetting = Helper::getSetting();

@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Receipt</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    body {
        font-family: "Prompt", sans-serif;
        background-color: #e1a0cb;
        color: #452d78;
    }


    table {
        width: 100%;
    }

    p {
        margin: 0;
    }

    .center_text td {
        text-align: center;
    }

    .title {
        font-size: 36px;
        margin-top: 13px;
        margin-bottom: -14px;
        word-spacing: 10px;
        text-align: center;
        line-height: 70px;
    }

    .description {
        font-size: 17px;
        margin: -3px;
        word-spacing: 5px;
        text-align: center;
    }

    .text_danger {
        color: red;
    }

    .dotted_bottom {
        border-bottom: 2px dotted;
    }

    .nowrap {
        white-space: nowrap;
    }

    .text_end {
        text-align: end;
    }

    .margin_space {
        margin-top: 10px;
        margin-bottom: 0px;
        font-size: 15px;
    }

    .margin_space2 {
        margin-top: 25px;
        margin-bottom: 0px;
        font-size: 15px;
    }

    .amount_box {
        width: 200px;
        border: 2px solid #452d78;
        height: 40px;
        position: relative;
        font-size: 20px;
        display: flex;
        align-items: center;
    }

    .rupee_sign {
        background-color: #452d78;
        height: 100%;
        padding: 0px 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 10px;
        font-size: 40px;
    }

    .margins {
        border: 1px solid;
        margin: 0px;
        padding: 7px;
    }

    .note {
        font-size: 15px;
    }

    .background_highlight {
        background-color: #452d78;
        border-radius: 5px;
        padding: 5px;
        color: white;
        text-transform: uppercase;
        width: 14%;
        margin: 0px auto;
    }

    .top_gap {
        margin-top: 70px;
    }

    @page {
       // size: landscape;
    }
    
    .canceled{
        font-size: 80px;
        color: #ff00008a;
        position: absolute;
        top: 231px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        right: 0;
        transform: rotate(-26deg);
    }
</style>

<body>
@for($i= 0; $i < 2; $i++)
    @if(!empty($invoice_data->status) && $invoice_data->status == 2)
        <p class="canceled">Canceled</p>
    @endif
    <div class="margins" style="margin-top: {{ $i == 1 ? '31px;' : '0px;' }}">
   
        <table class="margin_space">
            <tbody>
                <tr class="center_text">
                    <td>
                        <p class="background_highlight">Fee Receipt</p>
                        <p style="margin-bottom: -19px;"><b>{{ $i == 1 ? 'Student' : 'Office' }} Copy</b></p>
                    </td>
                </tr>

                <tr>
                    <td colspan="12">
                        <h4 class="title"> 
                            <i>{{$getSetting['name'] ?? ''}}</i>
                        </h4>
                        <p class="description">{{$getSetting['address'] ?? ''}}</p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="nowrap margin_space">
            <tbody>
                <tr>
                    <td width="85%">
                        <p>Receipt No. <span class="text_danger">{{$invoice_data->invoice_no ?? ''}}</span></p>
                    </td>

                    <td width="3%">
                        <p>Date</p>
                    </td>

                    <td width="12%">
                        <p class="dotted_bottom">&nbsp; {{ date('d-M-Y', strtotime($invoice_data->date ?? ''))}}</p>
                    </td>
                </tr>
               
            </tbody>
        </table>
        <table class="nowrap margin_space">
            <tbody>
                <tr>
                    <td width="5%">
                        <p>Registration No.</p>
                    </td>
                    <td width="28%">
                        <p class="dotted_bottom">&nbsp; {{ $invoice_data->admissionNo ?? '-' }}</p>
                    </td>

                    <td width="3%">
                        <p>Class</p>
                    </td>

                    <td width="30%">
                        <p class="dotted_bottom">&nbsp; {{ $invoice_data->class_name ?? '' }}</p>
                    </td>
                    <td width="5%">
                        <p>Fee Type</p>
                    </td>
                    
                    @php
                        $fees_types = [];
                        $total_amount = 0;
                        if(count($data) != 0){
                            foreach($data as $item){
                            
                        $feesGroup  = DB::table('fees_group')->whereNull('deleted_at')->where('id',$item['fees_group_id'])->first();
                        $fees_master  = DB::table('fees_master')->whereNull('deleted_at')->where('fees_group_id',$item['fees_group_id'])->where('class_type_id',$invoice_data->class_type_id)->first();
                        
           
                                $fees_types[] .= $feesGroup->name.'';
                                $total_amount += $item->paid_amount;
                            }   
                        }
                    @endphp

                    <td width="28%">
                        <p class="dotted_bottom">&nbsp; {!! implode(',&nbsp;', $fees_types) !!}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="nowrap margin_space">
            <tbody>
                <tr>
                    <td width="20%">
                        <p>Received with thanks form Mr/Mrs</p>
                    </td>

                    <td width="80%">
                        <p class="dotted_bottom">&nbsp; {{ $invoice_data->first_name ?? '-' }} {{ $invoice_data->last_name ?? '' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="nowrap margin_space">
            <tbody>
                <tr>
                    <td width="5%">
                        <p>Father's Name</p>
                    </td>

                    <td width="45%">
                        <p class="dotted_bottom">&nbsp; {{ $invoice_data->father_name ?? '-' }}</p>
                    </td>

                    <td width="5%">
                        <p>Cash/Cheque</p>
                    </td>

                    <td width="45%">
                        <p class="dotted_bottom">&nbsp; {{ $invoice_data->payment_mode ?? '-' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        @php
                            $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                            $words = $formatter->format($total_amount ?? '');
                            $words .= ' rupees';
                        @endphp

        <table class="nowrap margin_space">
            <tbody>
                <tr>
                    <td width="5%">
                        <p>Amount in Words</p>
                    </td>

                    <td width="95%">
                        <p class="dotted_bottom" style="text-transform: capitalize;">&nbsp;  {{ $words ?? '' }}</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <table class="nowrap margin_space2">
            <tbody>
                <tr>
                    <td width="100%">
                        <p class="amount_box">
                            <span class="rupee_sign">
                                ₹
                            </span>

                            <span class="amount">
                                {{ $total_amount ?? '-' }}
                            </span>
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>
        
  <table style ='margin-top:-50px;' >

    <tfoot style='border:1px solid black'>
            <tr>
            <td style="text-align: center;"></td>
                <td style="text-align: right">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/principal_sign/'.$getSetting['principal_sign'] }}" style="height:90px;margin-top: -10px;margin-bottom: -10px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'">

                </td>
                
            </tr>
            <tr>
            <td style="text-align: left;font-size: 11px;">&nbsp;&nbsp;&nbsp;• The fee Once paid Shall neither Refunded nor Adjusted under any Circumstances</td>
                <td style="text-align: right;padding:10px">
            Authorised Signature 
            </td>
                
            </tr>
           
           
           
        </tfoot>
    </table>


</div>
@endfor
</body>

</html>