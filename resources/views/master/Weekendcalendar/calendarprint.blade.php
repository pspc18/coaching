@php
$getSetting=Helper::getSetting();
//dd($getSetting);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar Print</title>
    <style>
     
        body {
  // font-family: Arial, sans-serif;
      font-size:12px;
      max-width:750px;
      margin: 10px auto 
     
   /* border: 0.5px solid; */
   }

   table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px;
        }
      
      
        .ltr{
            text-align: left;
            border-right: none !important;
            padding:5px
        }
        .rtr{
            text-align:right;
        }
       .calendar_font{
                font-family: serif;
                font-size: 28px;
        }
       .session_font{
            margin-top: -19px;
            font-family: auto;
            font-size: 20px;
        }
       .mont_font{
            font-family: auto;
            font-size: 25px;
            margin-bottom: 0px;
        }
       .border_td{
                border: 1px solid black;
                height: 44px;
                font-size: 19px;
                text-align: center;
        }
   </style>
</head>
<body class='page'>
<table style="border: 1px solid black;color:black; padding: 30px;">
			<tbody >
					<tr>
      <td rowspan='2' width='20%'>
          <img  src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 119px;height: 100px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" >
          </td>
      <td   width='70%' style="font-size:27px;text-align:center;font-family: math;"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span>
      
      </td>
      <td width='20%'> 
      </td>
    
   </tr>
	<tr style="text-align:center;">
      <td width='60%'  style="text-align:center;font-size: 15px;">
      <p style="margin-bottom: -10px;">
        <b>Gmail</b> : {{$getSetting['gmail'] ?? ''}} 
        <b>Mobile No.</b> : {{$getSetting['mobile'] ?? ''}} 
      </p>
      <p>
        <b>Address</b> : {{$getSetting['address'] ?? ''}} 
      </p>
      
    </td>
      </tr>

      @php
        $month_name = DB::table('months')->where('id',$id)->first();
        $session = DB::table('sessions')->where('id',Session::get('session_id'))->first();
      @endphp
	<tr>
      <td width='60%' colspan="3"  style="text-align:center;font-size: 15px;">
        <h2 class="calendar_font">Academic Calendar</h2>
        <h3 class="session_font">Session {{ $session->from_year ?? '' }}-{{ $session->to_year ?? '' }}</h3>
        <!--<p class="mont_font">{{ $month_name->name ?? '' }} {{ $session->from_year ?? '' }}</p>-->
    </td>
      </tr>


    <table >
        <thead>
				<tr>
                <th class="border_td">Date</th>
                <th class="border_td">Day</th>
                <!--<th class="border_td">Special Day</th>-->
                <th class="border_td">Event/Schedule</th>
               </tr>
        </thead>       
		<tbody>
          @if(!empty($data))
           
            @foreach ($data as $item)
           
            <tr>
                <td class="border_td">{{date('d-M-Y', strtotime($item['date'])) ?? '' }}</td>
                <td class="border_td">{{ $item['day'] ?? '-' }}</td>
                <!--<td class="border_td">{{ $item['special_day'] ?? '-' }}</td>-->
                <td class="border_td">{{ $item['event_schedule'] ?? '-' }}</td>
            </tr>
            @endforeach
            @endif
       </tbody>
    </table>

     
  </tbody>
  </table> 
 




  
    </thead>
    </table>



 

</body>
</html>

