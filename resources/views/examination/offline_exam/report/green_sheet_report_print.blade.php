<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Green Sheet Report</title>
    <style>
        /* Result */
        .collapse {
            border-collapse: collapse;
        }

        /* Class Teacher Name's */
        .collapse1 {
            border-collapse: collapse;
        }

        /* No of students */
        .collapse2 {
            border-collapse: collapse;
        }

        /* special exam */
        .collapse3 {
            border-collapse: collapse;
        }

        /* Class Wise Result   */
        .collapse4 {
            border-collapse: collapse;
        }

        /* Signature  */
        .collapse5 {
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 3px;
        }

        .portrait {
            writing-mode: vertical-rl;
            white-space: nowrap;
        }
       table tr td {
            white-space: nowrap;
        
        }
        .table-container {
            display: inline-flex;
            justify-content: space-between;
        }
    </style>
</head>

<body style="margin: 0px 0px 0px 15px;">
    @php
    $getSetting=Helper::getSetting();

    $session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
    $list_subject = \App\Models\Subject::where("class_type_id", $search['class_type_id'])
                        ->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC")
                        ->get();                                                    
@endphp
             
     @php
            $className = DB::table('class_types')->where('id',$search['class_type_id'])->where('branch_id',Session::get('branch_id'))->whereNull('deleted_at')->first();
             $session=DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();

    @endphp
    <table class="table" style="text-align: center;width: 100%;"> 
        <tr>
              <td style="font-size: 34px; border: none; text-align: center;" colspan="12"> {{$getSetting->name ?? ''}}</td>
           
        </tr>
    </table>
   
    <table class="collapse">
        <thead>
             <tr>
              <td colspan="4"> SESSION : {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}</td>
              <td colspan="10" style="width: 103%;border: none;"> </td>
               <td colspan="8"> Class : {{$className->name ?? ''}}   </td>
        </tr>
            <tr>
                <th class="portrait" rowspan="6">Sr.No</th>
                <th class="portrait" rowspan="6">S.R. No</th>
                <th class="portrait" rowspan="6">Date Of Birth</th>
                <th class="portrait" rowspan="6" >Roll No</th>
                <th class="Student" rowspan="6">Student</th>
            </tr>
            <tr>
                @php
                
                @endphp
                    @if(!empty($list_subject))
                       @foreach($list_subject as $key => $sub)
                        <th colspan="8" >{{$sub->name ?? '' }}</th>
                       
                        
                         @endforeach
                    @endif
                
                <th colspan="7">Grand Total</th>
            </tr>

            <tr>
                @if(!empty($list_subject))
                       @foreach($list_subject as $key => $sub)
                           @if(!empty($examlist))
                               @foreach($examlist as $key => $exam)
                                               <th class="portrait" colspan="1">{{$exam->exam_name ?? '' }}</th>
                                 @endforeach
                            @endif  
                             <th class="portrait">Total</th>
                            <th class="portrait">Percentage</th>
                            <th class="portrait">Grade.</th>
                         @endforeach
                    @endif
                               
           
              
               
                <th class="portrait" >Total</th>
                <th class="portrait" >Obtained</th>
                <th class="portrait" >Percentage</th>
                <th class="portrait" >Grade </th>
                <th class="portrait" >Rank</th>
                <th class="portrait">Divison</th>
                <th class="portrait">Total Attend</th>
            </tr>
            <tr>
                @if(!empty($list_subject))
                       @foreach($list_subject as $key => $sub)
                           @if(!empty($examlist))
                               @foreach($examlist as $key => $exam)
                                               <th class="portrait">Marks</th>
<!--                                                <th class="portrait">Grade</th>
-->                                                
                                 @endforeach
                            @endif  
                             <th class="portrait"></th>
                            <th class="portrait"></th>
                            <th class="portrait"></th>
                         @endforeach
                    @endif
                               
           
              
               
                <th class="portrait" ></th>
                <th class="portrait" ></th>
                <th class="portrait" ></th>
                <th class="portrait" > </th>
                <th class="portrait" ></th>
                <th class="portrait"></th>
                <th class="portrait"></th>
            </tr>
            <tr>
                @if(!empty($list_subject))
                        @php
                           
                        @endphp
                       @foreach($list_subject as $key => $sub)
                           @if(!empty($examlist))
                           @php
                          $total2 =0;
                           @endphp
                               @foreach($examlist as $key => $exam)
                               @php
                                $number_max = DB::table('fill_min_max_marks')
                                            ->where('exam_id',$exam['exam_id'])
                                                        ->where('subject_id',$sub->id ?? '')
                                            ->where('class_type_id',$search['class_type_id'] ?? '')
                                            ->where('session_id',Session::get('session_id'))
                                            ->where('branch_id',Session::get('branch_id'))
                                            ->where('deleted_at',null)
                                            ->first();
                                          
                                                 if ($number_max !== null) {
                                                $total1 = $number_max->exam_maximum_marks;
                                                $total2 += $number_max->exam_maximum_marks;
                                                }
                                           @endphp
                                               <th class="padding">{{$total1 ?? ''}}</th>
<!--                                                <th class="padding"></th>
-->                                                
                                 @endforeach
                            @endif  
                            @php
                            @endphp
                             <th class="padding">{{$total2 ?? ''}}</th>
                            <th class="padding"></th>
                            <th class="padding"></th>
                         @endforeach
                    @endif
                               
           
              
               
                <th class="padding" ></th>
                <th class="padding" ></th>
                <th class="padding" ></th>
                <th class="padding" > </th>
                <th class="padding" ></th>
                <th class="padding"></th>
                <th class="padding"></th>
            </tr>
  
        </thead>
        <tbody>
               @if(!empty($students))
                @php
                $i1 = 1;
                $first = 0;
                $second = 0;
                $fail = 0;
                $third = 0;
               
                @endphp
                @foreach($students as $student)    
                @php
                 $ids = $examlist->pluck('exam_id')->toArray();  
                    $lastExamlId = end($ids);
                    $exam_result_updates = DB::table('exam_result_updates')
                                            ->where('class_type_id',$student->class_type_id)
                                            ->where('admission_id',$student->id)
                                            ->where('exam_id',$lastExamlId)
                                            ->where('branch_id',Session::get('branch_id'))
                                            ->whereNull('deleted_at')->first();
                                       
                @endphp
                
                
                
                
                <tr>
                <td>{{$i1++}}</td>
                <td style="text-align: center;">{{$student->admissionNo ?? ''}}</td>
                <td>{{date("d-M-Y", strtotime($student->dob)) ?? ''}}</td>
                <td>{{$exam_result_updates->roll_no ?? '-'}}</td>
                <td>{{$student->first_name ?? ''}}</td>
                @php
                   $all_exam_maximum_marks = 0;
                   $all_total_obtained = 0;
                @endphp
                 @if(!empty($list_subject))
                       @foreach($list_subject as $key => $sub)
                           @if(!empty($examlist))
                            @php
                                $obtained = 0;
                                $maximum_marks = 0;
                            @endphp
                               @foreach($examlist as $key => $exam)
                                        
                                    @php
                                         $number = DB::table('fill_marks')
                                                    ->where('exam_id', $exam['exam_id'])
                                                    ->where('subject_id', $sub->id ?? '')
                                                    ->where('admission_id', $student->id)
                                                    ->where('session_id', Session::get('session_id'))
                                                    ->where('branch_id', Session::get('branch_id'))
                                                    ->where('deleted_at', null)
                                                    ->first();
                                       
                                           if ($number !== null) {
                                                if ($number->student_marks == 'AB' || $number->student_marks == 'F') {
                                                    $obtained += 0; 
                                                    $maximum_marks += $number->exam_maximum_marks;
                                                    if($sub->other_subject == 0){ 
                                                     $all_total_obtained += 0; 
                                                     $all_exam_maximum_marks += $number->exam_maximum_marks;
                                                    }
                                                } elseif (is_numeric($number->student_marks)) {
                                                    $obtained += floatval($number->student_marks); 
                                                    $maximum_marks += $number->exam_maximum_marks;
                                                    if($sub->other_subject == 0){ 
                                                     $all_total_obtained += floatval($number->student_marks); 
                                                    $all_exam_maximum_marks += $number->exam_maximum_marks;
                                                    }
                                                }else{
                                                    if($sub->other_subject == 0){ 
                                                     $all_total_obtained += 0; 
                                                    $all_exam_maximum_marks += $number->exam_maximum_marks;
                                                    }
                                                    $maximum_marks += $number->exam_maximum_marks;
                                                }
                                                
                                        
                                        }
                                         
                                        @endphp    
                                        
                                       
                                    <td class="padding">{{$number->student_marks ?? '-' }}</td>
                                     @php
                                       $percentage = ($maximum_marks > 0) ? ($obtained / $maximum_marks) * 100 : 0;

                                    $grade = \App\Models\Master\GradeSystem::where("branch_id", Session::get("branch_id"))->where('min_per', '<=', $percentage)->where('max_per', '>=', $percentage)->first();
                                    @endphp
<!--                                    <td class="padding"> {{$grade->grade_name ?? '-'}}</td>
-->                                 @endforeach
                            @endif  
                             @php
                               $percentage2 = ($maximum_marks > 0) ? ($obtained / $maximum_marks) * 100 : 0;

                            $grade1 = \App\Models\Master\GradeSystem::where("branch_id", Session::get("branch_id"))->where('min_per', '<=', $percentage2)->where('max_per', '>=', $percentage2)->first();
                           // dd($grade1);
                            @endphp
                            <td class="padding">{{$obtained ?? ''}}</td>
                            <td class="padding">{{ round($percentage2, 2) }}%</td>
                            <td class="padding">{{$grade1->grade_name ?? '-'}}</td>
                         @endforeach
                    @endif
                            @php
                   
                    //dd($lastId); 
                                $percentage3 = ($all_exam_maximum_marks > 0) ? ($all_total_obtained / $all_exam_maximum_marks) * 100 : 0;
                                
                            $grade11 = \App\Models\Master\GradeSystem::where("branch_id", Session::get("branch_id"))->where('min_per', '<=', $percentage3)->where('max_per', '>=', $percentage3)->first();
                            @endphp
                        <td class="padding" >{{$all_exam_maximum_marks ?? ''}}</td>
                        <td class="padding" >{{$all_total_obtained ?? ''}}</td>
                        <td class="padding" >{{ round($percentage3, 2) }}%</td>
                        <td class="padding" > {{$grade11->grade_name ?? '-'}}</td>
                        <td class="padding" >{{$exam_result_updates->rank ?? ''}}</td>
                        <td class="padding"> @if( $percentage3 >=60 && $percentage3 <=100 ) First 
                                                @php
                                                $first += 1;
                                                @endphp
                                                   @elseif( $percentage3>=48 && $percentage3 <=59.99) Second 
                                                @php
                                                $second += 1;
                                                @endphp
                                                   @elseif( $percentage3>=36 && $percentage3 <=47.99) Third
                                                 @php
                                                $third += 1;
                                                @endphp   
                                                   @elseif( $percentage3>=0 && $percentage3 <=35.99) Fail 
                                                @php
                                                $fail += 1;
                                                @endphp
                                                
                                                   @else - @endif 
                                                  
                                                   </td>
                        <td class="padding"> {{$exam_result_updates->attendence ?? ''}}</td>
                
            </tr>
                    @endforeach
                @endif
            
            
        </tbody>
    </table>
    <br>
<div class="table-container"> 
    <table class="collapse4" style="width: 28%;margin-right: 68px;">
        <thead>
            <tr>
                <th colspan="6">Result Summary</th>


            </tr>
        </thead>
        <tbody>
          
            <tr>
                <td>First Divison</td>
                <td>Second Divison</td>
                <td>Third Divison</td>
                <td>Supplementary</td>
                <td>Fail </td>
                 <td>Total</td>
            </tr>
            <tr>
                <td style="text-align: center;">{{$first ?? 0}}</td>
                <td style="text-align: center;">{{$second ?? 0}}</td>
                <td style="text-align: center;">{{$third ?? 0}}</td>
                <td></td>
                <td style="text-align: center;">{{$fail ?? ''}}</td>
                 <td style="text-align: center;">{{$first+$second+$third+$fail ?? ''}}</td>
            </tr>
        </tbody>
    </table>
    
    <table class="collapse5" style="
    width: 28%;">
        <thead>
            <tr>
                <th colspan="6">Signature</th>


            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Class Teacher</td>
                <td>Checked by</td>
                <td>Exam Incharge</td>
                <td style="padding: 0px 35px 0px 35px;text-align: center;">Principal</td>
                <td>Total Working Days</td>
                <td>Result  Date</td>
            </tr>
            <tr>
                <td style="padding: 26px;"></td>
                <td></td>
                <td></td>
                <td></td>
            
                <td></td>
                <td></td>
            </tr>
          
        </tbody>
    </table>
    </div>
</body>

</html>