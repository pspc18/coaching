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
        text-align:center;
      padding-left: 10%;
padding-right: 10%;
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
     border-collapse: collapse;
}
table th {
    border: 1px solid;
     
  
}
table td {
    border: 1px solid;
     
  
}
tr {
    width: 100%;
}

    
    .main{
        background:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU');
        padding:30px;
        background-size:100% 100%;
    }
    .style1{
    text-align:left;
    color:cadetblue;
    font-size:23px;
}
.style2{
   text-align:left;
   margin-top:-10px;
   font-size:18px;
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
        padding-left:0%;
        padding-right:0%;
        
    }
     .main{
        background:url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTjtYjCYO5NxCraV6exBWBCkhmNRpMQng5Miw&usqp=CAU');
        padding:10px;
        background-size:100% 100%;
    }
    .hide{
        display:none;
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
    table {
    border: 1px solid;
    width: 100%;
 font-size: 8px;
     border-collapse: collapse;
}
table th {
    border: 1px solid;
     
  
}
table td {
    border: 1px solid;
     
  
}
.style1{
    text-align:left;
    color:cadetblue;
    font-size:12px;
}
.style2{
   text-align:left;
   margin-top:-10px;
   font-size:10px;
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
		
     Exam Report for {{$exam_name->name ?? ''}} held on {{$exam_name->exam_date ?? ''}} 
		</div>
		<div  class="main">
		
<p class="style1">Welcome to the {{$getSetting['name'] ?? ''}},&nbsp;Jaipur </p>

<!--<p style="text-align:left;margin-top:-10px">To use the School's IT facilities you will need a Rukmani's username and password to log in to the network.</p>-->

<p class="style1">
Student Exam Report  </p>
<p style="text-align:left;margin-top:-10px">
Here is student's exam report card:-<br></p>
<p class="style2">
					
						
							<table class="table">
								<thead>
									<tr role="row">
										<th class="hide">Sr. No.</th>
										<th style="text-align:center;">Name</th>
										<th class="hide"style="text-align:center;">Father Name</th>
										<th class="hide"style="text-align:center;">Mobile</th>
										<th class=""style="text-align:center;">Total Question</th>
										<th style="text-align:center;">Correct Answer</th>
										<th style="text-align:center;">Wrong Answer</th>
										<th class="hide"style="text-align:center;">Skipped Answer</th>
										<th style="text-align:center;">Result</th>
										<th style="text-align:center;">Time Taken</th>
									
								</thead>
								<tbody> 
								@if(!empty($data)) 
    								@php 
    								    $i=1 
    								@endphp 
								@foreach ($data as $item)
									<tr>
									<td class="hide">{{$i++}}</td>
									<td>{{$item['first_name'] ?? ''}} {{$item['last_name'] ?? ''}}</td>
									<td class="hide">{{$item['father_name'] ?? ''}}</td>
									<td class="hide">{{$item['mobile'] ?? ''}}</td>
									<td class="">{{$item['total_ques'] ?? ''}}</td>
									<td>{{$item['correct_ans'] ?? ''}}</td>
									<td>{{$item['wrong_ans'] ?? ''}}</td>
									<td class="hide">{{$item['skip_ques'] ?? ''}}</td>
									<td >{{$item['percentage'] ?? ''}}%</td>
									<td >{{$item['time'] ?? ''}}</td>
									</tr> 
							@endforeach
							@endif
								</tbody>
							</table>
</div>
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
