@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        table td {
            padding-top: 9px;
         
        }
        body {
            font-family: Arial, sans-serif;
            max-width: 765px;
            margin: 0px auto
        }

        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0px;
        }
        table {
            max-width: 100%;
            width: 100%;
        }
        table td {
            font-size: 16px;
        }
         .toprincipal p{
            margin: 0px;
         }
       
    </style>
</head>

<body>

    <table style="margin: -10px;">
        <thead>
            <table style="margin: 0px;">
                <tr>
                    <td style="padding-top: 58px;">
                        Form No: {{ $data['admissionNo'] ?? '' }}
                    </td>
                    <td style="padding-top: 0px;">
                         <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 115px;height: 100px;margin-top: 24px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >

                    </td>
                    <td>
                    </td>
                </tr>
            </table>
            <table style="margin-top: -7px;">
                <tr>
                    <td colspan="3"
                        style="text-align: center;font-size: 35px;text-decoration: underline;padding-top: 0px;font-weight: bold;">
                       {{$getSetting['name'] ?? ''}}
                    </td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: center;padding-top: 0px;text-transform: capitalize;">
                        {{$getSetting['address'] ?? ''}}
                    </td>
                </tr>
                <tr>
                    <th colspan="3" style="text-decoration: underline;padding-top: 20px;font-size: 22px;">
                        ADMISSION FORM <br>
                    </th>
                </tr>
                <tr>
                    <td style="padding-top: 0px;">(Please write in block letters)</td>
                </tr>
            </table>
        </thead>
        <tbody>
            <table>
                <tr class="feeltr">
                    <td width="18%">Full Name Of Pupil</td>
                    <td style="border-bottom: 3px dotted black;width: 80%;">{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>
                </tr>
            </table>
            <table>
                <tr class="feeltr">
                    <td width="13%">Date Of Birth</td>
                    <td style="border-bottom: 3px dotted black;width: 86%;">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top: 0px;">(In Figures And Words)</td>
                </tr>
            </table>
           
            <table>
                <tr class="feeltr">
                    <td>Nationality</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 20%;">{{ $data['nationality'] ?? '-' }}</td>
                    
                    <td>Religion</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 20%;">{{ $data['religion'] ?? '-' }}</td>
                    
                    <td>Category</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 40%;">{{ $data['category'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="16%">Name Of Father</td>
                    <td style="border-bottom: 3px dotted black;width: 83%;">{{ $data['father_name'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td  width="19%">Father's Occupation</td>
                    <td style="border-bottom: 3px dotted black;width: 79%;">{{ $data['father_occupation'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="18%">Name Of Mother's</td>
                    <td style="border-bottom: 3px dotted black;width: 82%;">{{ $data['mother_name'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="19%">Mother's Occupation</td>
                    <td style="border-bottom: 3px dotted black;width: 78%;">{{ $data['mother_occupation'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="8%">Pan No.</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 36%;"></td>
                    
                    <td style="width: 10%;"></td>
                    
                    <td width="11%">Aadhar No.</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 32%;">{{ $data['aadhaar'] ?? '-' }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="19%">Residential Address</td>
                    <td style="border-bottom: 3px dotted black;width: 79%;">{{ $data['address'] ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="border-bottom: 3px dotted black;width: 100%;padding-top: 20px;" colspan="2"></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="6">Telephone:</td>
                </tr>
                <tr>
                    <td width="6%">Office</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 25%;"></td>
                    
                    <td width="8%">Residence</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 24%;"></td>
                    
                    <td width="6%">Mobile</td>
                    <td style="border-bottom: 3px dotted black;width: 26%;">{{ $data['mobile']  }}</td>
                </tr>
            </table>
            <table>
                <tr>
                    <td width="47%">Last School Attendent, If Any, With Transfer Certificate</td>
                    
                    <td style="border-bottom: 3px dotted black;width: 44%;"></td>
                </tr>
                <tr>
                    <td style="border-bottom: 3px dotted black;width: 100%;padding-top: 20px" colspan="2"></td>
                </tr>
            </table>
            <table class="toprincipal">
                <tr>
                    <td >
                        <p>To,</p>
                        <p>The Principal,</p>
                        <p>Gyandeep Public School,</p>
                        <p>Jaipur,</p>
                        <p>I want to say my Son/Daughter Admitted to your School. Kindly register His/Her name in Class....
                        </p>
                        <p>I have understood the rules and regulations of the school and promise to comply with them and ensure that my child also conforms to the standard required if Him/Her in conduct and studies.</p>
                        <p>I agree to pay Dues and School Fees as per the rules. If I withraw my Child without Notice, I shall pay One Month Tution Fees in advance.</p>
                    </td>
                </tr>
            </table>
            <table >
                <tr>
                    <td>Date</td>
                    <td style="border-bottom: 3px dotted black;width: 25%;" ></td>
                    <td style="width: 30%;"></td>
                    <td width="57%" style="text-align: end;">Signature & Name of Parent/Guardian</td>
                </tr>
                <tr>
                    <th colspan="4">(For Office Use)</th>
                </tr>
            </table>
            <table >
                <tr>
                    <td width="28%"></td>
                    <td  width="17%" ></td>
                    <td width="16%"></td>
                    <td  width="16%"></td>
                    <td width="15%"></td>
                    <td style="text-align: center;">
                       <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" style="height:90px;margin-bottom: -13px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'">
                    </td>
                </tr>
                <tr>
                    <td width="11%">Admitted to class & sec.</td>
                    <td style="border-bottom: 3px dotted black;width: 20%;" >
                        {{ $data['ClassTypes']['name'] ?? '-' }}
                        @if (!empty($data['Section']['name']))
                            ({{ $data['Section']['name'] }})
                        @endif
                    </td>
                    <td width="15%">Admission No.</td>
                    <td style="border-bottom: 3px dotted black;width: 15%;">{{ $data['admissionNo'] ?? '' }}</td>
                    <td  style="width: 20%;"></td>
                    <td style="text-align: center;"><b>Principal</b></td>
                </tr>
            </table>
    </table>
    </tbody>
    </table>


</body>

</html>