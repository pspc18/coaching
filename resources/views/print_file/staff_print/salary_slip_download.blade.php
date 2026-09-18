

<button id="btn" onload="myFunction()" class="btn btn-secondary btn1" style="visibility:hidden">Download</button>
<div class="row" id="capture">
@php
$getSetting=Helper::getSetting();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    
    <title>Salary-Slip</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">   
    <style>
    .footer_p {
            margin: -14px 0px 2px;
    }
    .image {
        margin-bottom: -15px;
    }
        table{
           width: 100%;
          border-collapse: collapse;
        }
    
        .w  {
            width: 100%;
        }
        .th{font-size: 60px;
            color: red;
          font-family: fangsong;
      }
     
       .th2{
        color: rgb(7, 105, 202);
        font-size: 32px;
       }
        td {
          border: 1px solid black;
        }
        .table2{
            width: 100%;
        }
        .a{
            text-align: right;
            padding-right: 8px;
        }
        .q{
            margin-left: 63%;
        }
        .b{
            padding-left: 8px;
        }
        .c{
            background-color: aqua;
        }
        .d{
            border-left: hidden;
    border-right: hidden;
        }
        .e{
            border-left: hidden;
            border-right: hidden;
        }   
        .f{
        text-align:right;
        margin-bottom:-14px;
        }
        .g{
            text-align:right;
        }
        .p{
            border-bottom: 6px solid #9d6e05;
    width: 100%;
        }
        .i{
            border-top: 1px solid;
            border-right: 1px solid;
            width: 16%;
        }
        .i2{
            border-right: 1px solid;
        }
        .i3{
            padding-left: 6px;
            text-align: left;
        }
        .span{
            margin-left: -14%;
        }

        @media  (max-width:600px) {
  
  .th{
    font-size: 100%;
  }
  
  img{
    width: 65%;
  }
  
  .tablep{
  width: 100%;
  }

  .g{
    width: 19%;
    margin-left: 85%;
    text-align: right;
  }
  .f{
    margin-left: 38%;
  } 
  #v{
    margin-left: -16%;
  }   
  }
  .w{
    width: 9%;
  }
    .font{
        font-size: 25px;
    }
    .hidden{
        visibility: hidden;
    }
body{
    background-image: url('{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}');
    background-size:50% 30%;
    background-position: center;
    background-repeat: no-repeat;
}
         </style>
</head>
<body>
    
    <table class="tablep">
        <thead>
            <tr>
            <th colspan="2"><img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="margin-left: -69%; width: 17%;"></th>
            <th colspan="10"><span class="th" style="margin-left: -70%">Rukmani Software Infotech Pvt. Ltd. </span><br>
                <span class="span" style="margin-left: -83%;">PAN NO:AAHCT5477G</span><br>
            <span class="span" style="margin-left: -83%;">GSTIN NO:08AAHCT5477G1ZX</span></th><br>
            </tr>
            <tr>
                <th class="hidden">r</th>
            </tr>
            <tr>
                <th colspan="">Date: {{date('d-m-Y', strtotime($data['date'])) ?? '' }}</th>
                <th colspan="10"><ins class="span font">EMPOLOYEE SALARY/PAY-SLIP</ins><br><br><ins class="span font">Empoloyee Details
                </ins></th>
            </tr>
            <tr>
                <th class="hidden">
                    r
                </th>
            </tr>
            <tr>
                <td class="c i i3 w"><b>Emp.Name</b></td>
                <td class="i i3"><b>{{ $data['User']['first_name'] ?? '' }} {{ $data['User']['last_name'] ?? '' }}</b></td>
                <td class="c i i3 w"><b>Emp.ID</b></td>
                <td class="i i3">{{ $data['employeeId'] ?? '' }}</td>
            </tr>
        </thead>

            <tbody>
                <tr>
                    <td class="c i3"><b>Gender</b></td>
                    <td class="i3">Male</td>
                     <td class="c i3"><b>Joining date </b></td>
                    <td class="i3">{{date('d-m-Y', strtotime($data['staffJoining_date'])) ?? '' }}</td>
                   
                </tr>
                <tr>
                    <td class="c i3"><b> Contact No.</b></td>
                    <td class="i3">+91-{{ $data['User']['mobile'] ?? '' }}</td>
                    <td class="c i3"><b>Designation</b></td>
                    <td class="i3">{{ $data['roleName'] ?? '' }}</td>
                </tr>
                <tr>
                        <td class="c i3"><b>Aadhar No</b></td>
                        <td class="i3">{{ $data['aadhaar_no'] ?? '' }}</td>
                        <td class="c i3"><b> SSSM.ID.</b></td>
                        <td class="i3">NA</td>
                    </tr>
                    <tr>
                        <td class="c i3"><b>Bank Name</b></td>
                        <td class="i3">{{ $data['bank_name'] ?? '' }}</td>
                        <td class="c i3"><b>Account No.</b></td>
                        <td class="i3">{{ $data['account_no'] ?? '' }}</td>
                    </tr>
               <!-- <tr>
                    <td class="c i3"><b>PAN</b></td>
                    <td class="i3">EKAPK9678C</td>
                    <td class="c i3"><b>Salary Month</b></td>
                    <td class="i3"><b>{{ $data['month_id'] ?? '' }}</b></td>
                </tr>
                
                    <td class="c i3"><b>Mobile</b></td>
                    <td class="i3">+91-9166071543</td>
                    <td class="c i3"><b>job.Location</b></td>
                    <td class="i3">Jaipur Branch</td>
                </tr>
                 
                    <tr>
                        <td class="c i3"><b>ESIC NO.</b></td>
                        <td class="i3">N.A.</td>
                        <td class="c i3"><b>PF No.</b></td>
                        <td class="i3">N.A.</td>
                    </tr>-->
                    
                 <!--   <tr>
                        <td class="c i3"><b>Reg.Mobile</b></td>
                        <td class="i3">+91-{{ $data['User']['mobile'] ?? '' }}</td>
                        <td class="c i3"><b>Reg. Email</b></td>
                        <td class="i3">{{ $data['email'] ?? '' }}</td>
                    </tr>-->
                    
                

            </tbody>
           
            
        
    </table><br><br>
    
    <table class="table2">
        <thead>
            <p><b><ins style="font-size: 25px;margin-left: 46%;">Salary Description</ins></b></p>
            <tr>
                <td class="c i2"><b>Sr. No</b></td>
                <td class="c i2"><b>Description of service</b></td>
                <td class="c i2"><b>Day/Qnty.</b></td>
                <td class="c i2"><b>Rate</b></td>
                <td class="c i2"><b>Amount</b></td>
            </tr>
        </thead>
        <tbody>
           <tr>
            <td class="a">1</td>
            <td class="b">Basic salary(@ {{ $data['basic_amt'] ?? '' }})</td>
            <td class="a">{{ $data['salary_day'] ?? '' }}</td>
            <td class="a">0.00</td>
            <td class="a">{{ $data['basic_amt'] ?? '' }}</td>
         </tr>
         <tr>
            <td class="a">2</td>
            <td class="b">Present</td>
            <td class="a">{{ $data['present'] ?? '' }}</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">3</td>
            <td class="b">Absent</td>
            <td class="a">{{ $data['absent'] ?? '' }}</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">4</td>
            <td class="b">DA</td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">5</td>
            <td class="b"> TDS</td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">6</td>
            <td class="b">HRA</td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">7</td>
            <td class="b">ESIC</td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
          
         </tr>
         <tr>
            <td class="a">8</td>
            <td class="b">Allowance</td>
            <td class="a" >NA</td>
             <td class="a">0.00</td>
            <td class="a">0.00</td>
          
         </tr>
         <tr>
            <td class="a">9</td>
            <td class="b">Profession Tax </td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a">10</td>
            <td class="b">Incentive</td>
            <td class="a">NA</td>
            <td class="a">0.00</td>
            <td class="a">0.00</td>
         </tr>
         <tr>
            <td class="a"></td>
            <td class="b e"></td>
            <td class="a e"></td>
            <td class="a">Total</td>
            <td class="a">{{ $data['total_amount'] ?? '' }}</td>
         </tr>
   <!--      <tr>
           
            <td colspan="4"class="a v" style="font-size: 23px;">Income Tax @5.00%</td>
            <td class="a">-</td>
         </tr>-->
         <tr style="border-bottom:hidden;">
         
            <td colspan="4"class="a"><b>Grangt Total</b></td>
            <td class="a">{{ $data['total_amount'] ?? '' }}</td>
         </tr>
       <!--  <tr>
            <td class="c"><b>Amount in Words:-</b></td>
            <td>Six Thounsant Nine Hunderd Thirty Three Rupees and Three Two Paisa Only</td>
            <td class="e"></td>
            <td class="e"></td>
            <td ></td>
         </tr>
         <tr>
            <td class="c"><b>Mode of Payment:</b></td>
            <td>Direct A/C Transfer</td>
            <td class="e"></td>
             <td class="e"></td>
             <td></td>
         </tr>-->
         <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
         </tr>
        </tbody>
    </table>

    <table class="k">
        <tbody>
           <p style="text-align: left;"><span><b>Term & Condition:- </b></span> <br>&nbsp;&nbsp;&nbsp;&nbsp;1.&nbsp;&nbsp; This salary /pay slip to be consider with Bank Salary Transaction/ cheque/ provided by company.<br>
            &nbsp;&nbsp;&nbsp;&nbsp;2. &nbsp;&nbsp;This is computerized pay slip so No need Seal & Sign.<br>  </p>
     
            <p class="f"><b class="f">For Rukmani Software Infotech Pvt. Ltd.</b></p>

            <p  class="g"><img class="image" src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="width:16%;"></p>

            <p class="p g" > <b>Director's Signature</b><br><br><br><br></p>
            <p class="footer_p"><span><i>1<sup>st</sup>Floor,Royal Plazz,F-42, Vidyadhar,Nagar,Jaipur,Rajasthan 302032</span><a href="mailto://{{ $getSetting['gmail'] ?? '' }}"><span style="margin-left: 47%;">{{ $getSetting['gmail'] ?? '' }}</span></i></a>
                <br><i>+91-9772625256/ +91-6376414391<a href="https://www.rukmanisoftware.com" target="blank" class="q">www.rukmanisoftware.com</i></a></i></p>
    </tbody>
      </table>
</body>

</html>

</div>
 <script type="text/javascript">
  
    function myFunction() {

  const captureElement = document.querySelector('#capture')
  html2canvas(captureElement)
    .then(canvas => {
      canvas.style.display = 'none'
      document.body.appendChild(canvas)
      return canvas
    })
    
    
    .then(canvas => {
      const image = canvas.toDataURL('image/png').replace('image/png', 'image/octet-stream')
            var pdf = new jsPDF("p", "pt", "letter");
var width = pdf.internal.pageSize.getWidth();
var height = pdf.internal.pageSize.getHeight();
            pdf.addImage(image, 'JPEG', 0, 0, width, height);

            pdf.save('Salary Slip.Rukmanisoft.pdf');
            
     
     // const a = document.createElement('a')
      
     // a.setAttribute('download', 'my-pdf.png')
    //  a.setAttribute('href', image)
    //  a.click()
      canvas.remove()
    })
}
window.onload = function(){
  document.getElementById('btn').click();
  var scriptTag = document.createElement("script");
scriptTag.src = "https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js";
document.getElementsByTagName("head")[0].appendChild(scriptTag);
}
const btn = document.querySelector('#btn')
btn.addEventListener('click', myFunction)
</script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.4.1/jspdf.debug.js'></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<!--<script type="text/javascript">
window.print();
</script>-->