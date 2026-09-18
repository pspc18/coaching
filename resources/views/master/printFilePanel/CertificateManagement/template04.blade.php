<!doctype html>
<html lang="en">

<head>
  <title>Tc Certificate</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
    integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

@php
$getSetting=Helper::getSetting();
@endphp

<style>
  .title_top {
    font-size: 25px;
    font-weight: 600;
    margin-bottom: 0px;
  } 

  .end_flex {
    display: flex;
    align-items: end;
  }

  .description_head {
    font-size: 16px;
    font-weight: 400;
    margin-bottom: 0px;
  }

  .title_bottom {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 0px;
  }

  .border_table {
    border: 1px solid black;
    padding: 10px;
  }

  .second_table {
    border-top: 1px solid black;
    border-bottom: 1px solid black;
  }

  .second_table th {
    border: 1px solid black;
    background-color: #CCCCCC;
  }

  .second_table td {
    border: 1px solid black;
  }

  .height_table {
    margin-top: 10%;
    margin-bottom: 5%;
  }

  .top_line {
    border-top: 2px solid black;
    padding-top: 20px;
    padding-bottom: 20px;
  }

  .flextd {
    display: flex;
    align-items: first baseline;
    justify-content: center;
    margin-bottom: 10px;
    margin-top: 20px;
  }

  .boldText {
    font-size: 20px;
    font-weight: 600;
  }

  .deshed {
    border-bottom: 2px dotted;
    width: 30%;
  }

  .flexTDD {
    display: flex;
    align-items: first baseline;
    padding: 0px 20px;
    margin-bottom: 10px;
  }

  .flexTDD_first {
    margin-top: 20px;
  }

  .DescriptionText {
    font-size: 20px;
    white-space: nowrap;
  }

  .capitalize_text {
    font-size: 20px;
    text-transform: capitalize;
    border-bottom: 2px dotted;
    width: 100%;
    margin-left:10px;
  }
    .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
              top: 460px;
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
         .ltr{
            text-align: left;
            border-right: none !important;
            padding:5px
        }
        .rtr{
            text-align:right;
        }
</style>

<body>
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
    crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
    integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
    crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>



  <div class="container">
    <table width="100%" class="border_table" cellspacing="0" cellpadding="0"style="">
         <div class="img_background_fixed">
            <div class="img_absolute">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" alt="bg_logo" class="backhround_img">
            </div>
      <tr>
        <td colspan="12">
          <table width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td width="16%" style="padding:15px">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" alt="logo"
                  width="100%">
              </td>
              <td width="71%" class="text-center">
                <p class="title_top" style="color:#aac818;">{{$getSetting['name'] ?? ''}}</p>
                
                <p class="description_head"><b>Email -</b> {{$getSetting['gmail'] ?? ''}}</p>
                <p class="description_head"><b>Mobile No. -</b> {{$getSetting['mobile'] ?? ''}}</p>
                <p class="description_head"> <b>Address -</b> {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</p>

              </td>
              <td width="13%">

              </td>
            </tr>
          </table>
        </td>
      </tr>
     
      <tr>
        <td colspan="12" class="text-center title_top top_line"><u>TRANSFER CERTIFICATE</u></td>
      </tr>
      <tr>
        <td colspan="6" class="text-center">
          <div class="flextd">
            <div class="boldText">T.C.issue No :</div>&nbsp;&nbsp;
            <div class="deshed ltr"> {{$data['id'] ?? ''}}</div>
          </div>
        </td>
        <td colspan="6" class="text-center">
          <div class="flextd">
            <div class="boldText">School Admission No :</div>&nbsp;&nbsp;
            <div class="deshed ltr">{{$data['admissionNo'] ?? ''}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD flexTDD_first">
            <div class="DescriptionText">1. This is certify that Miss/Master :</div>
            <div class="capitalize_text">{{$data['first_names'] ?? ''}} {{$data['last_name'] ?? ''}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">2. Mother's Name :</div>
            <div class="capitalize_text">{{$data['admissions_mother_name'] ?? ''}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">3. Father's Name :</div>
            <div class="capitalize_text">{{$data['admissions_father_name'] ?? ''}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="6">
          <div class="flexTDD pr-0">
            <div class="DescriptionText">4. From Date :</div>
            <div class="capitalize_text">{{ date('d-m-Y', strtotime($data['admission_date'])) }}</div>
          </div>
        </td>
        <td colspan="6">
          <div class="flexTDD pl-0">
            <div class="DescriptionText">To Date :</div>
            <div class="capitalize_text">{{ date('d-m-Y', strtotime($data['issue_date']))}}</div>
          </div>
        </td>
      </tr>
      <tr>
        @php
            $currentDate = date('d-m-Y');
        @endphp
        <td colspan="6">
          <div class="flexTDD pr-0">
            <div class="DescriptionText">and is leaving the school to day date
            </div>
            <div class="capitalize_text">{{$currentDate}}</div>
          </div>
        </td>
        <td colspan="6">
          <div class="flexTDD pl-0">
            <div class="DescriptionText">
              He/She has made payment of fees as mention below</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">5. As per school record his/her Date of birth(in figures):
            </div>
            <div class="capitalize_text">{{ date('d-m-Y', strtotime($data['dob'])) }}</div>
          </div>
        </td>
      </tr>
      <!--<tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">
              in words
            </div>
            <div class="capitalize_text">tiger</div>
          </div>
        </td>
      </tr>-->
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">
              6. Last examination passed by him/her is class
              Medium :
            </div>
            <div class="capitalize_text">{{$data['mudium'] ?? ''}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="6">
          <div class="flexTDD pr-0">
            <div class="DescriptionText">7. Year :
            </div>
            <div class="capitalize_text">{{ date('d-m-Y', strtotime($data['issue_date'])) }}</div>
          </div>
        </td>
        <td colspan="6">
          <div class="flexTDD pl-0">
            <div class="DescriptionText">
              and he/she was enrolled in class
            </div>
            <div class="capitalize_text">{{ $data['ClassName'] ?? '' }}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">
              8. His/Her character was :
            </div>
            <div class="capitalize_text">Good</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <div class="flexTDD">
            <div class="DescriptionText">
              9. before leaving school on date :
            </div>
            <div class="capitalize_text">{{$currentDate}}</div>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="12">
          <table width="100%" cellspacing="0" cellpadding="0" class="height_table">
            <tr>
              <td colspan="6" class="text-center">
                <p class="title_bottom">DIRECTOR SIGNATURE</p>
              </td>
              <td colspan="6" class="text-center">
                <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" alt="seal"
                  width="100px">
                <p class="title_bottom">PRINCIPAL SIGNATURE</p>
              </td>
            </tr>
          </table>
        </td>
      </tr>
       </div>
    </table>
  </div>
</body>

<script type="text/javascript">
  //window.print();
</script>

</html>