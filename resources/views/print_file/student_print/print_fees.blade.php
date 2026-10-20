<!DOCTYPE html>
<html>
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

$isCollection = $data instanceof \Illuminate\Support\Collection;
$firstDetail = $isCollection ? $data->first() : (is_object($data) ? $data : null);
$dataArr = is_array($data) ? $data : [];
$invoiceObj = isset($invoice_data) && is_object($invoice_data) ? $invoice_data : null;
$admissionObj = ($firstDetail && isset($firstDetail->Admission) && is_object($firstDetail->Admission)) ? $firstDetail->Admission : null;

$receiptNo = ($invoiceObj->invoice_no ?? null)
    ?: ($invoiceObj->offline_receipt_no ?? null)
    ?: ($firstDetail->receipt_no ?? null)
    ?: ($dataArr['receipt_no'] ?? '-');

$rawDate = ($invoiceObj->payment_date ?? null)
    ?: ($invoiceObj->date ?? null)
    ?: ($firstDetail->date ?? null)
    ?: ($dataArr['date'] ?? null);
$feeDate = !empty($rawDate) ? date('d-m-Y', strtotime($rawDate)) : '-';
$feeMonth = !empty($rawDate) ? \Carbon\Carbon::parse($rawDate)->format('F') : '-';

$firstName = ($invoiceObj->first_name ?? null)
    ?: ($admissionObj->first_name ?? null)
    ?: ($firstDetail->first_name ?? null)
    ?: ($dataArr['Admission']['first_name'] ?? ($dataArr['first_name'] ?? ''));

$lastName = ($invoiceObj->last_name ?? null)
    ?: ($admissionObj->last_name ?? null)
    ?: ($firstDetail->last_name ?? null)
    ?: ($dataArr['Admission']['last_name'] ?? ($dataArr['last_name'] ?? ''));

$studentName = trim($firstName . ' ' . $lastName);

$fatherName = ($invoiceObj->father_name ?? null)
    ?: ($admissionObj->father_name ?? null)
    ?: ($firstDetail->father_name ?? null)
    ?: ($dataArr['Admission']['father_name'] ?? ($dataArr['father_name'] ?? ''));

$className = ($invoiceObj->class_name ?? null)
    ?: (isset($admissionObj->ClassTypes) ? ($admissionObj->ClassTypes->name ?? null) : null)
    ?: ($dataArr['Admission']['ClassTypes']['name'] ?? '');

$sectionName = ($invoiceObj->section_name ?? null)
    ?: (isset($admissionObj->Section) ? ($admissionObj->Section->name ?? null) : null)
    ?: ($dataArr['Admission']['Section']['name'] ?? '');

$mobile = ($invoiceObj->mobile ?? null)
    ?: ($admissionObj->mobile ?? null)
    ?: ($firstDetail->mobile ?? null)
    ?: ($dataArr['Admission']['mobile'] ?? ($dataArr['mobile'] ?? ''));

$studentImg = ($invoiceObj->student_img ?? null)
    ?: ($invoiceObj->image ?? null)
    ?: ($admissionObj->student_img ?? null)
    ?: ($admissionObj->image ?? null)
    ?: ($dataArr['Admission']['student_img'] ?? '');

$paymentMode = ($invoiceObj->payment_mode ?? null)
    ?: ($firstDetail->payment_mode ?? null)
    ?: ($dataArr['payment_mode'] ?? 'Cash');

$totalAmount = ($invoiceObj->amount ?? null)
    ?? ($invoiceObj->total_amount ?? null)
    ?? ($isCollection ? $data->sum('total_amount') : ($firstDetail->total_amount ?? ($dataArr['total_amount'] ?? 0)));

$totalDiscount = ($invoiceObj->discount ?? null)
    ?? ($invoiceObj->discount_given ?? null)
    ?? ($isCollection ? $data->sum('discount') : ($firstDetail->discount ?? ($dataArr['discount'] ?? 0)));
@endphp
<body>
    <style>
        
         body {
            
    border: 1px solid;
    }
    .student_img {
    width: 80px; 
    height:100; 
    margin-top: 5%;
    margin-left:20%;
    padding-bottom: 10px;
        
    }
    
    .row{
        margin-right: 0px;
    }
    .img_background_fixed{
      position: relative;
    }
    
    .img_absolute{
        position: absolute;
        top: 113px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        right: 0;
    }
    
    .backhround_img{
        opacity: 0.3;
        width: 34%;
    }
    </style>
	<table style="border-bottom: 2px solid;">
			<tbody >
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }} " style="width: 97px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"></td>
  
      <td   style=" font-size:28px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
   
      <td rowspan="4"> 
      </td>
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="font-size:14px;text-align:center;"><p style="margin-bottom:-0.5;"><b >Address </b> {{$getSetting['address'] ?? ''}}</p></td>

      </tr>
      <tr>
     
   
      <td  style="font-size:14px; text-align:center;"><p style="margin-bottom:6px;"><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}  </p></td>
    </tr>
   	<tr style="text-align:center;">
       

   

      </tr>

    
  </tbody></table> 

<table Style="Width:100%;">
     <div class="img_background_fixed">
        <div class="img_absolute">
        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
        </div>
<div class="row">

<div class="col-sm-2"><b>Name</b></div>
<div class="col-sm-3 border-right"><b>: {{ $studentName }}</b></div>
<div class="col-sm-2"><b>Father's Name</b></div>
<div class="col-sm-3"><b>: {{ $fatherName }}</b></div>

<div></div>
</div>
<div class="row">

</div>
<div class="row">
<div class="col-sm-2 border-left"><b>Class</b></div>
<div class="col-sm-3 border-right"><b>: {{ $className }} 
@if(!empty($sectionName))
({{ $sectionName }})
@endif

</b>
</div>
<div class="col-sm-2"><b>Mobile</b></div>
<div class="col-sm-3"><b>: {{ $mobile }}</b></div>
</div>
<div class="row">

<div class="col-sm-2"><b>Fee Month</b></div>
<div class="col-sm-3 border-right"><b>: {{ $feeMonth }}</b></div>
</div>
<div class="row">
<div class="col-sm-2"><b></b></div>
<div class="col-sm-3 border-right"></div>
<div class="col-sm-2"></div>
<div class="col-sm-3"></div>

<div class="col-sm-2 border-left" style="margin-top: -72px;"><span class="style73">@if(!empty($studentImg))

    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_image/'.$studentImg }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
    @else
    <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
    @endif </span></div>
</div>
</div>
</table>



<table style="width:100%;" class="table-bordered">
    <tr style="border-top:2px solid black;">
        <th colspan="3">Receipt No.</th>
        <th colspan="3">Date</th>
        <th colspan="3">Discount</th>
        <th colspan="3">Amount</th>
    </tr>
    <tr style="border-top:2px solid black;">
        <td colspan="3">{{ $receiptNo }}</td>
        <td colspan="3">{{ $feeDate }}</td>
        <td colspan="3">{{ $totalDiscount ? $totalDiscount : '-' }}</td>
        <td colspan="3">{{ $totalAmount }}</td>
    </tr>
</table>

<table style="width:100%;">
    <tr style="border-top:2px solid black;">
<td rowspan="4 border-right border-top;"style="width: 25%;">
     @if(!empty($account->uplode_qr))
                <img src="{{ env('IMAGE_SHOW_PATH').'/uplode_qr/'.$account->uplode_qr }}" style="height:90px;">
            @endif

</td>


<td colspan="3"><b>Payment Mode</b></td>
<td colspan="3"></td>
<td colspan="3"></td>
<td colspan="3"><b>: {{ $paymentMode }}</b></td>




    
</tr>
<tr>
   
<td colspan="3"><b>Total Discount</b></td>
<td colspan="3"style="width:145px;"></td>
<td colspan="3"></td>
<td colspan="3" ><b>:</b> <b>{{ $totalDiscount ? $totalDiscount : '-' }}</b></td>
</tr>
<tr>
   
<td colspan="3"><b>Total Paid Amount</b></td>
<td colspan="3"style="width:145px;"></td>
<td colspan="3"></td>
<td colspan="3" ><b>:</b> <b id="inNumber">{{ $totalAmount }}</b></td>

</tr>


</table>

<table style="width:100%;text-align:center;border-top:2px solid black;">

<tr>
    
  
<td style="width:59%;"><b>Amount In Word</b></td>
<td style="width:24%;"></td>
<td><b id="inWords"> : {{ $dataArr['amount']['name'] ?? '' }}</b></td>

</tr>

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

<script type="text/javascript">
window.print();
</script>

</body>

</html>