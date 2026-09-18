<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Achievement Certificate</title>
</head>

@php
    $getSetting = Helper::getSetting();
@endphp

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
    font-weight: 400;
    font-style: normal;
    width: 900px;
    color: black;
    margin: 25px 100px;
    text-shadow: 1px 1px 0px lightgrey;
    transform: scale(0.8);
}

.print_div{
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    background :url('{{ env("IMAGE_SHOW_PATH").'/certificates_images/achievement3.jpg' }}');
    background-size: 100% 100%;
    overflow: hidden;
}

.name_text{
position: absolute;
top: 45%;
left: 27%;
font-size: 28px;
text-transform: capitalize;
font-weight: 600;
}

.class_text{
position: absolute;
top: 46%;
right: 27%;
font-size: 28px;
text-transform: capitalize;
font-weight: 600;
}

.for_text{
    position: absolute;
    top: 59%;
    font-size: 28px;
    text-transform: capitalize;
    font-weight: 600;
    left: 45%;
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
  font-family: "Protest Revolution", sans-serif;
  font-weight: 400;
  font-style: normal;
  font-size: 40px;
  font-weight: bold;
  line-height:36px;

        }
.td_bottom {
    border-bottom:3px dashed black;
    padding:10px;
font-size:25px;
   
}
.dynamic_data{
     font-family: "Protest Revolution", sans-serif;
  font-weight: 800;
  font-style: normal;
   font-size: 25px;
  text-transform: capitalize;
     font-style: italic;
    text-shadow: 0px 0px 0px #eea536;
}
</style>
<body>
      <div class="print_div">
        
        <div class="background_img">
            
         
                <h1 class="name_text">{{ $certificate_data->first_name ?? '' }} {{ $certificate_data->last_name ?? '' }}</h1>
              <h1 class="class_text">{{ $certificate_data->class_name ?? '' }}</h1>
              <h1 class="for_text">{{ $certificate_data->achievement_for ?? '' }}</h1>
              <!--<h1 class="issue_date">{{ date('d-M-Y', strtotime($certificate_data->iessu_date)) ?? '' }}</h1>-->
              <!--<img src="{{ env('IMAGE_SHOW_PATH').'setting/seal_sign/'.$getSetting->seal_sign }}" alt="sign">-->
        </div>
    </div>
</body>

<script type="text/javascript">
window.print();
</script>

</html>