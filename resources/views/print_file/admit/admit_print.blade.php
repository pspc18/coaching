
<head>
<title>Mark Sheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
@php
$getSetting=Helper::getSetting();

@endphp

<div class="row">
<div class="container border border-dark" style="border-width:3px !important;">
<table style="width:100%">

    <tr>
        <td style="width:20%"><img style="width: 215px; margin-top:30px; margin-left:50px;" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"></td>
        <td class="text-center"><h1><b>{{$getSetting['name'] ?? ''}}</b></h1></td>
       <td style="width:20%"><img style="width: 215px; margin-top:30px; margin-left:50px;" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'"></td>
    </tr>
</table>

<table style="width:100%;font-size:126%;margin-top: -1%;">
    <tr>
       <td class="text-center" ><p><b>{{$getSetting['address'] ?? ''}}</b></p></td>
    </tr>
    
    <tr>
        <td class="text-center"></td>
    </tr>
   
  

 </table>
  <hr width="100%;" style="border:2px solid black;padding-left: 29px;margin-left: -18px;">

<div class="row">
<div class="col-sm-3 text-left"><b>Name of Student:</b></div>
<div class="col-sm-5 text-left"><b>{{$data['name'] ?? ''}}</b></div>
<div class="col-sm-3 text-left"><b>Student Roll No.:</b></div>
<div class="col-sm-1 text-center"><b>{{$getSetting['student_roll_no'] ?? ''}}</b></div>
</div>
<div class="row">
<div class="col-sm-3 text-left"><b>Father's Name:</b></div>
<div class="col-sm-5 text-left"><b></b></div>
<div class="col-sm-3 text-left"><b>Class:</b></div>
<div class="col-sm-1 text-center"><b></b></div>
</div>
<div class="row border-top border-dark" style="border-width:3px !important">
<div class="col-sm-3 text-center border-right border-dark" style="border-width:2px !important"></div>
<div class="col-sm-9 text-center"><b>RESULT</b></div>
</div>

<div class="row  border-dark" style="border-width:2px !important;">
<div class="col-sm-3 text-center border-right border-top border-dark" style="border-width:2px !important"><b>Subject Name</b></div>
<div class="col-sm-2 text-center border-right border-top border-dark" style="border-width:2px !important"><b>Mix.</b></div>
<div class="col-sm-2 text-center border-right border-top border-dark" style="border-width:2px !important"><b>Mini.</b></div>
<div class="col-sm-2 text-center border-right border-top border-dark" style="border-width:2px !important"><b>Obt</b></div>
<div class="col-sm-3 text-center  border-top border-dark" style="border-width:2px !important"><b>Grade</b></div>
</div>
<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>80</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>80</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>80</b></div>
<div class="col-sm-3 text-center p-2"><b>A+</b></div>
</div>
<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>76</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>66</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>65</b></div>
<div class="col-sm-3 text-center p-2"><b>A+</b></div>
</div>
<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>65</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>66</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>76</b></div>
<div class="col-sm-3 text-center p-2"><b>A+</b></div>
</div>

<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>77</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>87</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>55</b></div>
<div class="col-sm-3 text-center p-2"><b>E2</b></div>
</div>
<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>56</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>66</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>87</b></div>
<div class="col-sm-3 text-center p-2"><b>E2</b></div>
</div>

<div class="row border-top border-dark" style="border-width:2px !important">
<div class="col-sm-3 text-left p-2 border-right border-dark" style="border-width:2px !important"><b>{{$getSetting['subject_name'] ?? ''}}</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>98</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>77</b></div>
<div class="col-sm-2 text-center p-2 border-right border-dark" style="border-width:2px !important"><b>83</b></div>
<div class="col-sm-3 text-center p-2"><b>A+</b></div>
</div>
<div class="row border-top border-bottom  border-dark" style="border-width:2px !important">
<div class="col-sm-3 border-right border-dark" style="border-width:2px !important"><b>PERCENTAGE</b></div>
<div class="col-sm-9 text-center p-1">95%</div>
</div>
<div class="row mt-5" style=" font-size:12px;">
<div class="col-sm-6  p-1"><b>CLASS TEACHER'S SIGNATURE</b></div>
<div class="col-sm-6 text-right p-1"><b>PRINCIPAL'S SIGNATURE & SEAL</b></div>
</div>
</div>
</div>
<script type="text/javascript">
window.print();
</script>