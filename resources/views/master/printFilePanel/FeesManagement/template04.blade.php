@php
    $getSetting = Helper::getSetting();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> RECEIPT Print</title>
    <style>
     
    body {
        font-family: system-ui;
        font-size: 16px;
        max-width: 1060px;
        margin: 10px auto;
        font-weight: 500;
        color: #8e1616;
   }
   

    table {
        width: 100%;
        border-collapse: collapse;
        margin: 0px;
    }

    .ltr{
        text-align: left;
        border-right: none !important;
        padding:5px
    }
    .rtr{
        text-align:right;
    }
    .ctr{
        text-align:center;
    }
   
    .bg_receipt{
        text-align: center;
        background: #8e1616;
        color: #f9f969;
        padding: 0px 27px 0px 21px;
        border-radius: 10px;
        font-size: 22px;
    }

    .font_spacing{
        margin-bottom: -11px;
        font-weight: 500;
        font-family: emoji;
        letter-spacing: 1px;
        font-size: 16px;
    }
    .bor_class{
        border: 1px solid;
        width: 64%;
        padding: 0px 38px 0px 0px;
    }
    .rupee_icon{
        color: #8e1616;
        padding: 0px 0px 0px 0px;
        font-size: 44px;
        border-right: 1px solid black;
    }
    .td_height{
        height: 25px;
    }
    .head{
        font-size: 65px;
        font-family: auto;
        font-weight: normal;
        text-align: center;
        color: #8e1616;
        margin: -3px;
    }
    .font_class{
        text-align: center;
        font-size: 21px;
        font-weight: 500;
        letter-spacing: 1px;
        margin-top: 0px;
        margin-bottom: -16px;
        color: #8e1616;
    }
    .add_font{
        font-weight: 500;
        letter-spacing: 1px;
        font-size: 16px;
        text-align: center;
        margin-bottom: -12px;
        color: #8e1616;
    }
    .mob{
        text-align: center;
        font-weight: 700;
        margin-bottom: 4px;
        color: #8e1616;
    }
    .padding{
        padding: 32px 0px 0px 0px;
    }
    .amt_font{
        font-size: 30px;
        font-weight: 400;
        margin: 10px;
    }
    @page {
        margin: 0 !important;
    }
    @media print{@page {size: landscape}}

   </style>
</head>
<body class='page'>
    <div style="border: 1px solid;margin-top: 30px;padding: 12px;background-color: #f7f62cb5;">
    <table style="border-bottom: 1px solid;">
        <thead>
                 
            <tr>
                <td width="12%">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 73%;">
                </td>             
                <td width="76%">
                     <p class="head">{{$getSetting['name'] ?? ''}}</p>
                     <p class="font_class">"An Institute For Class Not For Mass"</p>
                     <p class="add_font"> {{$getSetting['address'] ?? ''}}</p>
                     <p class="mob">Mob.: {{$getSetting['mobile'] ?? ''}}</p>
                </td> 
                <td width="12"></td>            
            </tr>      
        </thead>
    </table>
    <table style="margin-top: 10px;">
        <thead>
                 
            <tr>
                <td width="43%">Recepit No. {{$data[0]['receipt_no'] ?? ''}}</td>             
                <td width="13%" class="bg_receipt">RECEIPT</td>             
                <td width="29%" class="rtr">Date</td>             
                <td width="17%" class="" style="border-bottom: 1px dotted;">{{ date('d-M-Y', strtotime($data[0]->created_at)) }}</td>              
            </tr>      
        </thead>
    </table>
    <table style='margin-top: 30px ;'>
        <thead>      
            <tr>            
                <td width="6%" class="">Mr./Mrs</td>               
                <td width="45%" class="" style="border-bottom: 1px dotted;">{{ $data[0]['Admission']['first_name'] ?? '' }} {{ $data[0]['last_name'] ?? '' }}</td>              
                <td width="7%" class="">S/o./D/o</td>               
                <td width="90%" class="" style="border-bottom: 1px dotted;">{{ $data[0]['Admission']['father_name'] ?? '' }}</td>              
            </tr>      
        </thead>
    </table>
    
    <table style='margin-top: 20px ;'>
        <thead>      
            <tr>            
                <td width="5%" class="td_height">Class:</td>               
                <td width="35%" class="td_height" style="border-bottom: 1px dotted;">
                    {{ $data[0]['Admission']['ClassTypes']['name'] ?? '' }}
                </td>              
                <td width="9%" class="td_height">Head Name:</td>  
                @php
                        $fees_types = [];
                        $total_amount = 0;
                        if(count($data) != 0){
                            foreach($data as $item){
                                $fees_types[] = $item->fees_group_name;
                                $total_amount += $item->total_amount;
                            }   
                        }
                    @endphp
                <td width="90%" class="td_height" style="border-bottom: 1px dotted;">
                    {!! implode(',&nbsp;', $fees_types) !!}
                </td>              
            </tr>      
        </thead>
    </table>
    <table style='margin-top: 20px ;'>
        <thead>      
            <tr>            
                <td width="12%" class="td_height">Payment Mode:</td>  
                 @php
            $payment_name = DB::table('payment_modes')->whereNull('deleted_at')->where('id',$data[0]->payment_mode_id)->first();
        @endphp
           
                <td width="38%" class="td_height" style="border-bottom: 1px dotted;">{{ $payment_name->name ?? '' }}</td>              
                <td width="6%" class="td_height">Mobile</td>               
                <td width="90%" class="td_height" style="border-bottom: 1px dotted;">{{ $data[0]['Admission']['mobile'] ?? '' }}</td>              
            </tr>      
        </thead>
    </table>
     @php
                            $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                            $words = $formatter->format($total_amount ?? '');
                            $words .= ' rupees';
                        @endphp
    <table style='margin-top: 20px ;'>
        <thead>      
            <tr>            
                <td width="23%" class="td_height">Amount Deposited ( in Words )</td>               
                <td width="98%" class="td_height" style="border-bottom: 1px dotted;">{{ $words ?? '' }}</td>              
            </tr>      
            <tr>               
                <td colspan="12" width="98%" height="48px" class="" style="border-bottom: 1px dotted;"></td>              
            </tr>      
        </thead>
    </table>
    <table style='margin-top: 70px;'>
        <thead>      
            <tr>            
                <td width="17%" class="">
                    <p class="bor_class"><span class="rupee_icon">Rs.</span><b class="amt_font">{{ $total_amount ?? '-' }}</b></p>
                </td>                     
                <td  width="37%" class="padding">
                    <small>*Fee Onece Paid Will Not Be Refunded.</small>
                </td>                      
                <td width="11%" class="padding rtr">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" style="height:90px;margin-top: -91px;margin-bottom: -13px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'">
                    <p style="">Counselor's Signature </p>
                </td>                     
            </tr>      
        </thead>
    </table>
</div>
   </body>
</html>

