@php
    $getSetting=Helper::getSetting();
    $getSession=Helper::getSession();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admit Cards</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            max-width: 714px;
            margin: 20px auto;
            /*max-width: 750px;
            margin: 0 auto;*/
            /* border: 0.5px solid; */
            line-height: 16px;
        }

        .student_img {
            width: 80px;
            height: 100;
            margin-top: 5%;
            margin-left: 20%;
            padding-bottom: 10px;

        }

        p {
            margin-bottom: 0px;
            margin-top: 0px;
        }

        .lheight {
            line-height: 20px;
        }

        .row {
            margin-right: 0px;
        }

        .img_background_fixed {
            position: relative;
        }

        .img_absolute {
            position: absolute;
            top: 80px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            right: 0;
        }

        .backhround_img {
            opacity: 0.3;
            width: 34%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0px;
        }

        .inner_table th {
            border: 1px solid #000;
            padding: 4px;
            /* background-color: #f2f2f2; */
        }

        .invoice-header {
            margin-bottom: 20px;
            text-align: 'center'
        }

        .inner_table td {
            padding: 4px
        }

        .ltr {
            text-align: left;
            border-right: none !important;
        }

        .pltr {
            padding-left: 20px;
            margin: 10px;
        }

        .rtr {
            text-align: right;
        }

        .ctr {
            text-align: center;
        }

        #personal_detail th {
            border: 1px solid #000;
            text-align: left;
           /* padding: 5px 0px;*/
            font-weight:600;
        }

        #personal_detail td {
            border: 1px solid #000;
            text-align: left;
/*            padding: 5px 0px;
*/            font-weight:600;
        }

        .ptr {
            padding: 0px;
        }

        .bdtr {
            border: 1px solid black;
        }

        .bg_theme {
            /*background-color: gainsboro;*/
              background-color: #6639b5;
             color: white;
        }

        .bg_dark_theme {
            background-color: gray;
            color: white;
        }

        .striped {
            background-color: gainsboro;
        }

        .inner_text {
            font-size: 16px;
            font-weight: 600;
        }
        
        .plt{
            padding-left:10px !important;
        }
        
        .profile_pic{
            width: 100px;
            height: 100px;
            border: 1px solid black;
            border-radius: 10px;
            margin-top: -20px;
        }
        
        .print-page-break{
            margin-bottom:50px;
        }
        .print{
            margin-bottom: 50px;
        }
        
   
            .print-page-break {
                page-break-after: always;
            }
    
        .border_none{
         border: 0px solid #000 !important;;
        }
    </style>
</head> 

<body class='page'>
@php
    $session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
@endphp
 @if(!empty($data))
    @foreach($data as $key => $item)
    
    @php
  // dd($data);
    @endphp
    <div class=" @if(in_array($key, [1, 3, 5 ,7 ,9 ,11 ,13 , 15,17.19,21,23,25,27,29,31,33,35,37,39,41,43,45,47,49,51,53,55,57,59,61,63,65,67])) print-page-break @else print @endif"" >
    <table>
        <tbody class="">
            <tr>
                <td style="border: 1px solid black;" rowspan='1' colspan="12"width='100%'>
                  @if(Session::get('branch_id') == 1)
                  <img src="{{ env('IMAGE_SHOW_PATH').'/default/'}}KIDS_logo.jpg" style="width: 100%;">
                  @else
                            <img src="{{ env('IMAGE_SHOW_PATH').'/default/'}}Senior.jpg" style="width: 100%;">
        
                  @endif
          </td>
             
            </tr>
        </tbody>

        <tfoot>
            <tr style="border: 1px solid black;">
                <td colspan="12">
                    <p
                        style='text-align:center; font-weight:600;line-height:20px;margin-top:0px;font-size:15px; margin: 0px;'>
                         Admit Card <br>
                        (Academic Session {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}) - {{$item->exam_name ?? ''}}
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>

    <table id='personal_detail'>
        <tbody>
            <tr>
                <th class="border_none plt">Exam Roll No. :- {{ $item->exam_roll_no ?? '' }}</th>
                <td class="border_none plt">Class :- {{$item->class_name ??''}} </td>
                <th rowspan="2" style="text-align:center;" class="border_none plt">
                    <!--<img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$item->student_profile_image }}" class="profile_pic" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg'}}'" alt="profile">-->
                </th>
            </tr>
            <tr>
                <th class="border_none plt">Student Name :- {{$item->first_name ?? ''}} {{ $item->last_name ?? ''}}</th>
                <td class="border_none plt">Father's Name :- {{$item->father_name ?? ''}}</td>
            </tr>
        </tbody>
    </table>

  
    <table style=''>
        <tbody>
            @php   
         
                $sub = DB::table('examination_schedules')->select('examination_schedules.*','subject.name as subject_name')
                      ->leftjoin('subject as subject','subject.id','examination_schedules.subject_id')
                      ->where('examination_schedules.class_type_id',$item->class_type_id)
                      ->where('examination_schedules.examination_schedule_id',$item->examination_schedule_id)
                      ->where("examination_schedules.session_id", Session::get("session_id"))
                      ->where("examination_schedules.branch_id", Session::get("branch_id"))
                      ->where('examination_schedules.exam_id',$item->exam_id)
                      ->where('examination_schedules.deleted_at',null);
                    if ($item->class_orderBy > 10) {
                   // dd($item->orderBy);
                        if (!is_array($item->stream_subject)) {
                            $item->stream_subject = explode(',', $item->stream_subject); // Converts a comma-separated string to an array
                        }
                        $sub = $sub->whereIn('examination_schedules.subject_id', $item->stream_subject);
                    }

                      $sub = $sub->where('examination_schedules.date','!=','1970-01-01')
                      ->orderBy('examination_schedules.date','ASC')
                       ->orderBy('examination_schedules.from_time','ASC')->get(); 
                     // dd($sub);
            @endphp  

            <tr>
                <td style='border:0px solid black;width:50%'>
                    <table class='inner_table strippedTable'>
                        <thead>
                            <tr>
                                <th class='ltr'>Subject</th>
                                <th class='ctr'>Examination Date</th>
                                <th class='ctr'>Day</th>
                                <th class='ctr'>Timing</th>
                                <th class='ctr'>Checked By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($sub) > 0)
                                @foreach($sub as $key => $item1)
                                    @if(date('d-m-Y', strtotime($item1->date ?? '')) != '01-01-1970' )
                                        <tr>
                                            <td style="border:1px solid black" class='ltr'>{{$item1->subject_name ?? ''}}  </td>
                                            <td style="border:1px solid black" class='ctr'>{{date('d-M-Y', strtotime($item1->date)) ?? ''}}</td>
                                            <td style="border:1px solid black" class='ctr'>{{ date('l', strtotime($item1->date)) ?? '' }}</td>
                                            <td style="border:1px solid black" class='ctr'>@if(!empty($item1->from_time)){{date('h:i A', strtotime($item1->from_time ?? '')) }} to {{date('h:i A', strtotime($item1->to_time ?? '')) }}@else School Time @endif</td>
                                            <td style="border:1px solid black" class='ctr'>--</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @else
                                <tr>
                                <td style="border:1px solid black;background: #fbfbfb;" colspan="5" class='ctr'>Subject Not Found</td>
                                        </tr>
                            @endif
                          
                        </tbody>
                    </table>



                </td>


            </tr>

        </tbody>
    </table>
    
    <table>

        <tfoot style='border:1px solid black'>
            <tr>
                <td class="ltr bdtr" style="text-align: center;">
                    <div class="ptr">
                        
                        {{ date('d-M-Y',strtotime($item->created_at)) }}<br>
                        Date Of Issue 
                    </div>
                </td>

                <td class="ltr bdtr" style="text-align: center;">
                    <div class="ptr">
                        
                        --------- <br>
                        Class Teacher
                    </div>
                </td>
                <td class="ltr bdtr" style="text-align: center;">
                    <div class="ptr">
                        
                        --------- <br>
                        Rechecked By
                    </div>
                </td>
                <td class="ltr bdtr" style="text-align: center;">
                    <div class="ptr">
                        @if($getSetting['sign_print'] == 'Principal')
                        @if(!empty($getSetting['principal_sign']))
                        <img src="{{ env('IMAGE_SHOW_PATH').'/setting/principal_sign/'.$getSetting['principal_sign'] }}"  width = "42px" > 
                        @else
                         -------
                        @endif
                        <br>
                         Principal 
                       @elseif($getSetting['sign_print'] == 'Director')
                           @if(!empty($getSetting['seal_sign']))
                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}"  width = "42px" > 
                            @else
                             -------
                            @endif
                            <br>
                             Director 
                       @endif
                    </div>

                </td>
            </tr>
        </tfoot>
    </table>
     @php
            $notes = Helper::getNote(1);
           
            @endphp
    <p><b>{{$notes->note ?? ''}}</b></p>
    </div>
  
    @endforeach
@endif
    
    
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function stripeRows() {
            $('.strippedTable tbody tr:even').addClass('striped');
        }
        stripeRows();
    });
</script>

</html>