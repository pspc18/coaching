<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Achievement Certificate</title>
  
</head>

@php
    $getSetting = Helper::getSetting();

    
    if($certificate_data->class_type_id > 4)
    {
        $backgroundImage = '/certificates_images/achievement2.jpg';
    }else{
        $backgroundImage = '/certificates_images/achievementlkgukg.jpg';
    }
    
@endphp

<style>
/*@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Protest+Revolution&display=swap');*/
/*@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&family=Encode+Sans:wght@100..900&family=Fugaz+One&family=Gruppo&family=Protest+Revolution&family=Rubik+Bubbles&display=swap');*/

.background_image{
    font-family: "Fugaz One", sans-serif;
    position:absolute;
    top:0;
    left:0;
    height:100%;
    width:100%;
    background-image: url('{{ env("IMAGE_SHOW_PATH").$backgroundImage }}');
    background-size:100% 100%;
    overflow:hidden;
}

.title_text{
    position: absolute;
    top: 46%;
    left: 26%;
    font-weight: 400;
    text-transform: capitalize;
    color: #303030;
}

.for_main_text{
    position: absolute;
    top: 60%;
    left: 20%;
    font-weight: 400;
    text-transform: capitalize;
    color: #303030;
}

.class_main_text{
    position: absolute;
    top: 46%;
    right: 25%;
    font-weight: 400;
    text-transform: capitalize;
    color: #303030;
}

.date_text{
    position: absolute;
    top: 68%;
    left: 20%;
    font-weight: 400;
    text-transform: capitalize;
    color: #303030;
}

.class_teacher_img{
    position: absolute;
    top: 63%;
    left: 43%;
}

.class_teacher_img img{
    width: 130px;
}
</style>

<style>
      @page{
    margin:0;
}
body{
    margin: 0px auto;
}
p{
  font-size: 22px !important;
  line-height: 32px;
}
.container {
    font-family: "Fugaz One", sans-serif;
    font-style: normal;
    width: 900px;
    color: black;
    margin: 0px auto;
    text-shadow: 1px 1px 0px lightgrey;
    transform: scale(0.7);
    margin-left:3pc;
}

.print_div{
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    background :url('{{ env("IMAGE_SHOW_PATH").$backgroundImage }}');
    background-size: 100% 100%;
    overflow: hidden;
}

.name_text{
position: absolute;
top: 49%;
left: 35%;
font-size: 18px;
text-transform: capitalize;
font-weight: 600;
}

.class_text{
position: absolute;
top: 49%;
right: 27%;
font-size: 18px;
text-transform: capitalize;
font-weight: 600;
}

.for_text{
    position: absolute;
    top: 59%;
    font-size: 18px;
    text-transform: capitalize;
    font-weight: 600;
    left: 40%;
}

.issue_date{
    position: absolute;
    top: 66%;
    font-size: 14px;
    text-transform: capitalize;
    font-weight: 500;
    left: 35%;
}

.student_img {
    width: 80px;
    height: 100;
    margin-top: 5%;
    margin-left: 20%;
    padding-bottom: 10px;

}

p {
    margin-bottom: 0px;
    margin-top: 0px;
    line-height:40px;
}

.lheight {
    line-height: 20px;
}

.row {
    margin-right: 0px;
}

.img_background_fixed {
    position: relative;
}

.img_absolute {
    position: absolute;
    top: 80px;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    right: 0;
}

.backhround_img {
    opacity: 0.3;
    width: 34%;
}

table {
    /*width: 100%;*/
    border-collapse: collapse;
    margin: 0px;
}

.inner_table th {
    border: 1px solid #000;
    padding: 5px;
    /* background-color: #f2f2f2; */
}

.invoice-header {
    margin-bottom: 20px;
    text-align: 'center'
}

.inner_table td {
    padding: 5px
}

.ltr {
    text-align: left;
    border-right: none !important;
}

.pltr {
    padding-left: 20px;
    margin: 10px;
}

.rtr {
    text-align: right;
}

.ctr {
    text-align: center;
}

#personal_detail th {
    border: 1px solid #000;
    text-align: left;
    padding: 5px 0px;
    font-weight:600;
}

#personal_detail td {
    border: 1px solid #000;
    text-align: left;
    padding: 5px 0px;
    font-weight:600;
}

.ptr {
    padding: 10px;
}

.bdtr {
    border: 1px solid black;
}

.bg_theme {
   // background-color: gainsboro;
}

.bg_dark_theme {
    background-color: gray;
    color: white;
}

.striped {
    background-color: gainsboro;
}

.inner_text {
    font-size: 16px;
    font-weight: 600;
}

.plt{
    padding-left:10px !important;
}

.profile_pic{
    width: 100px;
    height: 100px;
    border: 1px solid black;
    border-radius: 10px;
}

.print-page-break{
    margin-top:30px;
}

@media print {
    .print-page-break {
        page-break-after: always;
        margin-top:0px;
    }
}
        
.certificate_heading{
  /*font-family: "Protest Revolution", sans-serif;*/
  font-weight: 400;
  font-style: normal;
  font-size: 32px;
  font-weight: bold;
  line-height:36px;

        }
.td_bottom {
    border-bottom:3px dashed black;
    padding:10px;
font-size:25px;
   
}
.dynamic_data{
     /*font-family: "Protest Revolution", sans-serif;*/
  /*font-weight: 800;*/
  font-style: normal;
   font-size: 25px;
  text-transform: capitalize;
     font-style: italic;
    text-shadow: 0px 0px 0px #eea536;
}

b{
    font-weight: 400;
}
</style>
<body>
    
    @if($certificate_data->class_type_id > 4)
        <div class="background_image">
      <h3 class="title_text">
          {{ $certificate_data->first_name ?? '' }} {{ $certificate_data->last_name ?? '' }}
      </h3>
      <h3 class="class_main_text">
          {{ $certificate_data->class_name ?? '' }}
      </h3>
      <h3 class="for_main_text">
          {{ $certificate_data->achievement_for ?? '' }}
      </h3>
      
  </div>
    @else
        <div class="print_div">
        
        <div class="background_img">
            
            <div class = 'container'>  
            
            <table style="border: 0px solid black; width:{{$certificate_data->class_type_id > 4 ? '88%' : '100%' }}">
        <tbody class="bg_theme"    >
            <tr>
                <td rowspan='2' width='10%' class="rtr">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style='filter: drop-shadow(5px 5px 4px #eea536);'width='150px' onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'"   width = "110px" >
                </td>
                <td width='90%' style="font-size:{{$certificate_data->class_type_id > 4 ? '31px' : '35px' }};text-align:center;text-transform:capitalize"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span>
                </td>

                <td width="20%"></td>

            </tr>
            <tr style="text-align:center;">
                <td width='100%'>
                    <div class="ctr lheight">
                         <p style='margin-top:-0px;margin-bottom:11px'>BE A CREATIVE LEARNER</p>
                        <p><b>Address </b> {{$getSetting['address'] ?? ''}} {{','.$getSetting['pincode'] ?? ''}}<br>
                      {{$getSetting['gmail'] ?? ''}} || www.stepbystepconvertschool.com
                      
                         <!--<p><b>Phone:-</b> {{$getSetting['mobile'] ?? ''}} </p>-->
                    </div>
                </td>

                <td width="20%"></td>
            </tr>
        </tbody>

       
    </table>
     @if($certificate_data->class_type_id > 4)
    <br><br><br><br>
    @endif
    <div style='text-align:center;  margin-top:60px;margin-bottom:40px;'>
        <span class='certificate_heading'>CERTIFICATE OF ACHIEVEMENT <br><span style='font-family: "Fugaz One", sans-serif;font-size:20px'>Presented to</span></span>
    </div>
    
   <table width='100%' style='margin-top:20px'>
       <tbody>
           <tr><td class='td_bottom'width='50%'colspan='2'  >To &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class='dynamic_data'>{{ $certificate_data->first_name ?? '' }} {{ $certificate_data->last_name ?? '' }}</span></td><td  class='td_bottom'width='50%'>Of Class
           &nbsp;&nbsp;&nbsp;&nbsp;<span class='dynamic_data' style='position: absolute;'>{{ $certificate_data->class_name ?? '' }}</span>
           </td></tr>
           <tr><td class='td_bottom' colspan='3' width='100%'  style='text-align: center; padding: 10px;vertical-align: baseline;justify-content: space-around;
  vertical-align: top;
  height: 60px;'>For <br><br>
  <span class='dynamic_data'>{{ $certificate_data->achievement_for ?? '' }}</span>
  </td></tr>
  <tr><td class='td_bottom'width='33.3%' style='text-align: center;vertical-align: baseline;justify-content: space-around;
  vertical-align: bottom;
  height: 30px;'><br>Date</td>
  <td class='td_bottom'width='33.3%' style='text-align: center;vertical-align: baseline;justify-content: space-around;
  vertical-align: bottom;
  height: 30px;'>Class Teacher</td>
  <td class='td_bottom'width='33.3%' style='text-align: center;vertical-align: baseline;justify-content: space-around;
  vertical-align: bottom;
  height: 30px;'><br>Principal</td></tr>
          
       </tbody>
   </table>
    </div>
    
   <!--
           
              <h1 class="name_text">{{ $certificate_data->first_name ?? '' }} {{ $certificate_data->last_name ?? '' }}</h1>
              <h1 class="class_text">{{ $certificate_data->class_name ?? '' }}</h1>
              <h1 class="for_text">{{ $certificate_data->achievement_for ?? '' }}</h1>
              <h1 class="issue_date">{{ date('d-M-Y', strtotime($certificate_data->iessu_date)) ?? '' }}</h1>
              <img src="{{ env('IMAGE_SHOW_PATH').'setting/seal_sign/'.$getSetting->seal_sign }}" alt="sign">
        </div>
    </div>
    @endif
    

</body>

<script type="text/javascript">
    window.print();
</script>
</html>