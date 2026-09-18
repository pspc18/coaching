@php
$getSetting=Helper::getSetting();
$getSession=Helper::getSession();
 $geturl = Route::current();
 //dd($geturl);
@endphp
<html lang="en">

<head>
    <title>Evente Certificate</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


    <style type="text/css">
        .style4 {
            font-size: 18px 
        }
        
        .style5 {
            font-size: 30px;
            font-weight: bold;
            color:#aac818;
        }
        
        .style9 {
            font-size: 16px
        }
        
        .style10 {
            font-size: 24px;
            padding: 0px 15px 0px;
            letter-spacing:1px;
            line-height:40px;
        }
        
        .style11 {
            font-size: 36px;
            font-weight: bold;
        }
         .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: 85px;
            width: 100%;
            /*display: flex;*/
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:40%;
        }
        
        @media print{@page {size: landscape}}

    </style>
</head>

<body>
    <div style="padding: 10px;">
        <table width="100%" border="2" cellspacing="0" cellpadding="0">
            <tr>
                <td colspan="3">
                    <table width="100%" cellspacing="0" cellpadding="0" border="2" >
                        <tr >
                            <td width="20%" style="text-align:center;padding:10px; border: 0px solid white;">
                                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'"   width = "70%" >
                                
                                </td>
                            <td width="60%" style="border: 0px solid white;">
                                <div>
                                    <div align="center">
                                        <p class="style5">{{$getSetting['name'] ?? ''}}</p>
                                        <div><span class="style4"><strong>Email Id</strong> - {{$getSetting['gmail'] ?? ''}}</span></div>
                                        <div><span class="style4"><strong>Mobile No.</strong> - {{$getSetting['mobile'] ?? ''}}</span></div>
                                        <div><span class="style4"><strong>Address</strong> - {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</span></div>
                                      <!--  <div class="style4"><strong>Website - </strong></div>
                                        <div class="style4"><strong>Mobile App - </strong> </div>-->
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </td>
                            <td width="20%" style="text-align:center;padding:10px; border: 0px solid white;">
                                <!--<img src="{{ env('IMAGE_SHOW_PATH').'/setting/right_logo/'.$getSetting['right_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'"   width ="70%" > -->
                                </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr>
                <td colspan="3">
                    <table width="100%" cellspacing="0" cellpadding="0" style="">
                        <tr>
                            <td>
                                <div>
                                    <div align="center">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                             <div class="img_background_fixed">
                                                    <div class="img_absolute">
                                                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" alt="bg_logo" class="backhround_img">
                                                    </div>
                                            <tr>
                                                <td width="25%" style="padding: 0px 15px 0px;">
                                                    <div style="font-size:24px;letter-spacing:2px"><i>Date :</i> <strong style="border-bottom: 2px dotted black;">{{date('d-m-Y', strtotime($data['organized_date'])) ?? '' }}</strong></div>
                                                </td>
                                                <td width="50%">
                                                    <div>
                                                        <div align="center" class="style10" style="padding: 25px 0px 40px 0px;">

                                                            <p class="style11">Evente Certificate</p>
                                                           
                                                            <div style="font-weight: 600;"><u>TO WHOM SOEVER IT MAY CONCERN</u> </div>
                                                           
                                                        </div>
                                                    </div>
                                                </td>
                                                <td style="text-align:center;">
                                                   <img width='50%' height='50%' src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" >
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="style10"><strong><i>This is certified that</i></strong> <i>Mstr/Ms.</i> <strong style="border-bottom: 2px dotted black;">{{$data['name'] ?? ''}}</strong></div>
                                                    <div class="style10"><i>Son/Daughter Of </i><strong style="border-bottom: 2px dotted black;"> &nbsp;{{$data['father_names'] ?? ''}}</strong></div>
                                                    <div class="style10"><i>Mother 's Name</i> <strong style="border-bottom: 2px dotted black;">{{$data['mother_name'] ?? ''}}</strong></div>
                                                    <div class="style10"><i>Address </i><strong style="border-bottom: 2px dotted black;">{{$data['address'] ?? ''}}</strong></div>
                                                    <div class="style10"><i>was a regular student of this Institution.</i> </div>
                                                    <!--<div class="style10">He/She has passed in class <strong><u>{{$data['class_name'] ?? ''}}</u></strong> in the academi c year 2022-23 under SR No.<strong><u>{{$data['sr_no'] ?? ''}}</u></strong> .</div>-->
                                                    <div class="style10"><i>He/She has passed in class</i> <strong style="border-bottom: 2px dotted black;">{{$data['class_name'] ?? ''}}</strong> in the academic year<b style="border-bottom: 2px dotted black;">{{$data['from_year'] ?? ''}}-{{$data['to_year'] ?? ''}}</b> under SR No.<strong style="border-bottom: 2px dotted black;">{{$data['admissionNo'] ?? ''}}</strong>.</div>
                                                    <div class="style10"><i>His/Her date of birth as per our record is </i><strong style="border-bottom: 2px dotted black;">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}.</strong> </div>
                                                    <div class="style10"><i>To the best of my knowledge he/she bears a <strong style="border-bottom: 2px dotted black;">{{$data['event_type'] ?? ''}}.</strong> Character.</i> </div>
                                                    <div>
                                                       
                                                        <p>&nbsp;</p>
                                                    </div>
                                                    <div>
                                                        <div align="right" style="margin-bottom: -31px;"><span class="style10"><strong><i>Initials of the Head of the Institution</i></strong></span></div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </div>
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
    </div>
</body>

</html>


<script type="text/javascript">
   // window.print();
  </script>










<!--@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<html lang="en">

<head>
    <title>Character Certificate</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">


    <style type="text/css">
        .style4 {
            font-size: 18px
        }
        
        .style5 {
            font-size: 24px;
            font-weight: bold;
        }
        
        .style9 {
            font-size: 16px
        }
        
        .style10 {
            font-size: 20px;
            padding: 0px 15px 0px;
        }
        
        .style11 {
            font-size: 36px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <table width="100%" border="2" cellspacing="0" cellpadding="0" style="background-image: url('{{ env('IMAGE_SHOW_PATH').'printback.png' }}');    background-repeat: no-repeat;
    background-position: bottom;
    background-size: 419px 419px;font-family: auto;">
            <tr>
                <td colspan="3">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td width="17%">
                                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'"  style="padding: 0px 15px 0px; width: 58%;margin-top: -21px;">
                                
                                </td>
                            <td width="75%">
                                <div>
                                    <div align="center">
                                        <p class="style5">{{$getSetting['name'] ?? ''}}</p>
                                        <div><span class="style4"><strong>Email Id</strong> - {{$getSetting['gmail'] ?? ''}}</span></div>
                                        <div class="style4"><strong>Website - </strong></div>
                                        <div class="style4"><strong>Mobile App - </strong></div>
                                        <p>&nbsp;</p>
                                    </div>
                                </div>
                            </td>
                            <td width="22%">
                                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/right_logo/'.$getSetting['right_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" style="padding: 5px 0px 0px;     width: 79%;margin-top: -21px;"> </td>
                        </tr>
                    </table>
                </td>
            </tr>


            <tr>
                <td colspan="3">
                    <table width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <div>
                                    <div align="center">
                                        <table width="100%" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td width="15%" style="padding: 0px 15px 0px;">
                                                    <div>Date : <strong>{{$data['iessu_date'] ?? ''}} </strong></div>
                                                </td>
                                                <td width="74%">
                                                    <div>
                                                        <div align="center" class="style10">
                                                            <p class="style11">Character Certificate</p>
                                                            <p>&nbsp;</p>
                                                            <div style="font-weight: 600;"><u>TO WHOM SOEVER IT MAY CONCERN</u> </div>
                                                            <p>&nbsp;</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td width="14%">
                                                    <img src="{{ env('IMAGE_SHOW_PATH').'profile/'.$data['admissions_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" style="width: 58%;margin-top: -21px;">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="style10"><strong>Thi s i s certified that</strong> Mst r/Ms. <strong><u>{{$data['name'] ?? ''}}</u></strong></div>
                                                    <div class="style10">Son/Daughter Of <strong><u>{{$data['father_name'] ?? ''}}</u></strong></div>
                                                    <div class="style10">Mother 's Name <strong><u>{{$data['admissions_mother_name'] ?? ''}}</u></strong></div>
                                                    <div class="style10">Address <strong><u>{{$data['admissions_address'] ?? ''}}</u></strong></div>
                                                    <div class="style10">was a regular student of this Institution. </div>
                                                    <div class="style10">He/She has passed in class <strong><u>{{$data['class_name'] ?? ''}}</u></strong> in the academi c year {{$data['from_year'] ?? ''}}-{{$data['to_year'] ?? ''}} under S.R. No.<strong><u>{{$data['sr_no'] ?? ''}}</u></strong> .</div>
                                                    <div class="style10">His/Her date of bi r th as per our record is <strong><u>{{$data['dob'] ?? ''}}.</u></strong> </div>
                                                    <div>
                                                        <p class="style10">To the best of my knowledge he/she bear s a <strong><u>{{$data['character_type'] ?? ''}}</u></strong> Charac ter . </p>
                                                        <p>&nbsp;</p>
                                                    </div>
                                                    <div>
                                                        <div align="right"><span class="style10"><strong>Initial s of the Head of the Ins titution</strong></span></div>
                                                    </div>
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
    </div>
</body>

</html>-->