@php
use Carbon\Carbon;
$getSetting = Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <style>
     
     body {
   font-family: Arial, sans-serif;
      font-size:12px;
      max-width:750px;
      margin: 25px auto ;
   /* border: 0.5px solid; */
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
       top: 80px;
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

   table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px;
        }
        .inner_table th{
            border: 1px solid #000;
            padding: 5px;
            /* background-color: #f2f2f2; */
        }
      
        .invoice-header {
            margin-bottom: 20px;
            text-align:'center'
        }
        .inner_table td{
            padding:5px
        }
        .ltr{
            text-align: left;
            border-right: none !important;
        }
        .rtr{
            text-align: right;
        }
   </style>
</head>
<body class='page'>
<table>
			<tbody >
					<tr>
      <td rowspan='2' width='25%'>
          <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/rukmanisoft_logo.png' }}'" style="width: 150px;" >
          </td>
      <td   width='50%' style="font-size:20px;text-align:center;"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
      <td width='25%'> 
      </td>
    
   </tr>
	<tr style="text-align:center;">
      <td width='50%'  style="text-align:center;">
      <p ><b >Address </b> {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</p>
      <p ><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}</p>
    </td>
      <td width='25%'></td>
      </tr>

  </tbody>
  </table> 
  <p style='text-align:center; font-weight:900;line-height:20px;margin-top:35px'>

Payslip for the month of {{date('F-Y',strtotime($data->created_at))}}

</p>
  <table >
      	<tbody >
      <tr>
        <td style='border:1px solid black;width:50%'>
        <table class='inner_table'>
      	<tbody >
            <tr >
                
            <td>Employee Name:</td>
            <td>{{ $data['User']['first_name'] ?? '' }} {{ $data['User']['last_name'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Contact No:</td>
            <td>{{ $data['mobile'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Gender:</td>
            <td>{{ $data['gender_id'] ==1  ? 'Male' : '' }} {{ $data['gender_id'] ==2  ? 'Female' : '' }} {{ $data['gender_id'] != 1 || $data['gender_id'] != 2   ? 'Male' : '' }}</td>
            </tr>
            <tr>

            <td>Aadhar No:</td>
            <td>{{ $data['aadhaar_no'] ?? '' }}</td>
            </tr>
            

    </tbody>
    </table>

        </td>
        <td style='border:1px solid black;width:50%'>
       
        <table class='inner_table' >
      	<tbody >
            <tr>
                
            <td>Employee ID:</td>
            <td>{{ $data['employeeId'] ?? '' }}</td>
            </tr>
            <tr>

<td>Joining Date:</td>
<td>{{date('d-m-Y', strtotime($data['staffJoining_date'])) ?? '' }}</td>
</tr>
            <tr>

            <td>SSSM.ID:</td>
            <td>NA</td>
            </tr>
            <tr>

<td>Bank Name:</td>
<td>{{ $data['b_name'] ?? '' }}</td>
</tr>
            <tr>

<td>Account No:</td>
<td>{{ $data['acc_no'] ?? '' }}</td>
</tr>

    </tbody>
    </table>

        </td>
         
      </tr>
    
       </tbody>
  </table>



  <table style ='margin-top:20px' >
      	<tbody >
      <tr>
        <td style='border:0px solid black;width:50%'>
        <table class='inner_table'>
        <thead>
            <tr>
                <th class='ltr' >Earnings</th>
                <th class='rtr'>Amount</th>
               
              
            </tr>
        </thead>
      	<tbody style='border:1px solid black'>
            <tr >
                
            <td>Basic:</td>
            <td class="rtr">Rs. {{ $data['basic_amt'] ?? '' }}</td>
            </tr>
            <tr>

            <td>DA:</td>
            <td class="rtr">Rs. {{ $data['da'] ?? '' }}</td>
            </tr>
            <tr>

            <td>HRA:</td>
            <td class="rtr">Rs. {{ $data['hra'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Allowance:</td>
            <td class="rtr">Rs. {{ $data['allowance'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Incentive:</td>
            <td class="rtr">Rs. {{ $data['incentive'] ?? '' }}</td>
            </tr>
            <tr>

            <td>&nbsp;</td>
            <td>&nbsp;</td>
            </tr>
           

    </tbody>
    </table>

        </td>
        <td style='border:0px solid black;width:50%'>
       
        <table class='inner_table' >
        <thead>
            <tr>
                <th class='ltr'>Deduction</th>
                <th class='rtr'>Amount</th>
               
              
            </tr>
        </thead>
      	<tbody style='border:1px solid black' >
            <tr >
                
            <td>PF:</td>
            <td class="rtr">Rs. {{ $data['pf'] ?? '' }}</td>
            </tr>
            <tr>

            <td>TDS:</td>
            <td class="rtr">Rs. {{ $data['tds'] ?? '' }}</td>
            </tr>
            <tr>

            <td>TDS:</td>
            <td class="rtr">Rs. {{ $data['tds'] ?? '' }}</td>
            </tr>
            <tr>

            <td>ESIC:</td>
            <td class="rtr">Rs. {{ $data['esic'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Profession Tax:</td>
            <td class="rtr">Rs. {{ $data['tax'] ?? '' }}</td>
            </tr>
            <tr>

            <td>Other Deduction:</td>
            <td class="rtr">Rs. {{ $data['other_deduction'] ?? '' }}</td>
            </tr>

    </tbody>
    </table>

        </td>
         
      </tr>
    
       </tbody>
  </table>


  <table style='text-align:center;border:1px solid black;margin-top:20px'>
      	<tbody >
      <tr>
<td>Total Days</td>
<td>Present</td>
<td>Absent</td>
<td>Salary Day</td>

    </tr>
      <tr>
<td>{{ $data['salary_day'] ?? '' }}</td>
<td>{{ $data['present'] ?? '' }}</td>
<td>{{ $data['absent'] ?? '' }}</td>
<td>{{ $data['total_days'] ?? '' }}</td>

    </tr>

    </tbody>

    </table>

  <p style='border-bottom:1px solid lightgrey; padding-bottom:20px'>Net Pay for the month: Rs. {{ $data['total_amount'] ?? '' }}</p>


<p style='text-align:center; line-height:20px;margin-top:35px'>

Generated By Rusoft</br>
for more details, log on to <a href='https://school.rusoft.in'>www.school.rusoft.in</a>

</p>


 



</body>
</html>

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