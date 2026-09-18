
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

body{

font-family: Trebuchet MS;
font-size:12px;
}

@media screen and (min-width: 600px) {
    .container{
        text-align:center;padding-left:20%;
        padding-right:20%;
        max-width:100%;
    }
.content{
    background:white;
    background-repeat: repeat-y;
    background-size:90% 400%;
    border-bottom:0px solid white;
    border-top:50px solid #0dccf5;
    border-left:2px solid #0dccf5;
     border-right:2px solid #0dccf5;
    
    text-align:center;
    max-width:100%;
    }}
    
   @media screen and (max-width: 599px) {
    .container{
        text-align:center;
        padding-left:5%;
        padding-right:5%;
        
    }
    .content{
    background:white;
    background-size:100% 904%;
     border-top:50px solid #0dccf5;
    border-bottom:0px solid #0dccf5;
    
    text-align:center;
    max-width:100%;
    }
    }
    
</style>
</head>
<body>
<div class="container">
	
		<div class="content">
	 <img src="https://pbs.twimg.com/profile_images/1294560793732685824/6SqEWK5L_400x400.jpg"style="padding-left:5px;height:150px;width:150px;border-radius:16px 3px 16px 3px;background:white">
       
		</div>
		
		<div  style="  border-bottom:2px solid #0dccf5;
    border-top:0px solid #0dccf5;
    border-left:2px solid #0dccf5;
     border-right:2px solid #0dccf5;padding:25px">
		
<p style="text-align:center;font-size:33px;margin-top:-60px!important">Assignment Details </p>

<!--<p style="text-align:center;margin-top:-10px">We've detected that you forgot your password To use the School's IT facilities you will need a Rukmani's username and password to log in to the network. Use credentials below to get started.</p>
<br>-->

<p style="text-align:center;margin-top:-10px;font-size:18px;background-color: #0dccf5;
border-radius: 5px;
font-size: 17px;
font-family: 'Source Sans Pro', sans-serif;
padding: 7px 18px;color:white">
<strong style="margin-top:20px"><i>Name: -</i></strong>&nbsp;{{ $first_name ?? '' }} {{ $last_name ?? '' }}<br>
<strong><i>Father Name:- </i></strong>&nbsp;{{ $father_name ?? '' }}<br>
<strong><i>Date & Time:- </i></strong>&nbsp;{{ $dateTime ?? '' }}
</p>

<p style="text-align:center;margin-top:30px">For More Details Click below</p>
<p><b><i><a href="{{ $url ?? '' }}" target="blank">{{ $url ?? '' }}</a></i></b></p>
<!--<p style="text-align:center;margin-top:30px">

If you did not request a forgot password, you can safely ignore this email.Only a person with access to your email can reset your account password.

</p>-->
<br><br><br>

</div>


		</div>
         <div>
         </div>
	</div>

</body>
</html>
