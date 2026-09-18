@php
$setting = Helper::getSetting();


@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f4;
        }

        .payslip {
            width: 1000px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid black;

        }

        header .logo {
            width: 165px;
  height: 150px;
  display: flex;
  align-items: center;

        }



        .company-details h1 {
            margin: 0;
        }

        .employee-details,
        .salary-details {
            margin-top: 20px;
        }

        .employee-details table,
        .salary-details table,
        .net-pay table {
            width: 100%;
            border-collapse: collapse;
        }

        .employee-details td,
        .salary-details th,
        .salary-details td,
        .net-pay td {
            border: 2px solid black;
            text-align: left;
            font-size: 14px;
            padding: 3px;
        }


        .salary-details thead {
            background: #f4f4f4;
        }

        .net-pay {
            margin-top: 20px;
            text-align: right;
            font-size: 1.2em;
        }

        .pay {
            font-size: 0.9em;
            margin-top: 50px;
        }

        .seal {
            text-align: center;
            width: 130px;
        }

        footer {
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>


@php
$total_gross = $data['basic_amt'] ?? 0;
$total_gross_after = ($data['basic_amt']/$data['total_days'])*$data['salary_day'];
$total_deduction = ($data['tds'] ?? 0)+($data['pf'] ?? 0)+($data['other_deduction'] ?? 0);
$total_Reimbursement = $data['incentive'] ?? 0;

@endphp
<body>
    <div class="payslip">
        <header>
            <div class="company-details">
                <h1>{{ $setting->name ?? '' }}</h1>
                <p>{{ $setting->address ?? '' }}</p>
            </div>
            <div class="logo">
                <img width="150px" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$setting['left_logo'] }}"  alt="logo">
            </div>

        </header>
        <section class="employee-details">
            <h2 style="text-align: center;">Payslip for the month of {{ date('F', strtotime($data['date']))}}, {{ date('Y') }}</h2>
            <table class="salary-details">
                <thead >
                    <tr>
                        <td colspan="2"><strong>Employee Pay Summary</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </thead>
                <tr>
                    <td>Employee Name</td>
                    <td>{{ $data['name'] ?? 'NA' }}</td>
                    <td>PAN</td>
                    <td>{{ $data['staffpan_no'] ?? 'NA' }}</td>
                </tr>
                <tr>
                    <td>Designation</td>
                    <td>{{ $data['staffDesignation'] ?? 'NA' }}</td>
                    <td>Bank Name</td>
                    <td>{{ $data['b_name'] ?? 'NA' }}</td>
                </tr>
                <tr>
                    <td>Employee ID</td>
                    <td>Employee_{{ $data['employeeId'] ?? 'NA' }}</td>
                    <td>Bank A/C No.</td>
                    <td>{{ $data['acc_no'] ?? 'NA' }}</td>
                </tr>
                <tr>
                    <td>Date of Joining</td>
                    <td>@if(!empty($data['staffJoining_date'])) {{ date('d-M-Y',strtotime($data['staffJoining_date'])) }} @else NA @endif</td>
                    <td>P.F. A/C Number</td>
                    <td>NA</td>
                </tr>
                <tr>
                    <td>Department</td>
                    <td>IT</td>
                    <td>UAN Number</td>
                    <td>NA</td>
                </tr>
                <tr>
                    <td>Location</td>
                    <td>{{ $data['staffAddress'] ?? 'NA' }}</td>
                    <td>Days Worked</td>
                    <td>{{ $data['salary_day'] ?? 'NA' }}</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td>Pay Date(dd-mm-yyyy)</td>
                    <td>{{ date('d-M-Y') }}</td>
                </tr>
            </table>
        </section>
        <section class="salary-details">
            <table>
                <thead>
                    <tr>
                        <th>EARNINGS</th>
                        <th>Earnings</th>
                        <th>Particular</th>
                        <th>Deductions</th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic</td>
                        <td>INR {{ (Int)$data['basic_amt']}}</td>
                        <td>Income Tax Deduction</td>
                        <td>INR {{ (Int)$data['tds'] ?? '0' }}</td>
                    </tr>
                    <tr>
                        <td>DA</td>
                        <td>INR {{ (Int)$data['da'] ?? '' }}</td>
                        <td>{{ $data['deduction_remark'] ?? 'Other Deduction'}}</td>
                        <td>INR {{ (Int)$data['other_deduction'] ?? '0'}}</td>
                    </tr>
                    <tr>
                        <td>Incentive</td>
                        <td>INR {{ (Int) $data['incentive'] ?? '' }}</td>
                        <td>P.F.</td>
                        <td>INR {{ (Int)$data['pf'] ?? '0' }}</td>
                    </tr>
                    
                    
                </tbody>
                <tfoot>
                    <tr>
                        <td><strong>Gross Earnings</strong></td>
                        <td><strong>INR {{(Int)$data['basic_amt']+(Int)$data['da']+(Int)$data['incentive']}}</strong></td>
                        <td><strong>Total Deductions</strong></td>
                        <td><strong>INR {{(Int)$total_deduction}}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </section>
        <section class="net-pay">
            <table class="reimbursements salary-details">
                <thead>
                    <tr>
                        <td colspan="6"><strong>Attendence</strong></td>
                    </tr>
                </thead>
                <tr>
                    <th style="text-align: center;">Total Days(Days)</th>
                    <th style="text-align: center;">Present(Days)</th>
                    <th style="text-align: center;">Absent(Days)</th>
                    <th style="text-align: center;">Holiday(Days)</th>
                    <th style="text-align: center;">HalfDay(Days)</th>
                    <th style="text-align: center;">DoubleShif(Days)</th>
                </tr>
                <tr>
                    <td style="text-align: center;">{{$data['total_days'] ?? '0' }}</td>
                    <td style="text-align: center;">{{$data['present'] ?? '0' }}</td>
                    <td style="text-align: center;">{{$data['absent'] ?? '0' }}</td>
                    <td style="text-align: center;">{{$data['holiday'] ?? '0' }}</td>
                    <td style="text-align: center;">{{$data['half_day'] ?? '0' }}</td>
                    <td style="text-align: center;">{{$data['double_shift'] ?? '0' }}</td>
                </tr>
               
            </table>

        </section>
        <section class="net-pay">
            <table class="salary-details">
                <thead>
                    <tr>
                        <td><strong>NETPAY</strong></td>
                        <td><strong>AMOUNT</strong></td>
                    </tr>
                </thead>
                <tr>
                    <td>Gross Earnings</td>
                    <td>INR {{(Int)$data['basic_amt']+(Int)$data['da']+(Int)$data['incentive']}}</td>
                </tr>
                <tr>
                    <td>Total Deductions</td>
                    <td>INR {{(Int)$total_deduction}}</td>
                </tr>
                <tr>
                    <td><strong>Total Net Payable</strong></td>
                    <td><strong>INR {{(Int)$data['basic_amt']+(Int)$data['da']+(Int)$data['incentive']- (Int)$total_deduction}}</strong></td>
                </tr>
            </table>
        </section>
        <footer>
            
            @php
                 $formatter = new NumberFormatter('en_IN', NumberFormatter::SPELLOUT);
                $words = $formatter->format((Int)$data['basic_amt']+(Int)$data['da']+(Int)$data['incentive']- (Int)$total_deduction);
                $words .= ' rupees';
       @endphp
            <div class="pay">
                <p><strong style='text-transform:capitalize'>Total Net Payable INR {{(Int)$data['basic_amt']+(Int)$data['da']+(Int)$data['incentive']- (Int)$total_deduction}} ({{$words}})</strong></p>
                <p>**Total Net Payable = Gross Earnings - Total Deductions</p>
            </div>
            <div class="seal">
                <img width="60px" src="{{asset('rukmaniImage/logo/school_seal.png')}}" alt="">
                <p><strong>Signature & CEO</strong></p>
            </div>
        </footer>
    </div>
</body>
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
</html>