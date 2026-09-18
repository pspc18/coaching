@php
use Carbon\Carbon;
$getSetting = Helper::getSetting();

@endphp

<!DOCTYPE html>
<html>
    
<body >
<head>
    <title>Salary Slip</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


    <style type="text/css">
        .style4 {
            font-size: 18px
        }
        
        .style5 {
            font-size: 40px;
            font-weight: bold;
            color:#aac818;
        }
        
        .style9 {
            font-size: 16px
        }
        
        .style10 {
            font-size: 14px;
            padding: 0px 15px 0px;
            letter-spacing:1px;
            line-height:40px;
        }
        
        .style11 {
            font-size: 36px;
            font-weight: bold;
        }
    </style>
</head>

<!--//Month first and last date -->
<!--<h3 style="font-family: sans-serif;color:red;text-align: center;">{{date('d-F-Y', strtotime(Carbon::createFromDate(Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])->year, Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])->month)->startOfMonth()))}}  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{date('d-F-Y', strtotime(Carbon::createFromDate(Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])->year, Carbon::createFromFormat('Y-m-d H:i:s', $data['created_at'])->month)->endOfMonth()))}} </h3>-->
 <div class="container">
        <table width="100%" >
            <tr>
                <td colspan="3">
                    <table style="width:100%; border:2px black solid">
                        <tr >
                            <td width="20%" style="text-align:center;padding:10px; border: 0px solid white;">
                                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" width = "60%" >
                                
                                </td>
                            <td width="60%" style="border: 0px solid white;">
                                <div>
                                    <div align="center">
                                        <p class="style5"> {{$getSetting['name'] ?? ''}}</p>
                                      
                                        <div><span class="style4"><strong>Address</strong> - {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</span></div>
                                        <div><span class="style4"><strong>Email Id</strong> - {{$getSetting['gmail'] ?? ''}}</span></div>
                                      
                                      <!--  <div class="style4"><strong>Website - </strong></div>
                                        <div class="style4"><strong>Mobile App - </strong> </div>-->
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </td>
                            <td width="20%" style="text-align:center;padding:10px; border: 0px solid white;">
                             <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" width = "60%" >
                                   </td>
                        </tr>
                    </table>
                </td>
            </tr>
        <tr>
            <tr>
    <th colspan="4" style="text-align:center;"><h3><b>Empoloyee Details</b></h3></th>

  </tr>
            <table style="width:100%; border:2px black solid">
  
  <tr>
      <th  rowspan="4" width="15%">
         <img  width="100px" src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data->photo ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" >
                                                </th>
    <th width="32%" style="text-align:left;">Empoloyee Name : {{ $data['User']['first_name'] ?? '' }} {{ $data['User']['last_name'] ?? '' }}</th>
    <th width="32%" style="text-align:left;">Empoloyee ID : {{ $data['employeeId'] ?? '' }}</th>
     <th width="21%" style="text-align:left;"> Gender : {{ $data['gender_id'] ==1  ? 'Male' : '' }} {{ $data['gender_id'] ==2  ? 'Female' : '' }} {{ $data['gender_id'] != 1 || $data['gender_id'] != 2   ? 'Male' : '' }}</th>
   
  
  
    
  
 </tr>
 <tr>
   
       <th  style="text-align:left;">Joining Date :{{date('d-m-Y', strtotime($data['staffJoining_date'])) ?? '' }}</th>
       
  <th style="text-align:left;"> Contact No. : {{ $data['mobile'] ?? '' }}</th>
    <th style="text-align:left;"> Designation : {{ $data['roleName'] ?? '' }} </th>
 </tr>
 <tr>
  <th style="text-align:left;"> Aadhar No. : {{ $data['aadhaar_no'] ?? '' }}</th>
    <th style="text-align:left;"> SSSM.ID. : NA</th>
      <th style="text-align:left;"> Bank Name : {{ $data['b_name'] ?? '' }} </th>
 </tr>
 

  
  <tr>
      
    <th style="text-align:left;"> Account No. : {{ $data['acc_no'] ?? '' }}</th>
 </tr>


</table>
        </tr>

            <tr>
                <td colspan="4">
                    <table width="100%" >
                        <tr>
                            <td>
                                <div>
                                    <div align="center">
                                        <table width="100%" >
                                            <tr>
                                                 <td colspan="4" style="text-align:center;"><h3><b>MONTHLY SALARY SLIP [ {{date('F-Y',strtotime($data->created_at))}} ]</b></h3></td>
                                              
                                              
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    
        <table  style="width:100%; border:2px black solid" >
 
  
 <tr>
  <th > Salary</th>
    <th  > Amount</th>
    <th  > Deducation </th>
    <th  > Amount</th>
   
 </tr>
 <tr>
  <th >  Basic Salary</th>
    <th  > {{ $data['basic_amt'] ?? '' }}</th>
    <th  > PF </th>
    <th  > {{ $data['pf'] ?? '' }}</th>
   
 </tr>
 <tr>
  <th ft;>  DA</th>
    <th  > {{ $data['da'] ?? '' }}</th>
    <th  > TDS </th>
    <th  > {{ $data['tds'] ?? '' }}</th>
   
 </tr>
 <tr>
  <th > HRA</th>
    <th  > {{ $data['hra'] ?? '' }}</th>
    <th  >ESIC </th>
    <th  >{{ $data['esic'] ?? '' }}</th>
   
 </tr>
 <tr>
  <th > Allowance</th>
    <th  > {{ $data['allowance'] ?? '' }}</th>
    <th  >Profession Tax </th>
    <th  >{{ $data['tax'] ?? '' }}</th>
   
 </tr>
 <tr>
  <th > Incentive</th>
    <th  > {{ $data['incentive'] ?? '' }}</th>
    <th  >Other Deducation </th>
    <th  >{{ $data['other_deduction'] ?? '' }}</th>
   
 </tr>


</table>
<br>


<table style="width:100%; border:2px black solid;text-align:center">
 
  
 <tr>
  <th > Total Days(A)</th>
    <th  > Present</th>
    <th >  Absent (B) </th>
    
    <th  > Salary Day (A-B)</th>
   
 </tr>
 <tr>
  <th >   {{ $data['salary_day'] ?? '' }}</th>
    <th  > {{ $data['present'] ?? '' }}</th>
    <th > {{ $data['absent'] ?? '' }} </th>
   
    <th  > {{ $data['total_days'] ?? '' }}</th>
   </tr>
 </table>
                                            
                                             <table style="width:100%; border:2px black solid">
 
  
 <tr>
  <th > Total(Amount)(A) :  RS.{{ $data['total_amount'] ?? '' }}</th>
    <th  > Total Deducation  :  ??</th>
   
 </tr>




</table>
                                           </td>
                                            </tr>
                                        </table>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

        </table>
        <table style="width:100%">
    
   
    
    
  <tr style="border-top: 2px solid;">
         
          </tr>    
    
      <tr>
          <td ><h3><i style="margin-top:40px;position: absolute;">Signature Empoloyee</i></h3>  </td>
          <td style="text-align: right;"><h3 style="padding-right:5%;">
              @if(!empty($getSetting['seal_sign']) )
              <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'"style="width:150px;">
              @endif
              <br>Signature / CO</h3></td>
          </tr>    <br>
  </table>
  
  <table style="width:100%;  border-top: 2px solid">
  
      <tr >
          <td style="text-align: center;">Note: This is computerised copy so no need any Signature.</td>
      </tr>    
  </table>
    </div>

</body>




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
    window.print();
  </script>



</html>
















