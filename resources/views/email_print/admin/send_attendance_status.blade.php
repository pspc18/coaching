<!DOCTYPE html>
@php
$getSetting=Helper::getSetting();
@endphp

 <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
	<meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<style>	
@media screen and (min-width: 600px) {
    .container{
        text-align:center;padding-left:20%;
        padding-right:20%;
        max-width:100%;
    }
    table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table{
    width:100%;
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
    }
    
}

.btn-xs {
    padding: .125rem .25rem;
} 
.text-left {
    text-align: left !important
}
.pl-3 {
    padding-left: 1rem !important
}
   @media screen and (max-width: 599px) {
       
           table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
table{
    width:100%;
}
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
		<div style="text-align:left;background:#043e44;color:white;font-weight:bold;padding:15px; ">Today Staff Attendent Report : {{ $dateTime ?? '' }}
		
    <!-- Dear, {{ $first_name ?? '' }} {{ $last_name ?? '' }}-->
		</div>
		<div  style="background:url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU);padding:30px;background-size:100% 100%;">
		
<p style="text-align:left;color:cadetblue;font-size:23px">Welcome to the {{$getSetting['name'] ?? ''}},&nbsp;Jaipur </p>

<p style="text-align:left;margin-top:-10px">Today Staff Attendents Report is Following:</p>
<table>
    <tr>
        <th>Sr. No.</th>
        <th class="text-left pl-3">NAME</th>
        <th>STATUS</th>
    </tr>
    @if(!empty($staff))
        @php
            $i = 1
        @endphp
        @foreach($staff as $item)
            <tr>
                <td>{{ $i++ }}</td>
                <td class="text-left pl-3">{{ $item['Teacher']->first_name ?? '' }} {{ $item['Teacher']->last_name ?? '' }}</td>
                <td>
                    @if($item->attendance_status_id == 1)
                        <button type="button" class="btn btn-success btn-xs" style="background-color: #28a745; color: white;">Present</button>
                    @elseif($item->attendance_status_id == 2)
                        <button type="button" class="btn btn-danger btn-xs" style="background-color: #dc3545; color: white;">Absent</button>
                    @elseif($item->attendance_status_id == 3)
                        <button type="button" class="btn btn-info btn-xs" style="background-color: #17a2b8; color: white;">Work From Home</button>
                    @elseif($item->attendance_status_id == 4)
                        <button type="button" class="btn btn-warning btn-xs" style="background-color: #ffc107; color: black;">Half Day</button>
                    @elseif($item->attendance_status_id == 5)
                        <button type="button" class="btn btn-primary btn-xs" style="background-color: #6639b5; color: white;">Holiday</button>
                    @endif
                </td>
            </tr>    
        @endforeach
    @endif

</table>
<!--<p style="text-align:left;color:cadetblue;font-size:23px">
Username and password </p>
<p style="text-align:left;margin-top:-10px">
Here is your username and password:-<br></p>
<p style="text-align:left;margin-top:-10px;font-size:18px">
<strong style="margin-top:20px"><i>Username:- </i></strong>&nbsp;{{ $userName ?? '' }}<br>
<strong><i>Password:- </i></strong>&nbsp;{{ $password ?? '' }} 
</p>

<p style="text-align:left;margin-top:30px">

The email and mobile is the only way that the School will use to contact you so it is important you know how to access it.

</p>

<p style="line-height:35px;margin-top:30px">Welcome dear [  {{ $first_name ?? '' }}  ],<br><strong style="font-size:23px;" >{{$getSetting['name'] ?? ''}}<strong></p>-->
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
