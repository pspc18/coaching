
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
#p {
color: #fff;
background-color: #28a745;
border-color: #28a745;
box-shadow: none;   
}
#a {
color: #fff;
background-color: #dc3545;
border-color: #dc3545;
box-shadow: none;   
}
#hf {
color: #1f2d3d;
background-color: #ffc107;
border-color: #ffc107;
box-shadow: none;  
}
#h {
color: #fff;
background-color: #6639b5;
border-color: #6639b5;
box-shadow: none;    
}
#w {
color: #fff;
background-color: #17a2b8;
border-color: #17a2b8;
box-shadow: none;  
}
table {
    border: 1px solid;
    width: 100%;
}
tr {
    width: 100%;
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
		
     Attendnce Report for {{ $date ?? '' }} 
		</div>
		<div  style="background:url(https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU);padding:30px;background-size:100% 100%;">
		
<p style="text-align:left;color:cadetblue;font-size:23px">Welcome to the {{$getSetting['name'] ?? ''}},&nbsp;Jaipur </p>

<!--<p style="text-align:left;margin-top:-10px">To use the School's IT facilities you will need a Rukmani's username and password to log in to the network.</p>-->

<p style="text-align:left;color:cadetblue;font-size:23px">
Today Collection Details Here Is Your School's Today Collection Details </p>
<p style="text-align:left;margin-top:-10px">
Here is your staff's attendance status:-<br></p>
<p style="text-align:center;margin-top:-10px;font-size:18px">
    DATE-29-07-2022
							<table class="table">
								
									<tr role="row">
										<th>Sr. No.</th>
										<th style="text-align:left;">Particular</th>
											<th>Amount.</th>
								</tr>
							 
									<tr>
							
										<td></td>
										<td style="text-align:left;"><b>Fees Collection</b></td>
									
										</tr>
										<tr>
										<td>1.</td>
								        <td style="text-align:left;">Balance Sheet</td>
										<td><i class="fa fa-money"></i>₹ 200</td>
										    
										  </tr>  
										<tr>
										<td>2.</td>
								        <td style="text-align:left;">Total Collection Of Fees</td>
										<td><i class="fa fa-money"></i>₹ 500</td>
										    
										  </tr>  
										<tr>
										<td>3.</td>
								        <td style="text-align:left;">Total Hostal Fees</td>
										<td><i class="fa fa-money"></i>₹ 2500</td>
										    
										  </tr>  
										<tr>
										<td>4.</td>
								        <td style="text-align:left;">Total Library Fees</td>
										<td><i class="fa fa-money"></i>₹ 1200</td>
										    
										  </tr>  
										  	<tr>
							
										<td></td>
										<td style="text-align:left;"><b>Expanse</b></td>
									
										</tr>
										  
										<tr>
										<td>5.</td>
								        <td style="text-align:left;">Today Expanse</td>
										<td><i class="fa fa-money"></i>₹ 25000</td>
										    
										  </tr>  
									
								@if(!empty($teacher)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($teacher as $item)
								
										
                                            @if($item['attendance_status_id'] == 1)
                                                <button id="p">Present</button>
                                            @elseif($item['attendance_status_id'] == 2)
                                                <button id="a">Absent</button>
                                            @elseif($item['attendance_status_id'] == 3)
                                                <button id="w">Work From Home</button>
                                            @elseif($item['attendance_status_id'] == 4)
                                                <button id="hf">Half-day</button>
                                            @elseif($item['attendance_status_id'] == 5)
                                                <button id="h">Holiday</button>
                                            @endif 
										
								
								@endforeach 
								@endif 
								</tbody>
							</table>

</p>

<!--<p style="text-align:left;margin-top:30px">

The email and mobile is the only way that the School will use to contact you so it is important you know how to access it.

</p>-->

<!--<p style="line-height:35px;margin-top:30px">Welcome dear [    ],<br><strong style="font-size:23px;" >{{$getSetting['name'] ?? ''}}<strong></p>-->
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
