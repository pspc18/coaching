<!DOCTYPE html>
@php
$getSetting=Helper::getSetting();
@endphp
<html>
<head>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
.text_name{
    text-align:left;
}
.contant{

background-image: linear-gradient(white,#cde61d,#de5db7);
}
</style>
</head>
<body>


<table style="width:100%" class="contant">
  <tr>
    <th style="width: 20%;"><img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/mini_logo.png' }}'" style="width: 61%;
margin-left: -30%;"></th>
    <th colspan="2" ><h1>{{$getSetting['name'] ?? ''}}</h1><br><h3>{{$getSetting['address'] ?? ''}}</h3></th>
  </tr>
<tr>
    <th colspan="12" class="text_name "><h3> Dear , {{$first_name ?? ''}} {{$last_name ?? ''}} Your Admission Successfully</h3></th>
</tr>
<tr>
    <th>Name </th> 
    <th>{{$first_name ?? ''}} {{$last_name ?? ''}} </th> 
</tr>
<tr>
    <th>Mobile </th> <span> </span>
    <th>{{$mobile ?? ''}} </th> 
</tr>
<tr>
    <th>Email </th> 
    <th>{{$email ?? ''}}</th> 
</tr>
<tr>
    <th>Father's Name </th> 
    <th>{{$father_name ?? ''}}</th> 
</tr>
<tr>
    <th>Address  </th> 
    <th>{{$address ?? ''}}</th> 
</tr>
<tr>
    <th>Cabin No </th> 
    <th>{{$cabin_id ?? ''}} </th>
</tr>

</table>
</body>
</html>

