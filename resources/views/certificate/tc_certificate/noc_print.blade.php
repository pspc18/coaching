
<head>
    <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/all.min.css') }}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">
</head>

@php
$getSetting=Helper::getSetting();
@endphp




</head>



<body style="font-size: larger; border:1px solid black;">

  

  @include('print_file.print_header')
  
    <div class="container"pl-2">
    
    <br>
    <br>
    <h2><b>No Dues Certificate Letter</b></h2>
    <br>
    <h3><b>SIMPTION TECH PVT LTD</b></h3>
    <BR>
    <p>C-4,2ND FLOOR ,PRABHAT SQUARE BHOPAL</p>
    <P>18-06-2022</P>
    <P><B>9024613923</B></P>
    <br>
    <br>

    <p>To Whom It May Concem:</p>
    <p>This Letter is to Certify That Mr./Miss <b style="border-bottom: 2px dotted;"> &nbsp; {{ $data->name ?? '' }} C/O &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></p>
<p>With Admission Number &nbsp; &nbsp; <b style="border-bottom: 2px dotted;">&nbsp;&nbsp;&nbsp;   &nbsp;&nbsp;&nbsp;{{ $data->students_adnission_no ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    </b>  &nbsp;has no dues towards &nbsp;<b style="border-bottom: 2px dotted;">  &nbsp; TEACH PVT LTD &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></p>
<p>as Of Date <b style="border-bottom: 2px dotted;">&nbsp; {{ $data->issue_date ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;</b>    &nbsp;Mr./Miss &nbsp; <b style="border-bottom: 2px dotted;">&nbsp; &nbsp; {{ $data->name ?? '' }}&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</b> confirms to his best<b  style="border-bottom: 2px dotted;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></p>
<p>Knowledge That he has surrenderd all <b style="border-bottom: 2px dotted;">&nbsp;&nbsp;&nbsp;{{$getSetting['name'] ?? ''}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></p>
<p>and cleared all of overdue amount as of the mantioned date.if it turnes out later that some due have</p>
<p>been missed, Mr./Miss <b style="border-bottom: 2px dotted;">&nbsp;&nbsp;{{ $data->name ?? '' }}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp;shall revert back to<b style="border-bottom: 2px dotted;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></p>
<p><b style="border-bottom: 2px dotted;">&nbsp;{{$getSetting['name'] ?? ''}}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b>&nbsp; for further settlement.</p>
<br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
</table>
</div>

@include('print_file.print_footer')

</html>
<script type="text/javascript">
 window.print();
</script>