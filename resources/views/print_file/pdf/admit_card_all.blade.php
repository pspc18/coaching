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
            font-size: 12px;
            max-width: 750px;
            margin: 0 auto;
            /* border: 0.5px solid; */
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
            padding: 5px;
            /* background-color: #f2f2f2; */
        }

        .invoice-header {
            margin-bottom: 20px;
            text-align: 'center'
        }

        .inner_table td {
            padding: 5px
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
            padding: 5px 0px;
            font-weight:600;
        }

        #personal_detail td {
            border: 1px solid #000;
            text-align: left;
            padding: 5px 0px;
            font-weight:600;
        }

        .ptr {
            padding: 10px;
        }

        .bdtr {
            border: 1px solid black;
        }

        .bg_theme {
            background-color: gainsboro;
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
        }
        
        .print-page-break{
            margin-top:30px;
        }
        
        @media print {
            .print-page-break {
                page-break-after: always;
                margin-top:0px;
            }
        }
    </style>
</head>

<body class='page'>
@php
    $session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
@endphp
 @if(!empty($data))
    @foreach($data as $key => $item)
    <div class="print-page-break">
    <table style="border: 1px solid black;">
        <tbody class="bg_theme">
            <tr>
                <td rowspan='2' width='30%' class="rtr">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" width = "150px" >
                </td>
                <td width='50%' style="font-size:20px;text-align:center;text-transform:capitalize"><span><strong>{{$getSetting['name'] ?? ''}}</strong></span>
                </td>

                <td width="20%"></td>

            </tr>
            <tr style="text-align:center;">
                <td width='50%'>
                    <div class="ctr lheight">
                        <p><b>Address </b> {{$getSetting['address'] ?? ''}} {{','.$getSetting['pincode'] ?? ''}}</p>
                        <p><b>Phone:-</b> {{$getSetting['mobile'] ?? ''}}<br>
                        <p><b>Email:-</b> {{$getSetting['gmail'] ?? ''}}<br>
                        <p><b>Website:-</b> -
                    </div>
                </td>

                <td width="20%"></td>
            </tr>
        </tbody>

        <tfoot>
            <tr style="border: 1px solid black;">
                <td colspan="12">
                    <p
                        style='text-align:center; font-weight:600;line-height:20px;margin-top:0px;font-size:12px; margin: 10px;'>
                        Examination Admit Card <br>
                        (Academic Session {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}) - Final Examination
                    </p>
                </td>
            </tr>
        </tfoot>
    </table>

    <table id='personal_detail'>
        <tbody>
            <tr>
                <th class="bg_theme plt">Exam Roll No. :- {{ $item->exam_roll_no ?? '' }}</th>
                <td class="bg_theme plt">Class :- {{$item->class_name ??''}} </td>
                <th rowspan="2" style="text-align:center;" class="bg_theme plt">
                    <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$item->student_profile_image }}" class="profile_pic" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg'}}'" alt="profile">
                </th>
            </tr>
            <tr>
                <th class="plt">Student Name :- {{$item->first_name ?? ''}} {{ $item->last_name ?? ''}}</th>
                <td class="plt">Father's Name :- {{$item->father_name ?? ''}}</td>
            </tr>
        </tbody>
    </table>

  
    <table style='margin-top:20px'>
        <tbody>
            @php                               
                $sub = DB::table('examination_schedules')->select('examination_schedules.*','subject.name as subject_name')
                      ->leftjoin('subject as subject','subject.id','examination_schedules.subject_id')
                      ->where('examination_schedules.class_type_id',$item->class_type_id)
                      ->where('examination_schedules.examination_schedule_id',$item->examination_schedule_id)
                      ->where("examination_schedules.session_id", Session::get("session_id"))
                      ->where("examination_schedules.branch_id", Session::get("branch_id"))
                      ->where('subject.deleted_at',null)
                      ->where('examination_schedules.date','!=','1970-01-01')
                      ->orderBy('examination_schedules.date','ASC')->get();    
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
                            @if(!empty($sub))
                                @foreach($sub as $key => $item1)
                                    @if(date('d-m-Y', strtotime($item1->date ?? '')) != '01-01-1970' )
                                        <tr>
                                            <td style="border:1px solid black" class='ltr'>{{$item1->subject_name ?? ''}}</td>
                                            <td style="border:1px solid black" class='ctr'>{{date('d-M-Y', strtotime($item1->date)) ?? ''}}</td>
                                            <td style="border:1px solid black" class='ctr'>{{ date('l', strtotime($item1->date)) ?? '' }}</td>
                                            <td style="border:1px solid black" class='ctr'>{{date('h:i A', strtotime($item1->from_time ?? '')) }} to {{date('h:i A', strtotime($item1->to_time ?? '')) }}</td>
                                            <td style="border:1px solid black" class='ctr'>--</td>
                                        </tr>
                                    @endif
                                @endforeach
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
                <td class="ltr bdtr">
                    <div class="ptr">
                        Date Of Issue <br><br>
                        {{ date('d-M-Y',strtotime($item->created_at)) }}
                    </div>
                </td>

                <td class="ltr bdtr">
                    <div class="ptr">
                        Class Teacher<br>
                        ---------
                    </div>
                </td>
                <td class="ltr bdtr">
                    <div class="ptr">
                        Rechecked By <br>
                        ---------
                    </div>
                </td>
                <td class="ltr bdtr">
                    <div class="ptr">
                        Director <br>
                        -------
                    </div>

                </td>
            </tr>
        </tfoot>
    </table>
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