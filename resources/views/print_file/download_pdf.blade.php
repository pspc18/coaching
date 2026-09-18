@php
$getSetting=Helper::getSetting();
$getSession=Helper::getSession();
@endphp
<head>
<title>Mark Sheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<style type="text/css">
    .style12 {
        font-size: 24px;
        font-weight: bold;
    }
    
    p{
        margin-bottom:0px;
    }
    
    .style13 {
        font-size: 18px
    }
</style>
    <div class="container">
    <table width="100%" border="2" cellspacing="0" cellpadding="0" style="background-image: url('C:/Users/Dell/Downloads/back.png');background-size: contain;
            background-repeat: no-repeat;
            background-position: center;background-size: 500px 500px;">
        <tr>
            <td colspan="4">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="15% " style="text-align: center;">
                            <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/left_logo/'}}{{ $getSetting['left_logo']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 58%;margin-top: -21px; "> </td>
                        <td width="67% " style="text-align: center;">
                            <div align="center " style="margin-top: 14px;">
                                <p class="style12 ">{{$getSetting['name'] ?? ''}}</p>
                                  <p class="style13 "> <strong>Emall id-</strong> {{$getSetting['gmail'] ?? ''}}</p>
                                <p class="style13 "><strong>Address -</strong> {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</p>
                                <!--<p class="style13 "><strong>Contact No.:</strong> {{$getSetting['mobile'] ?? ''}}</p>-->
                              <br>
                            </div>
                        </td>
                        <td width="15% ">
                            <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/right_logo/'}}{{ $getSetting['right_logo']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 58%;margin-top: -21px; "> </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="21% "style="padding: 0px 12px 11px;">
                <p>Admission No.</p>
                <p>Name</p>
                <p>Father's Name</p>
                <p>Mother's Name</p>
            </td>
            <td width="29% " style="padding: 0px 10px 13px;">
                <p>{{$data[0]->admissionNo ?? '' }}</p>
                <p>{{$data[0]->name ?? '' }}</p>
               
                <p>{{$data[0]->father_name ?? '' }}</p>
              
                <p>{{$data[0]->mother_name ?? '' }}</p>
                <p>
                </p>
            </td>
            <td width="26%" style="padding: 0px 11px 16px;">
                <p> Exam Roll No.</p>
                <p>Class:</p>
                <p>Date Of Birth:</p>
                <p>Gender: </p>
            </td>
            <td width="24%" style="padding: 0px 12px 12px;">
                <p>--</p>
                <p>{{$data[0]->class_name ?? '' }} @if($data[0]->stream_id != "")[{{$data[0]->stream_id ?? ''}}]@endif</p>
                <p>{{$data[0]->dob ?? '' }}</p>
                <p>{{$data[0]->gender_id == 1 ? 'Male' : 'Female' }}</p>
            </td>
        </tr>
        <tr>
            <td colspan="4 " style="text-align: center;">
                <div align="center " class="style13 ">
                    <p>&nbsp;</p>
                    <p><strong>Progress Report Card (Session {{$data[0]->from_year ?? ''}}{{"-"}}{{$data[0]->to_year ?? ''}}) </strong></p>
                    <p>&nbsp;</p>
                </div>
            </td>
        </tr>
        <tr style="text-align: center;">
            <td style="padding: 9px;">
                <div align="center " class="style13 "><strong>Subjects</strong></div>
            </td>
            <td>
                <div align="center "><span class="style13 "><strong>Maximum Marks </strong></span></div>
            </td>
            <td>
                <div align="center "><strong>Marks</strong> <span class="style13 "><strong>Obtained</strong> </span></div>
            </td>
            <td>
                <div align="center "><strong>Subject Dictation </strong></div>
            </td>
        </tr>
       @if(!empty($data)) @php $total_obtained =0; $total_possible =0; @endphp @foreach($data as $key => $item )
                <tr style="text-align: center;">
                    <td style="padding: 5px;">{{$item->subject_name ?? '' }}</td>
                       @if(count(Helper::getMaxMarks($item->exam_id ?? '', $item->class_type_id ?? '')) >0)
                       @foreach(Helper::getMaxMarks($item->exam_id ?? '', $item->class_type_id ?? '') as $marks)
                       @if($marks->subject_id == $item->subject_id)
                        <td>{{ $marks->exam_maximum_marks ?? ''}}
                            <!--{{$total_obtained += $item->student_marks ?? '0' }}-->
                        </td>
                 
                    <!--{{$total_possible += $marks->exam_maximum_marks ?? '0' }}-->
                    <td>
                          {{$item->student_marks ?? '' }}
                    </td>
                     <td>{{($item->student_marks > 75) ? 'D' : '--' }}</td>
                    @endif @endforeach @endif
                </tr>
    
                @endforeach @endif
       
       
        <tr>
            <td>
                <div align="center ">
                    <p>&nbsp;</p>
                    <p><strong>Total Max. Marks: - {{$total_possible ?? '0' }} </strong></p>
                </div>
            </td>
            <td colspan="3 ">
                <div align="center" style="text-align: center;margin-left: 20%;">
                    <p>&nbsp;</p>
                    <p><strong>Total  Marks Obtained</strong> :- {{$total_obtained ?? '0' }} </p>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="6 ">
                <table width="100% " cellspacing="0 " cellpadding="0 ">
                    <tr>
                        <td width="49% " style=" border-right: 2px solid grey; ">
                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                <tr style="text-align: center;">
                                    <td>
                                        <div>
                                            <div align="center "><br><strong>Percentage: - {{ sprintf("%.2f", ($total_obtained / $total_possible) * 100) }}% </strong></div>
                                            <br>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="51% ">
                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                <tr>
                                    <td width="33% " style="text-align: center;">
                                        <div align="center " class="style3 "><strong>Result: - </strong>
                                        
                                        {{($total_obtained/$total_possible)*100 <= 39 ? 'Third Division' : '' }} 
                                        {{(($total_obtained/$total_possible)*100 > 39 )&& (($total_obtained/$total_possible)*100 < 60) ? 'Second Division' : '' }} 
                                        {{($total_obtained/$total_possible)*100 >59 ? 'First Division' : ''}} 
                                        
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <tr>
                    <td colspan="6 ">
                        <table width="100% " cellspacing="0 " cellpadding="0 ">
                            <tr>
                                <td style=" border-right: 2px solid grey; ">
                                    <table width="100% " cellspacing="0 " cellpadding="0 ">
                                        <tr>
                                            <td>
                                                <div>
                                                    <div align="center "></div>
                                                    <br>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <table width="100% " cellspacing="0 " cellpadding="0 ">
                                        <tr>
                                            <td width="33% ">
                                                <div align="center " class="style3 "></div><br> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                        <tr>
                            <td colspan="6 " style="display: none;">
                                <table width="100% " cellspacing="0 " cellpadding="0 ">
                                    <tr>
                                        <td width="30% " style=" border-right: 2px solid grey; ">
                                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                <tr>
                                                    <td>
                                                        <div> <br>
                                                            <div align="center "><strong>Other Subjects </strong></div>
                                                            <br>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="30% ">
                                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                <tr style="display: none;">
                                                    <td width="33% ">
                                                        <div align="center " class="style3 ">
                                                            <p><strong>AAJADI KE BAAD KA SWARNIM BHARAT - I</strong></p>
                                                            <p><strong>(200)</strong></p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="30% " style="border-left: 2px solid grey; ">
                                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                <tr>
                                                    <td width="33% ">
                                                        <div align="center " class="style3 "><strong>JEEVAN KAUSHAL (100) </strong></div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <tr>
                                    <td colspan="6 "style="display: none;">
                                        <table width="100% " cellspacing="0 " cellpadding="0 ">
                                            <tr>
                                                <td width="30% " style=" border-right: 2px solid grey; ">
                                                    <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                        <tr>
                                                            <td>
                                                                <div> <br>
                                                                    <div align="center "><strong>Marks Obtained/Grade </strong></div>
                                                                    <br>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="30% ">
                                                    <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                        <tr>
                                                            <td width="33% ">
                                                                <div align="center " class="style3 "><strong>183</strong></div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="30% " style="border-left: 2px solid grey; ">
                                                    <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                        <tr>
                                                            <td width="33% ">
                                                                <div align="center " class="style3 "><strong>87</strong></div>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                        <tr>
                                            <td colspan="4 ">
                                                <table width="100% " cellspacing="0 " cellpadding="0 " style="margin-top: -11%;">
                                                    <tr style="display: none;">
                                                        <td colspan="3 ">
                                                            <p>**Congratulationns Promoted to Class - 12</p>
                                                            <p>Result Declaration Date :- 02/05/2023 </p>
                                                            <p>&nbsp;</p>
                                                            <p>&nbsp;</p>
                                                            <p>&nbsp;</p>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td width="28%" style="text-align: center;">
                                                            <p align="center ">&nbsp;
                                                            </p>
                                                            <p align="center ">
                                                                <!--<img src="C:/Users/Dell/Downloads/27781552-removebg-preview.png " onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" style="width: 30%;margin-top: 50%; ">-->
                                                            </p>
                                                            <p align="center "><strong>Class Teacher Sign </strong></p>
                                                            <p align="center ">&nbsp;</p>
                                                        </td>
                                                        <td width="31%" style="text-align: center;">
                                                            <div align="center ">
                                                                <p>
                                                                    <!--<img src="C:/Users/Dell/Downloads/27781552-removebg-preview.png " onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/user_image.jpg' }}'" style="width: 30%;margin-top: 48%; ">-->
                                                                </p>
                                                                <p><strong>Exam Controller Sign </strong></p>
                                                            </div>
                                                        </td>
                                                        <td width="41%" style="text-align: center;">
                                                            <div align="center ">
                                                                <p>
                                                                     <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/seal.png' }}'" style="width: 30%;margin-top: 33%; ">
                                                            
                                                                </p>
                                                                <p><strong>Principal Sign </strong></p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </td>
                                </tr>
                            </td>
                        </tr> 
                    </td>
                </tr> 
            </td>
        </tr>
    </table>
    </div>