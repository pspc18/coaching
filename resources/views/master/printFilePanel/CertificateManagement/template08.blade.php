

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sport Certificate</title>
</head>
@php 
$getSetting=Helper::getSetting();
//dd($getSetting);
@endphp

<style>
    @page{
    margin:0;
}
body{
    margin: 0px auto;
    color: #010068;
}

.print_div{
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    background :url('{{ env("IMAGE_SHOW_PATH").'/default/print_file_samples/CertificateManagement/template08.jpg' }}');
    background-size: 100% 100%;
    overflow: hidden;
}

    


        .backhround_img {
            opacity: 0.3;
            width: 34%;
        }
.school{
   position: absolute;
    left: 20%;
    font-size: 60px;
    top: 12%;
    color: #0566bc;
    font-family: ui-serif;
    font-weight: 900; 
}
.name{
    position: absolute;
    top: 42%;
    left: 38%;
    font-size: 37px;
}
.event{
    position: absolute;
    top: 56%;
    left: 44%;
    font-size: 23px;
}
.date{
    position: absolute;
    top: 62%;
    left: 38%;
    font-size: 21px;
}
.rank{
    position: absolute;
    top: 62%;
    left: 52%;
    font-size: 23px;
}
.sign{
    position: absolute;
    top: 72%;
    width: 100px;
    left: 46%;
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
            <h1 class="name">{{$data['stu_first_name'] ?? ''}} {{$data['stu_last_name'] ?? ''}}</h1>
            <h1 class="event">{{$data['event_type'] ?? ''}}</h1>
            <h1 class="date">{{date('d-m-Y', strtotime($data['organized_date'])) ?? '' }}</h1>
            <h1 class="rank">{{$data['rank'] ?? ''}}</h1>
            <img class="sign" src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" alt="">
        </div>
    </div>
</body>

<!--<script type="text/javascript">-->
<!--   window.print();-->
<!-- </script>-->

</html>