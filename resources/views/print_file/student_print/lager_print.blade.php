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
//dd($data);
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
        .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: 159px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width: 33%;
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
                    <p style="margin-bottom:3px;"><b>Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}} </p>
                </td>
            </tr>
            <tr style="text-align:center;">



                <!--<td style="font-size:14px;text-align:center;"><b>www.rukmanisoftware.com</b></td>-->

            </tr>


        </tbody>
    </table>

    <table Style="Width:100%;">
        <div class="img_background_fixed">
        <div class="img_absolute">
        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
        </div>
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
        </div>
    </table>


    </div>
    <table width="100%" style="border: 2px solid black;">
        <tr class="bg-light">
            <td class=""><b>{{__('fees.Slip No') }}</b></td>
            <td class=""><b>{{__('common.Date') }}</b></td>
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


        <tr>
          <td><b>{{$item->receipt_no  ?? '' }}</b></td>
            <td><b>{{date('d-m-Y', strtotime($item['date'])) ?? '' }}</b></td>

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
            <td width="67.5%"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAOEAAADhCAMAAAAJbSJIAAAAG1BMVEUAAAD///8iIiL5+fkmJib39/cfHx/y8vI2NjaY7mNSAAAFNklEQVR4nO2d67KbMAyESUjO6fs/cafTwdCstaxsp0DO7k9btvVBMpIvwHSbu3QDkSpB2LrPv/k2zVOX0EdSJai0fkJJm+azEd6LZ1DSJhPuyoQmNOHnEZbWJVqMI2xzBEuEKhLrcKh7XEX0OJaQ/AIxE8LfraK1lQlNaEITmvCihCSaM1+HEaKvxGkCT5rHhOQqzMIIlVanJcRfspKpVlqZ0IQmNKEJr074iG1JPDyIUDEWOkZ9CCHJyExoQhOa0IQmvBAhKm5VCta1d2h0vpwmQ6jknCfOS01oQhOa0IT/hVAweeuatyKFmZRkrtj5dmaUEnCaYJjQhCY0oQl/OiGxRV9TDtbOtbWpiVBp9IaziW1qIhyVhSoy4a5MWC0xYb2kTSbclQmrJRjriglZBW/TnMsU2oSoQFjxbNjoozpiY5jQhH2jj+qIjWFCE/aNPqojNsYPJowBx/mVy2kU1wRnseYBJcUGH+VLlRxD2LnllCoxoQlNaEITXpWQTOSJDdnQJ5v+tZxG4VFQBRtMzYiNskVzAULMVE1oQhOa0IQ/nZAMjjY4SyaEmx6JeSdG3B957FCxQZFNf23fYjRh6oCoItLKhCY0oQlNeFVCpb+2eIhVrWcT0VkFNR4qlQMofnW/c6+PUElHJzBOHTTtPl9qQhOa0IQmPJoQu/m/8RD7JmBETa0V5iLlzfTHEI7KSxVjE5rQhCb8VEKlkRItlA43hI/7X61dLyWo6Va+4lJGg5Kw+fQdfxXm19erF4vxeqO+X42JzbP1OzPkhgutSdr1FRtj1vYl2JyOMLX39IbTJiY0oQlNeBnCGLAS6LEKS5Bw7XGT05CPs4GWaL4J9JAwTFHPa6QunmGgL7pDx1iChCW70FYxcFi8worNIozUGOhTojszRxCS/0+bTBj4YcJ/Wyk2JmyWCQM/3keINak9bVRyl7tES4ysq2tlCr3oGecJr5N9nKRLM3q8P+tEftEMxsm3Cq6EguLWJNArOScao85C2JZnm9CEJjShCSca6LGKGBPC1UYijFfsMU9YSyAHgDl+JdBjFck7YIlgLVnG2q7qM8JM1c6d/SMS6FOTfbK8v7mbRxAqf78UITuIYkITmtCEH0qoBHpFOPgTB5uWwExm61JVGSTe/cfYLUz2KwkDLt3jZH9LOEiZa1+kTPZRqQT1LIRtWbUJTWhCE5pwoqlEipA6NsMifkbKysAaqsEEQnYl/8L5e3yEr+LgG94jTC5s3E3n8TyyMnABwlRJpWcT7smEJgx9NWG9pNLz+QmLDXlXPXFwu6ofTqor02zEeEIVTPbLBJ7EdyzB5YR4jl9ZBxj3rSDlego8bE0JbLBDtDGhCU1oQhNelbDYks16MjjZFEAvpJN7bEYPrTJn9dlmfbyuQDYFVk9rT+cpN4ERCq0WdZ7ML5I2BQ4h7DwvWyQt1Zkw8JWUkFYmzMqEJmS+khLSalHnyXzsmOUAbySEB/dIyMaboBzaxw6n16rs6csc4X6How6aEplw12kTmtCEJtzVwYTFBCOa8nE1Re8khGf2cXMgPrCHD9yxR/CgOV+nGUWINqDUgT2U9Ns+ljB3MsaEJjShCU2ouZgBZHkCJQyfqqudzCeEMMcnCzZkfR6O8JG1gspe/7idGXLr4huknMGTjueRns9GqPy1TGhCE5rQhNVuyPydfFMt7rj61vn9p+pqsbaCEacH4at4cP5eyTIIIXHwDacvyQ0HdWahiky4KxOa0IQm3NXBhLWny0O1OTinjuhXVMbHEqyKWytqdPA3WHBz+4cepeoAAAAASUVORK5CYII=
" style="height:90px;">
            </td>
            <td width="32.5%">
                <table width="100%">

                    <tr>
                        <td style="text-align:left;"><b>{{__('Total Assign Amount') }}</b></td>
                        <td class="text-right"><b>{{$data['total_assign'] ?? '' }}</b></td>
                    </tr>
                    <tr>
                        <td style="text-align:left;"><b>{{__('hostel.Total Amount') }}</b></td>
                        <td class="text-right"><b id="inNumber">{{$total_amount ?? '' }}</b></td>
                    </tr>
                    <tr>

                        <td><b>{{__('hostel.Total Discount') }}</b></td>
                        <td class="text-right"><b>{{$discount_amount  ?? '' }}</b></td>
                    </tr>
                    <tr>

                        <td><b>{{__('Due') }}</b></td>
                        <td class="text-right"><b>{{$data['total_assign']-$total_amount-$discount_amount  ?? '' }}</b></td>
                    </tr>
                  
                </table>
            </td>
        </tr>
    </table>

 

    <table style="width:100%;text-align:center;border-top:2px solid black;">

        <tr>


            <td style="width:68%;text-align: right;"><b>Amount In Word  :</b></td>
            <td> <b id="inWords"> {{ $total_amount ?? '' }}</b></td>

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