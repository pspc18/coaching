@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Student ID Card</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            /*height: 100vh;*/
            background-color: #f0f0f0;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .id-card {
            position: relative;
            width: 400px;
            height: 260px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            background: linear-gradient(180deg, transparent 54%, #6495ed);
            background-color: #fff;
            text-align: center;
            margin-top: 27px;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("background-image.jpg");
            background-size: cover;
            opacity: 0.2;
            z-index: -1;
        }

        .header {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 10px;
            color: black;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }

        .school-logo {
            width: 65px;
            height: 65px;
            border-radius: 10%;
        }

        .school-name {
            flex-grow: 1;
            text-align: center;
            margin: -21px;
            color: #0b4191;
            ;
        }

        .school-name h1 {
            font-size: 27px;
            font-weight: 800;
            line-height: 31px;
        }

        .school-address {
            margin-top: -23px;
            text-align: center;
            line-height: 1.2;
            margin-left: 0%;
            background-color: orange;
            /* Reduce line height */
        }

        .school-address p {
            color: #0b4191;
            font-size: 14px;
        }

        .mid_part {
            display: flex;
            margin-top: -12px;
        }

        .photo-container {
            margin-left: 11px;
        }

        .student-photo {
            width: 87px;
            height: 87px;
            border: 2px solid black;
        }

        .student-info {
            padding: 0 20px;
        }

        .student-info table {
            width: 100%;
            margin: 0 auto;
            text-align: left;
        }

        .student-info td {
            font-size: 14px;
            color: #333;
        }

        .student-info strong {
            color: black;
        }

        .signature img {
            width: 77px;
            position: absolute;
            bottom: 18px;
            left: 18px;
        }


        .footer {
            padding: 10px;

            position: absolute;
            bottom: -5px;
            width: 100%;
            display: flex;
            justify-content: space-around;

        }
        .footer p {
            color: white;
            margin: 0px 9px -10px 22px;
            font-size: 12px;
            padding: 3px 15px 7px 41px;
            border-radius: 8px 0px 0px 0px;
        }

        /*.footer p {
            color: white;
            margin: 0px 0px -6px 22px;
            font-size: 12px;
            padding: 2px 30px 7px 41px;
            border-radius: 8px 0px 0px 0px;
        }*/

        .footer_number {
            background-color: #0b4191;
        }
    </style>
</head>

<body>
    <div class="id-card">
        <div class="background-image"></div>
        <div style="background-color: cornflowerblue;">
            <div class="header">
                <div class="header-top">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" class="school-logo" />
                    <div class="school-name">
                        <h1>{{$getSetting['name'] ?? ''}}</h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="school-address">
            <p><strong>{{$getSetting['address'] ?? ''}}</strong></p>
        </div>


        <div class="mid_part">
            <div class="photo-container">
                <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" class="student-photo" />
            </div>
            <div class="student-info">
                <table>

                    <tr>
                        <td><strong>Name:</strong></td>
                        <td colspan="3">{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>

                    </tr>
                    <tr>
                        <td><strong>Father's:</strong></td>
                        <td colspan="3">{{ $data['father_name'] ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>D.O.B:</strong></td>
                        <td width="99px">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
                        <td><strong>Class:</strong></td>
                        <td>{{ $data['ClassTypes']['name'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Mobile:</strong></td>
                        <td colspan="3">{{$data['mobile'] ?? '-'}}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td colspan="3">{{ strlen($data['address'] ?? '') > 25 ? substr($data['address'], 0, 25) . '...' : ($data['address'] ?? '') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="signature">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" />
        </div>
        <div class="footer">
            <p style="color: #454571;margin-left: -30px;">Principal Signature</p>
            <p class="footer_number"><strong>SCHOOL MO.NO. : </strong> {{$getSetting['mobile'] ?? ''}}</p>
            <!-- <p><strong>Website:</strong> www.schoolwebsite.com</p> -->
        </div>
    </div>
</body>

</html>