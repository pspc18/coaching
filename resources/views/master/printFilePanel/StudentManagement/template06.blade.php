@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Student Id Card</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
      @page {
            size: A4;
            width: 100%;
            margin: 0;
        }

        @media print {
            .page {
                scale: 0.90;
            }
            
        }
        .page {
                    width: min-content;
                    position: relative;
    width: 400px;
    height: 260px;
    border-radius: 15px;
    overflow: hidden;
    text-align: center;
    margin-top: 27px;
    border: 2px solid;
    margin: 10px auto;
    

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
  </style>
</head>
<body>

                <div style="display: flex;font-family: sans-serif;" class="page">
                    <div>
                        <!--<p style="margin: 0;">-->
                            <!--<img src="idcardheder.jpg" alt="" width="400px">-->
                        <!--    <img class="logo_size" src="{{ env('IMAGE_SHOW_PATH').'/default/id_card_header.png' }}"-->
                        <!--        onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"-->
                        <!--        width="360px">-->
                        <!--</p>-->
                        <div style="background: darkgray;">
            <div class="header">
                <div class="header-top">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" class="school-logo" />
                    <div class="school-name">
                        <h1>{{$getSetting['name'] ?? ''}}</h1>
                    </div>
                </div>
            </div>
        </div>
                        <h5 style="margin: 0;text-align:center;">Identity Card 2024-2025</h5>
                        <div style="display: flex;">
                            <table style="width: 80%;height: 90px;font-size:14px;">
                                <tbody>
                                    <tr>
                                        <td>Name</td>
                                        <td style="text-transform: uppercase;">: {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</td>

                                    </tr>
                                    <tr>
                                        <td>Father</td>
                                        <td style="text-transform: uppercase;">: {{ $data['father_name'] ?? '' }}</td>

                                    </tr>
                                    <tr>
                                        <td>Class</td>
                                        <td style="text-transform: uppercase;">: {{ $data['class_name'] ?? '' }}</td>

                                    </tr>
                                    <tr>
                                        <td>DOB</td>
                                        <td>: {{date('d-M-Y', strtotime($data['dob'])) ?? '' }}</td>

                                    </tr>
                                    <tr>
                                        <td>Phone</td>
                                        <td>: {{ $data['mobile'] ?? '' }}</td>

                                    </tr>
                                    <br>
                                    <br><br>
                                    <tr>
                                        <td style="align-content: baseline;">Address</td>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td style="font-size:14px;">
                                                        : {{ strlen($data['address'] ?? '') > 25 ? substr($data['address'], 0, 25) . '...' : ($data['address'] ?? '') }}
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="text-align:start;">
                              
                                    <img class="img_bwm" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" width="70px" height="90px" >
                                <div >
                                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}"
                                        onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'"
                                        width="80px" height="25px">
                                </div>
                                <div>
                                    <b>Director</b>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
         
               
            </div>
     

</body>
</html>
