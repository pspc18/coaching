<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /> 
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Achievement Certificate</title>
</head>
@php
$getSetting=Helper::getSetting();
@endphp
<style>
    @page{
    margin:0;
}
body{
    margin: 0px auto;
}

.print_div{
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    background :url('{{ env("IMAGE_SHOW_PATH").'/default/print_file_samples/CertificateManagement/template06.jpg' }}');
    background-size: 100% 100%;
    overflow: hidden;
}

    


        .backhround_img {
            opacity: 0.3;
            width: 34%;
        }
.school{
    position: absolute;
    left: 22%;
    font-size: 57px;
    top: 15%;
}
.name{
    position: absolute;
    top: 51%;
    left: 40%;
    font-size: 37px;
}
.f_name{
    position: absolute;
    top: 59.5%;
    left: 35%;
    font-size: 20px;
}
.address{
    position: absolute;
    top: 59.5%;
    left: 63%;
    font-size: 20px;
}
.class{
    position: absolute;
    top: 68%;
    left: 43%;
    font-size: 20px;
}
.year{
    position: absolute;
    top: 68%;
    left: 68%;
    font-size: 20px;
}
.sr_no{
    position: absolute;
    top: 68%;
    left: 87%;
    font-size: 20px;
}
.dob{
    position: absolute;
    top: 72%;
    left: 55%;
    font-size: 20px;
}
.event{
    position: absolute;
    top: 76%;
    left: 32%;
    font-size: 20px;
}
.date{
    position: absolute;
    top: 81%;
    left: 46%;
    font-size: 26px;
}
        
        @media print {
            .print-page-break {
                page-break-after: always;
                margin-top:0px;
            }
        }
        
     
</style>
<body>
   <div class="print_div">
        
        <div class="background_img">
            <h1 class="school">{{$getSetting['name'] ?? ''}}</h1>
            <h1 class="name">{{$data['first_name'] ?? ''}} {{$data['last_name'] ?? ''}}</h1>
            <h1 class="f_name">{{$data['stu_father_name'] ?? ''}}</h1>
            <h1 class="address">{{ strlen($data['address'] ?? '') > 35 ? substr($data['address'], 0, 35) . '...' : ($data['address'] ?? '') }}</h1>
            <h1 class="class">{{$data['class_name'] ?? ''}}</h1>
            <h1 class="year">{{$data['from_year'] ?? ''}}-{{$data['to_year'] ?? ''}} </h1>
            <h1 class="sr_no">{{$data['admissionNo'] ?? ''}}</h1>
            <h1 class="dob">{{date('d-m-Y', strtotime($data['dob'])) ?? '' }}</h1>
            <h1 class="event">{{$data['achievement_for'] ?? ''}}</h1>
            <h1 class="date">{{ date('d-m-Y',strtotime($data['iessu_date'])) ?? ''}}</h1>
         
              
      
           
    <!--
              <h1 class="name_text">{{ $data->stu_first_name ?? '' }} {{ $data->stu_last_name ?? '' }}</h1>
              <h1 class="class_text">{{ $data->class_name ?? '' }}</h1>
              <h1 class="event_text">{{$data['event_type'] ?? ''}}</h1>
              <h1 class="issue_date">@if(!empty($data->organized_date)){{ date('d-M-Y', strtotime($data->organized_date)) ?? '' }}@endif</h1>
              <h1 class="issue_date_2">@if(!empty($data->organized_date)){{ date('d-M-Y', strtotime($data->organized_date)) ?? '' }}@endif</h1>-->
        </div>
    </div>
</body>

<!--<script type="text/javascript">-->
<!-- window.print();-->
<!--</script>-->

</html>