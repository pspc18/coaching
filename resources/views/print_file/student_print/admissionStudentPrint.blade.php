<head>
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/all.min.css') }}">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/tempusdominus-bootstrap-4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/jqvmap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">
</head>

@php
$getSetting=Helper::getSetting();

@endphp
<style>
  body {
    border: 1px solid;
  }

  #tbody td {
    padding-top: 20px;
    padding-bottom: 3px;
    font-size:25px;
  }

  .student_img {
    width: 100px;
    height: 120;
    margin-top: 10%;
    margin-left: 60%;
    border: 1px solid;
  }


  input[type=submit]:hover {
    background-color: #45a049;
  }
   .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: 328px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:40%;
        }
</style>

@include('print_file.print_header')
<div align="center" class="tab">


  <table>
    <br><br>
    <h3><u>Student Admission Form</u></h3> <br>
  </table>
  <table id="tbody" style="text-align:left;width:80%;">
      <div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
                </div>
    <tr>
      <td style="width:30%;"><strong>Student Name:-</strong></td>
      <td style="width: 70%;border-bottom:1px solid;">{{ $data['first_name'] ?? '-' }} {{ $data['last_name'] ?? '' }}</td>
      <td rowspan="4" style="position: inherit;">
        <!--<p style="color:red;margin-left:5%;"></p>-->

        <span class="style73">
            <span style="margin-left: 56%;color: red;">Add.No:{{ $data['admissionNo'] ?? '' }}</span>
            @if(!empty($data['image']))
          <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" style="margin-left: 66%; margin-top: 2%;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @else
          <img class="student_img" src="{{ env('IMAGE_SHOW_PATH').'/student_icon.png' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'">
          @endif </span>

      </td>

    </tr>
    <tr>
      <td><strong>Date of Birth:-</strong></td>
      <td  style="border-bottom:1px solid;">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</td>

    </tr>
    <tr>
      <td><strong>Gender:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['gender'] ['name'] ?? '-' }}</td>


    </tr>
    <tr>
      <td><strong>Admission Type:-</strong></td>
      <td style="border-bottom:1px solid;"> @if($data['admission_type_id'] == 1)
        Regular
        @elseif($data['admission_type_id'] == 2)
        Non
        @elseif($data['admission_type_id'] == 3)
        Other
        @else
        -
        @endif</td>

    </tr>
    <tr>
      <td><strong>Email:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['email'] ?? '-' }}</td>

    </tr>

    <tr>
      <td><strong>Aadhaar:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['aadhaar'] ?? '-' }}</td>

    </tr>
    <tr>
      <td><strong>Class:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['ClassTypes']['name'] ?? '-' }}
            @if (!empty($data['Section']['name']))
                ({{ $data['Section']['name'] }})
            @endif</td>

    </tr>
    <tr>
      <td><strong>Father's Name:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['father_name'] ?? '-' }}</td>

    </tr>

    <tr>
      <td><strong>Mother's Name:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['mother_name'] ?? '-' }}</td>

    </tr>


    <tr>
      <td><strong>Father's Contact:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['mobile'] ?? '-' }}</td>

    </tr>

    <tr>
      <td><strong>Pincode:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['pincode'] ?? '-' }}</td>

    </tr>

    <tr>
      <td><strong>Address:-</strong></td>
      <td style="border-bottom:1px solid;">{{ $data['address'] ?? '-' }}</td>

    </tr>
    </div>
  </table>
  <br><br>
 
  <div class="text-left ml-5 mr-5" style="border-top: 2px dotted"> 
  <br>
  <p>1. Admission to [{{$getSetting->name ?? ''}}] is subject to the availability of seats and the fulfillment of the school's admission criteria.</p>
 <p>2. Parents or guardians are required to complete the official admission application form, providing accurate and complete information about the child.</p>
<p>3. Admission decisions are at the sole discretion of [{{$getSetting->name ?? ''}}] and are non-discriminatory based on gender, race, religion, or nationality.</p>
<p>4. [{{$getSetting->name ?? ''}}] has a code of conduct that all students are required to follow. Failure to adhere to the code of conduct may result in disciplinary action.</p>
 </div>
  @include('print_file.print_footer')

  <style>
    td {
      padding: 5px;
    }
  </style>
  <script type="text/javascript">
    window.print();
  </script>
