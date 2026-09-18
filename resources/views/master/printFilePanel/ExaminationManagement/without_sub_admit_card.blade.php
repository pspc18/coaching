@php
    $getSetting=Helper::getSetting();
    $getSession=Helper::getSession();
     $admitcardNote = DB::table('admit_card_note')->whereNull('deleted_at')->first();
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
          /*margin: 20px auto;
            line-height: 16px;*/
        }

        .student_img {
            width: 80px;
            height: 100px;
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
        }

        .invoice-header {
            margin-bottom: 20px;
            text-align: 'center'
        }

        .inner_table td {
            padding: 4px;
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

        #personal_detail th, #personal_detail td {
            border: 1px solid #000;
            text-align: left;
            font-weight: 600;
        }

        .ptr {
            padding: 0px;
        }

        .bg_theme {
            background-color: #6639b5;
            color: white;
        }

        .bg_dark_theme {
            background-color: gray;
            color: white;
        }

 

        .inner_text {
            font-size: 16px;
            font-weight: 600;
        }

        .plt {
            padding-left: 10px !important;
        }

        .profile_pic {
            margin-right: 8px;
            border: 1px solid black;
            border-radius: 10px;
              width: 80px;
        height: 80px;
        }

      

        .print {
            margin-bottom: 50px;
            border: 1px solid black;
        }

        .border_none {
            border: 0px solid #000 !important;
        }
    </style>
    <style>
    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        width: 100%;
    }

    .card {
        width: 48%; /* Adjust to give space between two cards */
        margin-bottom: 40px;
        border: 1px solid black;
        box-sizing: border-box;
    }

    .print-page-break {
        page-break-after: always;
    }

    .img {
        width: 100%;
    }
    

    
    .table-detail {
        width: 100%;
    }
    td , th {
            padding-left: 8px;
    }
</style>
</head>

<body class='page'>
    @php
        $session = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
    @endphp
<div class="card-container">
    @if(!empty($data))
        @foreach($data as $key => $item)
            <div class="card @if(in_array($key, [3, 7, 11, 15, 19, 23, 27, 31, 35, 39])) print-page-break @endif">
                 <table>
                    <thead>
                       	<tr>
                          <td  rowspan="4">
                              <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 97px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
                          </td>
                          <td class="school-name" style="font-size:28px;text-align:center;width:100%;"><strong>{{$getSetting->name ?? ''}}</strong></td>
                       </tr>
                    	<tr style="text-align:center;">
                          <td   style="font-size:14px;text-align:center;"><p style="margin-bottom: 0px;"><b>Addresse :</b>{{$getSetting->address ?? ''}}</p></td>
                          </tr>
                          <tr>
                          <td  style="font-size:14px; text-align:center;"><p style="margin-bottom:6px;"><b>Phone :</b>{{$getSetting->mobile ?? ''}} </p></td>
                        </tr>
                    </thead>
                    <tbody>
                         <tr>
                        <td colspan="12">
                         <p style='text-align:center; font-weight:600; line-height:20px; font-size:15px; margin: 0px;'> <u> Permission Card {{$session->from_year ?? ''}} - {{$session->to_year ?? ''}} </u> </p>
                         </td>
                      </tr>
                    </tbody>
                </table>

                <table>
                    <tr>
                        <th style="text-align:start;font-size:15px; padding-left: 46px;">Roll No. :- {{ $item->exam_roll_no ?? '' }}</th>
                        <td style="text-align: right;font-size:15px;">Date :- {{ date('d-M-Y', strtotime(date('d-m-Y'))) }} &nbsp; &nbsp;</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td style="font-size:15px;">Student Name :- {{$item->first_name ?? ''}} {{ $item->last_name ?? '' }}</td>
                        <td rowspan="4" style="text-align:end;">
                            <img src="{{ env('IMAGE_SHOW_PATH').'/profile/'.$item->student_profile_image }}" class="profile_pic" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" alt="profile">
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;">Father's Name :- {{$item->father_name ?? ''}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;">Mother's Name :- {{$item->mother_name ?? ''}}</td>
                    </tr>
                    <tr>
                        <td style="font-size:15px;left: 62px;  position: relative;">Class :- {{$item->class_name ?? ''}}</td>
                    </tr>
                </table>

                <table>
                    <tr>
                        <td style="font-size: 14px;font-weight: bold;">
                            &nbsp; &nbsp; &nbsp; &nbsp; Student has been permitted to appear in the {{$item->exam_name ?? ''}} {{$session->from_year ?? ''}} - {{$session->to_year ?? ''}}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 14px;font-weight: bold;">
                            &nbsp; Note: {{$admitcardNote->note ?? ''}}
                        </td>
                    </tr>
                </table>

                <table>
                    <tfoot>
                        <tr>
                            <td style="text-align: start;font-size: 15px;">
                                <div style="margin-top: 22px;">
                                    &nbsp; &nbsp;&nbsp; &nbsp; --------- <br>
                                    &nbsp; &nbsp; Class Teacher
                                </div>
                            </td>
                            <td style="text-align: end;font-size: 15px;">
                                <div>
                                    @if($getSetting['sign_print'] == 'Principal')
                                        @if(!empty($getSetting['principal_sign']))
                                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/principal_sign/'.$getSetting['principal_sign'] }}" width="69px"> &nbsp; &nbsp; &nbsp;
                                        @else
                                            -------
                                        @endif
                                        <br>Principal &nbsp; &nbsp; &nbsp;
                                    @elseif($getSetting['sign_print'] == 'Director')
                                        @if(!empty($getSetting['seal_sign']))
                                            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/seal_sign/'.$getSetting['seal_sign'] }}" width="69px"> &nbsp; &nbsp; &nbsp;
                                        @else
                                            -------
                                        @endif
                                        <br>Director &nbsp; &nbsp; &nbsp;
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>

              
            </div>
        @endforeach
    @endif
</div>
</body>



</html>
