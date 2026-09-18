@php
$getSetting=Helper::getSetting();
$getSession=Helper::getSession();
 
@endphp
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

<body>
    
    @if(!empty($admissionNo))
      @php
            $total_obtained =0;
            $total_possible =0;
            
            @endphp
            
               @foreach($admissionNo as $admissionId)
    @foreach($data as $item)
    
    @if($admissionId->admission_id == $item->admission_id)
    
   <table width="100%" border="2" cellspacing="0" cellpadding="0" style="background-image: url('C:/Users/Dell/Downloads/back.png');background-size: contain;
            background-repeat: no-repeat;
            background-position: center;background-size: 500px 500px;">
    <tr>
        <td colspan="4">
            <table width="100%" cellspacing="0" cellpadding="0">
                <tr>
                    <td width=" 15% " style="text-align: center;">
                        <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/left_logo/'}}{{ $getSetting['left_logo']}}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 58%;margin-top: -21px; "> </td>
                    <td width="67% " style="text-align: center;">
                        <div align="center ">
                            <p class="style12 ">{{$getSetting['name'] ?? ''}}</p>
                              <p class="style13 "> <strong>Emall id-</strong> {{$getSetting['gmail'] ?? ''}}</p>
                            <p class="style13 "> <strong>Address-</strong> {{$getSetting['address'] ?? ''}},{{$getSetting['pincode'] ?? ''}}</p>
                            <!--<p class="style13 "><strong>Contact No.:</strong> {{$getSetting['mobile'] ?? ''}}</p>-->
                          
                        </div>
                    </td>
                    <td width="15% " style="text-align: center;">
                        <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/right_logo/'}}{{ $getSetting['right_logo']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" style="width: 58%;margin-top: -21px; "> </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td width="21% "style="padding: 0px 12px 40px;">
            <p>Admission No.</p>
            <p>Name</p>
            <p>Father's Name</p>
            <p>Mother's Name</p>
        </td>
        <td width="29% ">
            <p>{{$item->admissionNo ?? '' }}</p>
            <p>{{$item->name ?? '' }}</p>
           
            <p>{{$item->father_name ?? '' }}</p>
          
            <p>{{$item->mother_name ?? '' }}</p>
            <p><br>
            </p>
        </td>
        <td width="26%" style="padding: 0px 12px 34px;">
            <p> Exam Roll No.</p>
            <p>Class:</p>
            <p>Date Of Birth:</p>
            <p>Gender: </p>
        </td>
        <td width="24%" style="padding: 0px 12px 34px;border-right: 2px solid grey;">
            <p>2627</p>
            <p>{{$item->class_name ?? '' }} @if($item->stream_id != "")[{{$item->stream_id ?? ''}}]@endif</p>
            <p>{{$item->dob ?? '' }}</p>
            <p>{{$item->gender_id == 1 ? 'Male' : 'Female' }}</p>
        </td>
    </tr>
    <tr>
        <td colspan="4 " style="text-align: center;">
            <div align="center " class="style13 ">
                <p>&nbsp;</p>
                <p><strong>Progress Report Card (Session {{$item->from_year ?? ''}}{{"-"}}{{$item->to_year ?? ''}}) </strong></p>
                <p>&nbsp;</p>
            </div>
        </td>
    </tr>
    
    <tr style="text-align: center;">
        <td>
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
   @if(!empty($data)) @php $total_obtained =0; $total_possible =0; @endphp
   @foreach($data as $key => $item1 )
    @if($admissionId->admission_id == $item1->admission_id)
            <tr style="text-align: center;">
                <td>{{$item1->subject_name ?? '' }}</td>
                @if(count(Helper::getMaxMarks($item1->exam_id ?? '', $item1->class_type_id ?? '')) >0)
                @foreach(Helper::getMaxMarks($item1->exam_id ?? '', $item1->class_type_id ?? '') as $marks) 
                @if($marks->subject_id == $item1->subject_id)
                <!--{{$total_possible += $marks->exam_maximum_marks ?? '0' }}-->
                <td>

                    {{ $marks->exam_maximum_marks ?? ''}}

                </td>
                <td>{{$item1->student_marks ?? '' }}
                    <!--{{$total_obtained += $item->student_marks ?? '0' }}-->
                </td>
                 <td>{{($item->student_marks > 75) ? 'D' : '--' }}</td>
                @endif @endforeach 
                @endif
            </tr>
            @endif
            @endforeach
            @endif
   
   
    <tr>
        <td>
            <div align="center ">
                <p>&nbsp;</p>
                <p><strong>Total Max. Marks: - {{$total_possible ?? '0' }} </strong></p>
            </div>
        </td>
        <td colspan="3 ">
            <div align="center" style="text-align: center;">
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
                                        <div align="center "><strong>Percentage: - {{($total_obtained/$total_possible)*100 ?? '0' }}% </strong></div>
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
            <!--<tr>-->
            <!--    <td colspan="6 ">-->
            <!--        <table width="100% " cellspacing="0 " cellpadding="0 ">-->
            <!--            <tr>-->
            <!--                <td style=" border-right: 2px solid grey; ">-->
            <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
            <!--                        <tr>-->
            <!--                            <td>-->
            <!--                                <div>-->
            <!--                                    <div align="center "></div>-->
            <!--                                    <br>-->
            <!--                                </div>-->
            <!--                            </td>-->
            <!--                        </tr>-->
            <!--                    </table>-->
            <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
            <!--                        <tr>-->
            <!--                            <td width="33% ">-->
            <!--                                <div align="center " class="style3 "></div><br> </td>-->
            <!--                        </tr>-->
            <!--                    </table>-->
            <!--                </td>-->
            <!--            </tr>-->
            <!--        </table>-->
                    <!--<tr>-->
                    <!--    <td colspan="6 ">-->
                    <!--        <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                    <!--            <tr>-->
                    <!--                <td width="30% " style=" border-right: 2px solid grey; ">-->
                    <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                    <!--                        <tr>-->
                    <!--                            <td>-->
                    <!--                                <div> <br>-->
                    <!--                                    <div align="center "><strong>Other Subjects </strong></div>-->
                    <!--                                    <br>-->
                    <!--                                </div>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--                    </table>-->
                    <!--                </td>-->
                    <!--                <td width="30% ">-->
                    <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                    <!--                        <tr style="display: none;">-->
                    <!--                            <td width="33% ">-->
                    <!--                                <div align="center " class="style3 ">-->
                    <!--                                    <p><strong>AAJADI KE BAAD KA SWARNIM BHARAT - I</strong></p>-->
                    <!--                                    <p><strong>(200)</strong></p>-->
                    <!--                                </div>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--                    </table>-->
                    <!--                </td>-->
                    <!--                <td width="30% " style="border-left: 2px solid grey; ">-->
                    <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                    <!--                        <tr>-->
                    <!--                            <td width="33% ">-->
                    <!--                                <div align="center " class="style3 "><strong>JEEVAN KAUSHAL (100) </strong></div>-->
                    <!--                            </td>-->
                    <!--                        </tr>-->
                    <!--                    </table>-->
                    <!--                </td>-->
                    <!--            </tr>-->
                    <!--        </table>-->
                            <!--<tr>-->
                            <!--    <td colspan="6" style="display: none;">-->
                            <!--        <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                            <!--            <tr>-->
                            <!--                <td width="30% " style=" border-right: 2px solid grey; ">-->
                            <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                            <!--                        <tr>-->
                            <!--                            <td>-->
                            <!--                                <div> <br>-->
                            <!--                                    <div align="center "><strong>Marks Obtained/Grade </strong></div>-->
                            <!--                                    <br>-->
                            <!--                                </div>-->
                            <!--                            </td>-->
                            <!--                        </tr>-->
                            <!--                    </table>-->
                            <!--                </td>-->
                            <!--                <td width="30% ">-->
                            <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                            <!--                        <tr>-->
                            <!--                            <td width="33% ">-->
                            <!--                                <div align="center " class="style3 "><strong>183</strong></div>-->
                            <!--                            </td>-->
                            <!--                        </tr>-->
                            <!--                    </table>-->
                            <!--                </td>-->
                            <!--                <td width="30% " style="border-left: 2px solid grey; ">-->
                            <!--                    <table width="100% " cellspacing="0 " cellpadding="0 ">-->
                            <!--                        <tr>-->
                            <!--                            <td width="33% ">-->
                            <!--                                <div align="center " class="style3 "><strong>87</strong></div>-->
                            <!--                            </td>-->
                            <!--                        </tr>-->
                            <!--                    </table>-->
                            <!--                </td>-->
                            <!--            </tr>-->
                            <!--        </table>-->
                                    <tr>
                                        <td colspan="4 ">
                                            <table width="100% " cellspacing="0 " cellpadding="0 ">
                                                <!--<tr>-->
                                                <!--    <td colspan="3 ">-->
                                                <!--        <p>**Congratulationns Promoted to Class - 12</p>-->
                                                <!--        <p>Result Declaration Date :- 02/05/2023 </p>-->
                                                <!--        <p>&nbsp;</p>-->
                                                <!--        <p>&nbsp;</p>-->
                                                <!--        <p>&nbsp;</p>-->
                                                <!--    </td>-->
                                                <!--</tr>-->
                                                <tr>
                                                    <td width="28%" style="text-align: center;">
                                                        <p align="center ">&nbsp;
                                                        </p>
                                                        <p align="center "><img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'default/seal.png' }}'" style="width: 30%;margin-top: -21px; ">
                                                        </p>
                                                        <p align="center "><strong>Class Teacher Sign </strong></p>
                                                        <p align="center ">&nbsp;</p>
                                                    </td>
                                                    <td width="31%" style="text-align: center;">
                                                        <div align="center ">
                                                            <p>
                                                                <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="width: 30%;margin-top: -21px; ">
                                                            </p>
                                                            <p><strong>Exam Controller Sign </strong></p>
                                                        </div>
                                                    </td>
                                                    <td width="41%" style="text-align: center;">
                                                        <div align="center ">
                                                            <p>
                                                                 <img src="{{env('IMAGE_SHOW_PATH')}}{{'setting/seal_sign/'}}{{ $getSetting['seal_sign']}}"  onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/seal.png' }}'" style="width: 30%;margin-top: -21px; ">
                                                        
                                                            </p>
                                                            <p><strong>Principal Sign </strong></p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
</table>
    </tr>
    </table>

    @break;
    @endif

    @endforeach
    @endforeach
    @endif
</body>

</html>