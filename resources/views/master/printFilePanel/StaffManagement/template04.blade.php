@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Teacher ID Card</title>
    <link rel="stylesheet" href="styles.css" />
    <style>
      body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f0f0f0;
        margin: 0;
        font-family: Arial, sans-serif;
      }

      .id-card {
        position: relative;
        width: 300px;
        height: 465px;
        /* Height to accommodate all fields */
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        background-color: #fff;
        text-align: center;
        background: linear-gradient(180deg, transparent 50%, #f898a9);
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
        padding: 7px;
        color: darkred;
      }

      .header-top {
        display: flex;
        justify-content: space-between;
        width: 100%;
        align-items: center;
      }

.school-logo {
    width: 80px;
    height: 64px;
    border-radius: 10%;
    margin-top: -10px;
}
      .school-name {
        flex-grow: 1;
        text-align: center;
        font-size: 13px;
        margin-left: -14px;
        color:#701828;
      }

      .school-address {
        margin-top: -20px;
        text-align: center;
        line-height: 1.2;
        /* Reduce line height */
      }
      .school-address p {
            background-color: #701828;
            color: white;
            /* width: 110%; */
            height: 27px;
            font-size: 11px;
            padding: 5px 3px 0px 3px;
            margin-bottom: 5px;
      }

      .photo-container {
        margin: 0px 0;
        margin-top: -9px;
      }

      .student-photo {
        width: 108px;
        height: 129px;
        border: 1px solid black;
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
        padding: 3px 0;
        font-size: 14px;
        color: #2c2828;
        font-weight: 600;
      }

      .student-info strong {
        color: black;
      }
      .signature img {
            width: 58px;
            position: absolute;
            bottom: 23px;
            left: 27px;
      }

      .footer {
        padding: 10px;
        /*border-top: 1px solid #e0e0e0;*/
        position: absolute;
        bottom: 0;
        width: 100%;
        display: flex;
        justify-content: space-evenly;
        background-color: #701828;
      }

      .footer p {
        color: white;
        margin: -6px 0;
        font-size: 12px;
      }
    </style>
  </head>

  <body>
    <div class="id-card">
      <div class="background-image"></div>
      <div class="header">
        <div class="header-top">
          <img
            src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" class="school-logo"/>
          <div class="school-name">
            <h2>{{$getSetting['name'] ?? ''}}</h2>
          </div>
        </div>
        <div class="school-address">
          <p>{{$getSetting['address'] ?? ''}}</p>
        </div>
      </div>
      <div class="photo-container">
        <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['photo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" class="student-photo" />
      </div>
      <div class="student-info">
        <table>
          <!-- <tr>
                    <td style="width: 40%"><strong>Admission No:</strong></td>
                    <td>123456789</td>
                </tr> -->
          <tr>
            <td><strong>Name:</strong></td>
            <td>{{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}}</td>
          </tr>
          <tr>
            <td><strong>Father's:</strong></td>
            <td>{{$data['father_name'] ?? ''}}</td>
          </tr>
          <tr>
            <td><strong>D.O.B:</strong></td>
            <td>{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>
          </tr>
          <tr>
            <td><strong>D.O.J:</strong></td>
            <td>{{date('d-m-Y', strtotime($data['joining_date'])) ?? '' }}</td>
          </tr>
          <tr>
            <td><strong>Mobile:</strong></td>
            <td>{{$data['mobile'] ?? ''}}</td>
          </tr>
          <tr>
            <td><strong>Address:</strong></td>
            <td>{{$data['address'] ?? ''}}</td>
          </tr>
        </table>
      </div>
      <div class="signature">
        <img
          src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'"/>
      </div>
      <div class="footer">
        <!--<p></p>-->
        <p>Principal &nbsp;&nbsp;&nbsp; <strong> SCHOOL MO.NO.</strong> (123) 456-7890</p>
        <!-- <p><strong>Website:</strong> www.schoolwebsite.com</p> -->
      </div>
    </div>
  </body>
</html>
