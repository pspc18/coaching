<head>
    <title>School | Fees Receipt</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
@php
$getSetting=Helper::getSetting();

$account=Helper::getQRCode($getSetting->account_id);

@endphp
 <style>
     
        .student_img {
  width: 70px;
  height: 78;
  margin-top: 26%;
  margin-left: 20%;
  padding-bottom: 10px;
        }

        .row {
            margin-right: 0px;
        }
            </style>
<body style="border: 2px solid black;">
   

    <table style="border-bottom: 2px solid black;">
        <tbody>
            <tr>
                <td rowspan="4">
                    <img rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }} " style="width: 115;"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
                </td>

                <td style=" font-size:28px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>

                <td rowspan="4">
                    <!--<img src="{{ env('IMAGE_SHOW_PATH').'/setting/right_logo/'.$getSetting['right_logo'] }}" style="width: 115;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/rukmani_logo.png' }}'">-->
                </td>



            </tr>
            <tr style="text-align:center;">



                <td style="font-size:14px;text-align:center;">
                    <p style="margin-bottom:-0.5%;"><b>Address </b> {{$getSetting['address'] ?? ''}}</p>
                </td>

            </tr>
            <tr>


                <td style="font-size:14px; text-align:center;">
                    <p style="margin-bottom:1%;"><b>Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}} </p>
                </td>
            </tr>
       

        </tbody>
    </table>

    <table Style="Width:100%;">
        <div class="row">
 
            <div class="col-sm-2"><b>Name</b></div>
            <div class="col-sm-3 border-right"><b>: {{ $data['stuData']['first_name'] ?? '' }} {{ $data['stuData']['last_name'] ?? '' }}</b></div>
             <div class="col-sm-2"><b>Father's Name</b></div>
            <div class="col-sm-3 border-right"><b>: {{ $data['stuData']['father_name'] ?? '' }}</b></div>
            <div></div>
        </div>
        <div class="row">
            <div class="col-sm-2 border-left"><b>Date</b></div>
            <div class="col-sm-3 border-right"><b>: {{date('d-m-Y', strtotime($data['stuData']['admission_date'])) ?? '' }}</b></div>
             <div class="col-sm-2"><b>Mobile</b></div>
            <div class="col-sm-3 border-right"><b>: {{ $data['stuData']['mobile'] ?? '' }}</b></div>
        </div>

        <div class="row">
            <div class="col-sm-2"><b></b></div>
            <div class="col-sm-3 border-right"></div>
            <div class="col-sm-2"></div>
            <div class="col-sm-3 border-right"></div>

            <div class="col-sm-2" style="margin-top: -7%;"><span class="style73">

                    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['stuData']['student_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
                </span></div>
        </div>
    </table>


    </div>
    <table width="100%" style="border: 2px solid black;">
        <tr class="bg-light">
            <td class=""><b>{{__('fees.Slip No') }}</b></td>
            <td class=""><b>{{__('Date') }}</b></td>
            <td class=""><b>{{__('Month') }}</b></td>
            <td class=" text-right"><b>{{__('hostel.Discount') }}</b></td>
            <td class=" text-right"><b>{{__('common.Amount') }}</b></td>
        </tr>
        @if(!empty($data['stuPaidDetail']))
        @php

        $i=1;
        $total_amount = 0;
        $discount_amount = 0;
        @endphp
        @foreach ($data['stuPaidDetail'] as $item)

                @php
               $month = DB::table('months')->where('id',$item->month_id)->first();
            @endphp
        <tr>
          <td><b>{{$item->receipt_no  ?? '' }}</b></td>
            <td><b>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</b></td>
            <td><b>{{$month->name ?? '' }}</b></td>
         

            <td class="text-right">
                {{ number_format($item->discount ,2) ?? '' }}

                </b>
            </td>
            <td class="text-right">{{ number_format($item->total_amount ,2) ?? '' }}</td>
        </tr>
        @php
        $total_amount += $item->total_amount;
        $discount_amount += $item->discount;
        @endphp
        @endforeach
        @endif
        <tr class="tr_border">
            <td><b>&nbsp;</b></td>
            <td><b>&nbsp;</b></td>
            <td class="text-center"><b>{{__('hostel.Total') }} :</b></td>
            <td class="text-right"><b>{{ number_format($discount_amount,2) ?? ''}}</b></td>
            <td class="text-right"><b>{{ number_format($total_amount ,2) ?? '' }}</b></td>

        </tr>
    </table>
    @php
    $qrcode = Helper::getQRCode($getSetting['account_id']);
    @endphp
    <table width="100%">
        <tr class="tr_border">
            <td width="67.5%">
                
               @if(!empty($account->uplode_qr))
                <img src="{{ env('IMAGE_SHOW_PATH').'/uplode_qr/'.$account->uplode_qr }}" style="height:90px;">
            @endif

            </td>
            <td width="32.5%">
                <table width="100%">

                    <tr>
                        <td style="text-align:left;"><b>{{__('Total Assign Amount') }}</b></td>
                        <td class="text-right"><b>{{ $data['stuData']['hostel_fees']  ?? '' }}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;"><b>{{__('Collect Amount') }}</b></td>
                        <td class="text-right"><b id="inNumber">{{ $total_amount  ?? '' }}</b></td>
                    </tr>
                    <tr>
                       
                        <td  ><b>{{__('hostel.Total Discount') }}</b></td>
                        <td  class="text-right"><b>{{ $discount_amount  ?? '' }}</b></td>
                    </tr>
                    <tr>
                       
                        <td  ><b>{{__('Due Amount') }}</b></td>
                        <td  class="text-right"><b>{{ $data['stuData']['hostel_fees']-$discount_amount-$total_amount  ?? '' }}</b></td>
                    </tr>
                    
       


                </table>
            </td>
        </tr>
    </table>

 

    <table style="width:100%;text-align:center;border-top:2px solid black;">

        <tr>


            <td style="width:59%;"><b>Amount In Word</b></td>
            <td style="width:24%;"></td>
            <td><b id="inWords"> : {{ $total_amount ?? '' }}</b></td>

        </tr>

    </table>

</body>

</html>
<style>
    .bg-light {
        background-color: #cdcdcd !important;
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

    var number = document.getElementById("inNumber").innerText;
    var b = parseInt(number);
    var Inwords = toWordsconver(b);

    document.getElementById("inWords").innerText = Inwords + " " + "Only";

    //alert(Inwords);
</script>
@include('print_file.print_footer')

<script type="text/javascript">
    window.print();
</script>