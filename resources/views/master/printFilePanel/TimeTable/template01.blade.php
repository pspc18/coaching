@php
$getSetting=Helper::getSetting();
$getTimePeriod = Helper::getTimePeriod();
$numbers = [
    0 => 'First',
    1 => 'Second',
    2 => 'Third',
    3 => 'Fourth',
    4 => 'Fifth',
    5 => 'Sixth',
    6 => 'Seventh',
    7 => 'Eighth',
    8 => 'Ninth',
    9 => 'Tenth',
    10 => 'Eleventh',
    11 => 'Twelfth',
    12 => 'Thirteenth',
    13 => 'Fourteenth',
    14 => 'Fifteenth',
    15 => 'Sixteenth',
    16 => 'Seventeenth',
    17 => 'Eighteenth',
    18 => 'Nineteenth',
    19 => 'Twentieth',
];

$newNumber = [];
@endphp

@foreach($getTimePeriod as $key =>$time)

@php
$newNumber[$time->id] = $numbers[$key];
@endphp

@endforeach

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Table</title>
    <link rel="stylesheet" href="{{ asset('public/assets/school/css/adminlte.min.css') }}">
    <style>
     
        body {
   font-family: Arial, sans-serif;
      font-size:12px;
      max-width:750px;
      margin: 10px auto 
     
   /* border: 0.5px solid; */
   }
   .student_img {
   width: 80px; 
   height:100; 
   margin-top: 5%;
   margin-left:20%;
   padding-bottom: 10px;
       
   }
   
   .row{
       margin-right: 0px;
   }
   .img_background_fixed{
     position: relative;
   }
   
   .img_absolute{
       position: absolute;
        top: 36px;
       width: 100%;
       display: flex;
       align-items: center;
       justify-content: center;
       height: 100%;
       right: 0;
   }
   
   .backhround_img{
       opacity: 0.3;
       width: 36%;
   }

   table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px;
        }
        .inner_table th{
            border: 1px solid #000;
            padding: 5px;
            /* background-color: #f2f2f2; */
        }
      
        .invoice-header {
            margin-bottom: 20px;
            text-align:'center'
        }
        .inner_table td{
            padding:5px
        }
        .ltr{
            text-align: left;
            border-right: none !important;
            padding:5px
        }
        .rtr{
            text-align:right;
        }

       #personal_detail th {
            border: 1px solid #000;
            text-align: left;
            padding: 8px;
            background:#dddddd
        }
       #personal_detail td {
            border: 1px solid #000;
            text-align: left;
            padding: 8px;
        }
        .img_bwm{
            width: 100px;
            text-align: right;
            height: 109px;
            padding: 3px;
            border: 2px dotted black;
            margin-bottom: -13px;
            margin-top: -17px;
        }
        
        .fontweight{
                font-weight: 600;
        }
         
   </style>
   <script>
       //window.print();
   </script>
</head>
<body class='page'>
<table style="background:#6639b5;color:white; padding: 30px;" >
			<tbody >
			<tr>
      <td rowspan='2' width='25%'>
          <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 150px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >
          </td>
      <td   width='60%' style="font-size:20px;text-align:center;"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
      <td width='15%'> 
      </td>
    
   </tr>
	<tr style="text-align:center;">
      <td width='60%'  style="text-align:center;">
      <p ><b >Address </b> {{$getSetting['address'] ?? ''}} </p>
      <p ><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}</p>
    </td>
      <td width='15%'>
          
      </td>
      </tr>

  </tbody>
  </table> 
 
<table style="margin-top: 5px ;border-top:3px solid #6639b5">

<thead>
  <tr>
      <td colspan='3' style="font-size:13px;text-align:center;"><h1>TIME TABLE</h1></td>
    </tr>
<tr>

                                    <table id="example1" class="table table-bordered table-striped dataTable dtr-inline ">
                                        <thead>
                                            <tr>
                                                <th>{{ __('common.SR.NO') }}</td>

                                                <th>{{ __('common.Class') }} </th>
                                                <th>{{ __('common.Subject') }} </th>
                                                <th>{{ __('master.Teacher Name') }}</th>
                                                <th>{{ __("Period's Name") }}</th>
                                                <th>{{ __('master.Time Periods') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(!empty($data))
                                            @php
                                            
                                                $i=1;
                                                $count = 0;
                                                $numbers = [1 => 'First',2 => 'Second',3 => 'Third',4 => 'Fourth',5 => 'Fifth',6 => 'Sixth',7 => 'Seventh',8 => 'Eighth',9 => 'Ninth',10 => 'Tenth',11 => 'Eleventh',12 => 'Twelfth',];
                                                $class_type_id = '';
                                            @endphp
                                            
                                            @foreach ($data as $item)
                                                @php
                                                    if($class_type_id == '')
                                                    {
                                                        $class_type_id == $item->class_type_id;
                                                    }
                                                    if($class_type_id == $item->class_type_id)
                                                    {
                                                   
                                                        $count++;
                                                    }
                                                    else
                                                    {
                                                        $class_type_id = $item->class_type_id;
                                                        $count = 0;
                                                    }
                                                @endphp
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item['class_name'] ?? '' }}</td>
                                                <td>{{ $item['subject_name'] ?? '' }}</td>
                                                <td>{{ $item['first_name'] ?? '' }} {{ $item['last_name'] ?? '' }} </td>
                                                <td>{{$newNumber[$item->time_period_id] ?? ''}}</td>
                                                <td>{{ date('h:i:s A', strtotime($item->from_time)) ?? ''  }} To {{ date('h:i:s A', strtotime($item->to_time)) ?? ''  }} </td>
                                            </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                    


    </tr>






    </thead>
    </table>



  <table style='margin-top: 15px; border-bottom:30px solid #6639b5;' >

    <tfoot style='border:1px solid black;padding-bottom:10px'>
            <tr>
            <td style="text-align: center;"></td>
                <td style="text-align: right">
                <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" style="height:90px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'">

                </td>
                
            </tr>
            <tr>
            <td style="text-align: left;">&nbsp;&nbsp;&nbsp;Signature</td>
                <td style="text-align: right;padding:10px">
            Seal & Sign    
            </td>
                
            </tr>
           
           
           
        </tfoot>
    </table>

</body>
</html>

