<!DOCTYPE html>
@php
$getSetting=Helper::getSetting();
@endphp

 <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
<style>	
@media screen and (min-width: 600px) {
    .container{
        text-align:center;padding-left:20%;
        padding-right:20%;
        max-width:100%;
    }
.content{
    background:#282828;
    background-repeat: repeat-y;
    background-size:90% 400%;
    border-bottom:0px solid white;
    border-top:0px solid white;
    padding:25px;
    text-align:right;
    max-width:100%;
    }}
    
   @media screen and (max-width: 599px) {
    .container{
        text-align:center;
        padding-left:5%;
        padding-right:5%;
        
    }
    .content{
    background:#282828;
    background-size:100% 904%;
    border-bottom:0px solid white;
    border-top:0px solid white;
    padding:5px;
    text-align:right;
    max-width:100%;
    }
    }
    
</style>
</head>
<body>
<div class="container">
	
			<div class="content">
	 <img src="https://pbs.twimg.com/profile_images/1294560793732685824/6SqEWK5L_400x400.jpg"style="padding-left:5px;height:70px;width:70px;border-radius:16px 3px 16px 3px;background:white">
       
		</div>
		<div style="text-align:left;background:#043e44;color:white;font-weight:bold;padding:15px; ">
		
     Dear, {{ $first_name ?? '' }} {{ $last_name ?? '' }}
		</div>
		<div  style="background:url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU);padding:30px;background-size:100% 100%;">
		
<p style="text-align:left;color:cadetblue;font-size:23px">Welcome to the {{$getSetting['name'] ?? ''}},&nbsp;Jaipur </p>


<p style="text-align:left;margin-top:-10px;font-size:18px">
<strong style="margin-top:20px"><i>Name:- </i></strong>&nbsp;{{ $first_name ?? '' }}{{ $last_name ?? '' }}<br>
<strong><i>Class:- </i></strong>&nbsp;{{ $class ?? '' }} ({{ $section ?? '' }})<br>
<strong style="margin-top:20px"><i>Father Name:- </i></strong>&nbsp;{{ $father_name ?? '' }}<br>
<strong style="margin-top:20px"><i>Father Contact:- </i></strong>&nbsp;{{ $father_mobile  ?? '' }}<br>

<strong style="margin-top:20px"><i>Subject:- </i></strong>&nbsp;{{ $subject1 ?? '' }}<br>
<strong><i>Description:- </i></strong>&nbsp;{{ $description ?? '' }} <br>
<strong><i>Date:- </i></strong>&nbsp;{{ $date ?? '' }} <br>

</p>

<p style="text-align:left;margin-top:30px">


</p>

</div>

<div style="text-align:right; line-height:10px;margin-top:60px" >
		<p style="font-size:15px;line-height:20px;">Principle office,<br><br>{{$getSetting['name'] ?? ''}}</p>
		<p><a style="color:#ec0928" href="tel://91 {{$getSetting['mobile'] ?? ''}}">+91 {{$getSetting['mobile'] ?? ''}}</a></p>
		
    
       
        </div>
		</div>
         <div>
         </div>
	</div>

</body>
</html>
