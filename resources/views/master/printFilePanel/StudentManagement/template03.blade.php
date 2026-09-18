@php
$getSetting=Helper::getSetting();
//dd($data);
@endphp
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Admission Form</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: math;
}

body{
    background:#eee;
}

.page{
    width:210mm;
    min-height:297mm;
    margin:auto;
    background:#fff;
    border:3px solid #000;
    padding:8px;
}

.inner-border{
    border:1px solid #000;
    min-height:280mm;
    padding:10px;
}

.header{
    text-align:center;
}

.header h1{
    font-size:42px;
    font-weight:bold;
    font-family: emoji;
    background-color: #aeadad;
}

.header h2{
    font-size:26px;
    background-color: #c4c4c4;
}

.session{
    background:#ddd;
    padding:6px;
    font-size:22px;
    font-weight:bold;
    /* margin-top:10px; */
}

.logo-area{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin:15px 0;
}

.logo{
    width:90px;
    height:90px;
}

.form-title{
    border: 2px solid #000;
    padding: 10px 40px;
    font-size: 38px;
    font-weight: bold;
    box-shadow: 6px 6px #999;
    font-family: system-ui;
}

.reg-row{
    display:flex;
    justify-content:space-between;
    background:#ddd;
    padding:6px;
    font-weight:bold;
    margin-bottom:0px;
    margin-top: -10px;
}

.table{
    width:100%;
}

.table td{
    padding:10px 0;
    font-size:18px;
}

.line{
    border-bottom:1px dotted #000;
    display:inline-block;
    width:428px;
}

.photo{
    width:120px;
    height:140px;
    border:1px dashed #666;
    text-align:center;
    line-height:140px;
    font-weight:bold;
}

.class-title{
    background:#ddd;
    text-align:center;
    font-size:22px;
    font-weight:bold;
    margin-top:20px;
    padding:5px;
}

.class-list{
    padding:15px;
    font-size:18px;
    line-height:45px;
}

.signature{
    display:flex;
    justify-content:space-between;
    margin-top:139px;
    text-align:center;
    font-weight:bold;
}

.signature div{
    width:30%;
    border-top:1px solid #000;
    padding-top:8px;
}

@media print{

body{
    background:white;
}

.page{
    margin:0;
    border:none;
    width:100%;
}

}
</style>

</head>
<body>

<div class="page">

<div class="inner-border">

<div class="header">

<h1>{{$getSetting['name'] ?? ''}}</h1>

<h2>11th / 12th JET / ICAR / A.G SUPERVISOR</h2>
@php
    //dd($data);
    
    $session = DB::table('sessions')
                ->where('id', $data->session_id)
                ->first();
@endphp
<div class="session">
SESSION {{ $session->from_year ?? '' }}-{{ $session->to_year ?? '' }}
</div>

<div class="logo-area">

<img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" class="logo">

<div class="form-title">
ADMISSION FORM
</div>

<img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" class="logo">

</div>

</div>

<div class="reg-row">
<div>Reg. No. {{ $data['admissionNo'] ?? '' }}</div>
<div>Date : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 148px;">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</span></div>
</div>

<table class="table">

<tr>

<td width="75%">
Student Name : <span class="line">{{ $data['first_name'] ?? '' }} {{ $data['last_name'] ?? '' }}</span>
</td>

<td rowspan="5" align="center">
<div class="photo">
    <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$data['image'] ?? '' }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/user_image.jpg' }}'" style="width: 100%;height: 138px;">
</div>
</td>

</tr>

<tr>
<td>Father Name : <span class="line">{{ $data['father_name'] ?? '-' }}</span></td>
</tr>

<tr>
<td>Mother Name : <span class="line">{{ $data['mother_name'] ?? '-' }}</span></td>
</tr>

</table>

<table>
    <tr>
        <td style="padding: 10px 0;font-size: 18px;">
            FATHER'S Ph. NO. : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 148px;">{{ $data['father_mobile'] ?? '-' }}</span>
            STUDENT Ph. NO.: <span style="border-bottom: 1px dotted #000;display: inline-block;width: 148px;">{{ $data['mobile'] ?? '-' }}</span>
        </td>
    </tr>
    
</table>

<table style="margin-top: 0px;padding: 10px 0;font-size: 18px;">
    <tr>
        <td colspan="2">
            Address : <span style="width:489px" class="line">{{ $data['address'] ?? '-' }}</span>
        </td>
    </tr>
</table>

<table >
    <tr>
        <td style="width: 99px;"></td>
        <td style="padding: 10px 0;font-size: 18px;">
            Village : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 198px;">{{ $data['village_city'] ?? '-' }}</span>
            Tahsil : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 198px;">{{ $data['tehsil'] ?? '-' }}</span>
        </td>
        <td style="width: 99px;"></td>
    </tr>
    <tr>
        <td style="width: 99px;"></td>
        <td style="padding: 10px 0;font-size: 18px;">
            District : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 198px;">{{ $data['district'] ?? '-' }}</span>
            State : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 198px;"></span>
        </td>
        <td style="width: 99px;"></td>
    </tr>
    <tr>
        <td style="width: 99px;"></td>
        <td style="padding: 10px 0;font-size: 18px;">
            Pin Code  : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 168px;">{{ $data['pincode'] ?? '-' }}</span>
            Reference : <span style="border-bottom: 1px dotted #000;display: inline-block;width: 198px;"></span>
        </td>
        <td style="width: 99px;"></td>
    </tr>


    <tr>
        <td colspan="4" style="padding: 10px 0;font-size: 18px;">
            Before School Name :
            <span style="border-bottom: 1px dotted #000;display: inline-block;width: 498px;" class="">{{ $data['previous_school'] ?? '-' }}</span>
        </td>
    </tr>
</table>




<div class="class-title">
CLASS SESSION
</div>

<div class="class-list">
{{ $data['ClassTypes']['name'] ?? '-' }}
            @if (!empty($data['Section']['name']))
                ({{ $data['Section']['name'] }})
            @endif
<!-- ★ 11th [HM.... / EM....] (A.G/BIO/MATH)<br>

★ 12th [HM.... / EM....] (A.G/BIO/MATH)<br>

★ JET [HM.... / EM....]<br>

★ ICAR [HM.... / EM....]<br>

★ SUPERVISOR [....] -->

</div>

<div class="signature">

<div>
STUDENT SIGNATURE
</div>

<div>
PARENTS SIGNATURE
</div>

<div>
HEAD OF INSTITUTE SIGNATURE
</div>

</div>

</div>

</div>

</body>
</html>