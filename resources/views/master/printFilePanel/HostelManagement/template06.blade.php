
<head>
<title>Expense Receipt 2</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
@php
$getSetting=Helper::getSetting();

@endphp
<style>
    body {
        border: 2px solid black;
        padding: 10px;
    }
    .tr_border {
        border-top: 2px solid black;
        border-bottom: 2px solid black;
    }
       .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: 83px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width: 32%;
        }
</style>
<body>
@include('print_file.print_header')
<table width="100%" style="border-bottom: 2px solid black;">
   
    <tr>
        <td width="10%"><b>Date</b></td>
        <td width="90%"><b>: {{ date('d-m-Y', strtotime($data['expense_date']))?? ''  }}</b></td>
        
    </tr>
    
</table>

        <table width="100%" style="border-bottom: 2px solid black;">
            <div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
                </div>
                   <thead>
                  <tr role="row" style="text-align:center;">
                      <th style="border-right: 1px solid black;border-bottom: 1px solid black;border-left: 1px solid black;">Sr.NO.</th>
                      <th style="border-right: 1px solid black;border-bottom: 1px solid black;">Expense Name</th>
                      <th style="border-right: 1px solid black;border-bottom: 1px solid black;">Amount</th>
                     <th style="border-right: 1px solid black;border-bottom: 1px solid black;"> Expense By</th>
                     <th style="border-right: 1px solid black;border-bottom: 1px solid black;"> Date</th>
                 
                    </tr>
                            
                  </thead>
                  <tbody>
                 
                 <tr style="text-align:center;">
                    <td style="border-right: 1px solid black;border-left: 1px solid black;">1</td>
                    <td style="border-right: 1px solid black;">{{ $data['expense_name'] ?? ''  }} </td>
                    <td style="border-right: 1px solid black;">{{ $data['expense_amount'] ?? '' }}</td>
                    <td style="border-right: 1px solid black;">{{$data['first_name'] ?? ''}}{{$data['last_name'] ?? ''}}</td>
                    <td style="border-right: 1px solid black;">{{ date('d-m-Y', strtotime($data['expense_date']))?? ''  }}</td>
                  </tr>
      
            </tbody>
            </div>
                  </table>


</table>
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
@include('print_file.print_footer')
</body>
<script type="text/javascript">
  window.print();
</script>