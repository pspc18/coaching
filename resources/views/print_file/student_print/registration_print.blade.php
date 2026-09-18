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
      .img_background_fixed{
          position: relative;
        }
        
        .img_absolute{
            position: absolute;
            top: -53px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }
        
        .backhround_img{
            opacity: 0.3;
            width:38%;
        }
</style>
</head>
<body>
<div class="container">
	<div class="img_background_fixed">
                <div class="img_absolute">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
                </div>
		<div class="content">
	 <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="padding-left:5px;height:70px;width:70px;border-radius:16px 3px 16px 3px;background:white">
       
		</div>
		<div style="text-align:left;background:#043e44;color:white;font-weight:bold;padding:15px; ">
		
     Dear, {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}
		</div>
		<div  style="background:url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU);padding:30px;background-size:100% 100%;">
		
<p style="text-align:left;color:cadetblue;font-size:23px">Welcome to the {{$getSetting['name'] ?? ''}},&nbsp;Jaipur </p>

<p style="text-align:left;margin-top:-10px">Your Registration to {{$getSetting['name'] ?? ''}}  has been Successfully.</p>

<p style="text-align:left;color:cadetblue;font-size:23px">
Your Registration Details are Following : </p>

<p style="text-align:left;margin-top:-10px;font-size:18px">
<strong style="margin-top:20px"><i>Registration No. :- </i></strong>&nbsp;  {{ $data['registration_no'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Registration Date :- </i></strong>&nbsp; {{date('d-m-Y', strtotime($data['registration_date'])) ?? '' }}<br>
<strong style="margin-top:20px"><i>Name:- </i></strong>&nbsp;{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Class:- </i></strong>&nbsp;{{ $data['ClassTypes']['name'] ?? '' }}
                            @if (!empty($data['Section']['name']))
                                ({{ $data['Section']['name'] }})
                            @endif<br>
<strong style="margin-top:20px"><i>Father's name- </i></strong>&nbsp;{{ $data['father_name'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Mother's Name :- </i></strong>&nbsp; {{ $data['mother_name'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Mobile no.:- </i></strong>&nbsp;{{ $data['mobile'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Email:- </i></strong>&nbsp;{{ $data['email'] ?? '' }}<br>
<strong style="margin-top:20px"><i>Address:- </i></strong>&nbsp;{{ $data['address'] ?? '' }}<br>

</p>

<p style="text-align:left;margin-top:30px">

The email and mobile is the only way that the School will use to contact you so it is important you know how to access it.

</p>

<p style="line-height:35px;margin-top:30px">Welcome dear [  {{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}  ],<br><strong style="font-size:23px;" >{{$getSetting['name'] ?? ''}}<strong></p>
</div>

<div style="text-align:right; line-height:10px;margin-top:60px" >
       <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="    height: 92px;width: 15%;">
		<p style="font-size:15px;line-height:20px;">Principle office,<br><br>{{$getSetting['name'] ?? ''}}</p>
		<p><a style="color:#ec0928" href="tel://91 {{$getSetting['mobile'] ?? ''}}">+91 {{$getSetting['mobile'] ?? ''}}</a></p>
		
    
       
        </div>
		</div>
         <div>
         </div>
	</div>
	</div>

</body>
</html>

<script type="text/javascript">
 window.print();
</script>

