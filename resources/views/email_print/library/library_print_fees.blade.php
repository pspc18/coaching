
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
</style>
<body>
@include('print_file.print_header')
<table width="100%" style="border-bottom: 2px solid black;">
    <tr>
        <td width="25%"><b>Receipt No.</b></td>
        <td width="25%"><b>: {{ $data->slip_no ?? '' }}</b></td>
        <td width="25%"><b>Name</b></td>
        <td width="25%"><b>: {{ $data['HostelAssign']->name ?? '' }}</b></td>
    </tr>
    <tr>
        <td width="25%"><b>Date</b></td>
        <td width="25%"><b>: {{ $data->date ?? '' }}</b></td>
        <td width="25%"><b>Father Name</b></td>
        <td width="25%"><b>: {{ $data['HostelAssign']->f_name ?? '' }}</b></td>
    </tr>
    <tr>
        <td width="25%"><b>Mobile</b></td>
        <td width="25%"><b>: +91 {{ $data['HostelAssign']->mobile ?? '' }}</b></td>
        <td width="25%"><b>Fees Month</b></td>
        <td width="25%"><b>: {{ date('M',strtotime($data->date ?? '')) }}</b></td>
    </tr>    
</table>

<table width="100%">
    <tr class="bg-light tr_border" >
        <td width="25%"><b>Sr. No.</b></td>
        <td width="50%" ><b>P A R T I C U L A R S</b></td>
        <td width="25%" class="text-right"><b>Amount</b></td>
    </tr>
    <tr>
        <td width="25%"><b>1</b></td>
        <td width="50%"><b>Library Fees</b></td>
        <td width="25%" class="text-right"><b>{{ $data->amount ?? '' }}</b></td>
    </tr>   
</table>

<table width="100%">
    <tr class="tr_border" >
        <td width="17%"><img width="30%" src="{{ env('IMAGE_SHOW_PATH').'uplode_qr/'.$getSetting['Account']['uplode_qr'] }}"></td>
        <td width="50%">
            <table>
                <tr>
                    <td width="86%"style="text-align:left;"><b>Payment Mode</b></td>
                    <td ><b>: {{ $data['PaymentMode']->name ?? '' }}</b></td>
                </tr>
                <tr>
                   
                    <td  width="86"><b>Total Paid Amount</b></td>
                    <td  ><b>:</b> <b id="inNumber">{{ $data->amount ?? '' }}</b></td>
                </tr>
                
                <tr>
                    <td width="86%"><b>Total Discount</b></td>
                    <td ><b>: 20%</b></td>
                </tr>                
            </table>
            
         
        </td>
      
    </tr>
  
<table width="100%">
    <td width="17%"></td>
     <td width="50%">
          <table>
    
            
            <tr>
                    <td width="83%"><b>Amount In Word</b></td>
                     <td ><b>:</b> <b id="inWords"></b></td>
         </tr>
       
         
         
    
      
            </table>  
  </td>
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