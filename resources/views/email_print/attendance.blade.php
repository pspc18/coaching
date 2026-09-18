<!DOCTYPE html>
<html>
<head>
<title>Page Title</title>

<style>
body{
color:#26437d;
font-family: Helvetica, "Trebuchet MS", Verdana, sans-serif;
margin:8% auto; 
}


.img_logo{
width:180px;

height:70px;
margin-top:-63px;

}
.img_logo1{
width:100px;
padding-top:15px;

height:100px;
margin-top:-33px;

}
.mainDiv{
    background:#f3f3f3;
border-radius:20px;
padding:50px;
  max-width: 650px;
  max-height:330px;
  margin: auto;
background-position:center;
background-repeat:no-repeat;
background-size:100% 100%;
background-image:url('https://www.school.rukmanisoftware.com/schoolimage/design.png');
box-shadow: 0 3px 6px rgba(0,0,0,0.12), 0 6px 6px rgba(0,0,0,0.24);
transition: all 0.3s cubic-bezier(.25,.8,.25,1);
}

.padding_div{
     padding:3% 15% 0% 15%;
}
@media only screen and (max-width: 600px) {

h1{
    font-size:20px;
    color:#26437d;
}
h3{
    font-size: 14px;
    color:#26437d;
}
.img_logo1 {
  width: 65px;
  padding-top: 15px;
  height: 60px;
  margin-top: -39px;
}
.mainDiv {
 
  padding: 0px;

}
.padding_div{
      padding: 3% 3% 3% 3%;
}
.img_logo {
  width: 143px;
  height: 49px;
  margin-top: 6px;
}

}
</style>
</head>
@php
@endphp
<body style="">
<div  class="padding_div">
<div  class="mainDiv" >
<table>
<tr>
<td>

</td>
</tr>
</table>

<table style="width:100%">
<tr>
<td style="width:50%;text-align:left"></td>
<td style="width:50%;text-align:center" >
<img class="img_logo" src="https://www.school.rukmanisoftware.com/public/images/header-logo.png"><br>
<h1>Hey! {{ $name ?? '' }}</h1>
<h3>Today's your atttendance <b>
@if($attendance_status == 1)
    ( Present )
@elseif($attendance_status == 2)
    ( Absent )
@elseif($attendance_status == 3)
    ( Work From Home )
@elseif($attendance_status == 4)
    ( Half-day )
@elseif($attendance_status == 5)
    ( Holiday )
@endif    
    
    </b> has been submitted successfully on <b>{{ $date ?? '' }}.</b></h3>

</td>
</tr>
<tr>
<td style="width:50%;text-align:left"></td>
<td style="width:50%;text-align:center" >
<img class="img_logo1" src="https://icon-library.com/images/attendance-icon/attendance-icon-10.jpg">
</td>
</tr>
</table>
</div>
</div>
</body>
</html>
