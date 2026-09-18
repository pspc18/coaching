@php
$getSetting=Helper::getSetting();
@endphp
<!DOCTYPE html>
<html>
<head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  line-height: 30px;
}
.head{
    width: 100%;
    height: 150px;
    display: flex;
}
.lift{
    width: 40%;
    height: 150px;
    /*border: 1px solid rgb(0, 255, 64);*/
}
.right{
    width: 60%;
    height: 150px;
   /*border: 1px solid rgb(0, 255, 64);*/
}


</style>
</head>
<body style="width: 70%;
margin: 0 auto;">
<section style="background-color: #e1e1e1;">
    <div class="head">
    <div class="lift">
        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/mini_logo.png' }}'" width="40%" height="100px" style="border-radius: 65px;">
    </div>
    <div class="right">
        <h3>{{$getSetting['name'] ?? ''}}</h3>
         <h4> {{$teacher_us['address'] ?? ''}}</h4>
    </div>
    
    </div>
    <p><b>S. No.</b> {{$counter ?? ''}}</p>
<table style="width: 88%;margin-left: 5%;border: 3px solid black !important;">
  
  <tr>
    <td>Name </td>
    <td>{{$first_name ?? ''}} {{$last_name ?? ''}}</td>
    
  </tr>
  <tr>
    <td>Father's Name</td>
    <td>{{$father_name ?? ''}}</td>
    
  </tr>
 
  <tr>
    <td>Address</td>
    <td>{{$address ?? ''}}</td>
    
  </tr>
  <tr>
    <td>Building</td>
    <td> {{$building_id ?? ''}}</td>
    
  </tr>
  <tr>
    <td>Room No.</td>
    <td> {{$room_id ?? ''}}</td>
    
  </tr>
 
  <tr>
    <td>Mobile</td>
    <td>{{$mobile ?? ''}}</td>
    
  </tr>
  <tr>
    <td>Bed No.</td>
    <td>{{$bed_id ?? ''}}</td>
    
  </tr>
 
 
  
</table>
<br><br><br>
<p style="margin-left:5%;"><b>Your Hostel Assign Is Successfully In {{$getSetting['name'] ?? ''}}</b></p>
<br><br><br>
<p style="float: right;"><b><img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/seal.png' }}'" height="110px"><br>
    <u>Signature of Student</u></b></p>
</section>
</body>
</html>

