@php
    $getSetting=Helper::getSetting();
    $getSession=Helper::getSession();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>   Subject Wise Report </title>
  <style>
        .collapse {
            border-collapse: collapse;
        }

        th,td {
            border: 1px solid black;
            padding: 5px;
        }

        .portrait {
            writing-mode: vertical-rl;
            white-space: nowrap;
        }
         table tr td {
            white-space: nowrap;
        
        }
        @media print {
  @page {
    size: 5.5in 8.5in ;
    size: landscape;
  }
}

    </style>
</head> 

<body class='page'>
@php
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
                                               <td colspan="8"> Class : {{$className->name ?? ''}}   </td>
                                            
                                        </tr>
                                            <tr>
                                                 <th class="portrait" rowspan="3">Sr.No</th>
                                                <th class="portrait" rowspan="3">Admission No.</th>
                                                <th class="portrait" rowspan="3">Roll Number</th>
                                                <th class="Student" rowspan="3">Student</th>
                                            </tr>
                                             <tr>
                                                 @if(!empty($examlist))
                                               @foreach($examlist as $key => $exam)
                                                <th colspan="{{count($list_subject)+1}}" >{{$exam->exam_name ?? '' }}</th>
                                               
                                                
                                                 @endforeach
                                               @endif
                                              <th colspan="5" >Total</th>

                                            </tr>
                                            <tr>
                                                @if(!empty($examlist))
                                                 
                                               @foreach($examlist as $key => $exam)
                                               
                                                   
                                                         @if(!empty($list_subject))
                                                               @php
                                                   $total = 0;
                                                    @endphp
                                                                    @foreach($list_subject as $key => $item_subject)
                                                                    @php
                                                                     $number_max = DB::table('fill_min_max_marks')
                                                                            ->where('exam_id',$exam['exam_id'])
                                                                                        ->where('subject_id',$item_subject->id ?? '')
                                                                            ->where('class_type_id',$search['class_type_id'] ?? '')
                                                                            ->where('session_id',Session::get('session_id'))
                                                                            ->where('branch_id',Session::get('branch_id'))
                                                                            ->where('deleted_at',null)
                                                                            ->first();
                                                                          
                                                                       if ($number_max !== null) {
                                                                    if($item_subject->other_subject == 0){ 
                                                                    $total += $number_max->exam_maximum_marks;
                                                                    }else{
                                                                     $total += 0;
                                                                    } 
                                                                    }
                                                                    @endphp
                                                 <th class="portrait">{{$item_subject->name ?? '' }}  {{$number_max->exam_maximum_marks ?? '-' }}</th>

                                                 @endforeach
                                             @endif
                                                <th class="portrait">Total {{$total ?? ''}}</th>
                                                
                                                 @endforeach
                                               @endif
                                                <th class="portrait">Total </th>
                                                <th class="portrait">Obtained </th>
                                                <th class="portrait">Percentage </th>
                                                <th class="portrait">Rank </th>
                                                <th class="portrait">Division </th>
                                                
                                            </tr>
                                                                       
                                                                    </thead>
                                                                    <tbody>
                                                 @if(!empty($students))
                                        @php
                                        $i = 1;
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
                                                                <td>{{$i++}}</td>
                                                                <td>{{$student->admissionNo ?? ''}}</td>
                                                                <td>{{$exam_result_updates->roll_no ?? '-'}}</td>
                                                                <td>{{$student->first_name ?? ''}}</td>
                                                                
                                                                @if(!empty($examlist))
                                                     @php
                                                       $all_exam_maximum_marks = 0;
                                                       $all_total_obtained = 0;
                                                        @endphp
                                               @foreach($examlist as $key => $exam)
                                               
                                                     @php
                                                            $list_subject = \App\Models\Subject::where("class_type_id", $search['class_type_id'])
                                                                                ->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC")
                                                                                ->get();                                                    
                                                        @endphp
                                                         @if(!empty($list_subject))
                                                               @php
                                                   $total_obtained = 0;
                                                    @endphp
                                                                    @foreach($list_subject as $key => $item_subject)
                                                                    @php
                                                                     $number = DB::table('fill_marks')
                                                                                ->where('exam_id', $exam['exam_id'])
                                                                                ->where('subject_id', $item_subject->id ?? '')
                                                                                ->where('admission_id', $student->id)
                                                                                ->where('session_id', Session::get('session_id'))
                                                                                ->where('branch_id', Session::get('branch_id'))
                                                                                ->where('deleted_at', null)
                                                                                ->first();
                                                                                // Fetch max marks for this subject
                                                                                
                                                                       if ($number !== null) {
                                                                    if($item_subject->other_subject == 0){ 
                                                                            if ($number->student_marks == 'AB' || $number->student_marks == 'F') {
                                                                                $total_obtained += 0; 
                                                                                $all_total_obtained += 0; 
                                                                                $all_exam_maximum_marks += $number->exam_maximum_marks;
                                                                            } elseif (is_numeric($number->student_marks)) {
                                                                                $total_obtained += floatval($number->student_marks); 
                                                                                $all_total_obtained += floatval($number->student_marks); 
                                                                                  $all_exam_maximum_marks += $number->exam_maximum_marks;
                                                                            }
                                                                            
                                                                    }else{
                                                                     $all_exam_maximum_marks += 0;
                                                                     $total_obtained += 0;
                                                                     $all_total_obtained += 0;
                                                                     
                                                                    } 
                                                                    }
                                                                    @endphp
                                                 <td>{{$number->student_marks ?? '-' }} </td>

                                                 @endforeach
                                             @endif
                                                <td>{{$total_obtained ?? ''}}</td>
                                                
                                                 @endforeach
                                               @endif
                                                @php
                                                $percentage = ($all_exam_maximum_marks > 0) ? ($all_total_obtained / $all_exam_maximum_marks) * 100 : 0;
                                            @endphp
                                               <td>{{$all_exam_maximum_marks ?? '0'}}</td>
                                               <td>{{$all_total_obtained ?? '0'}}</td>
                                               <td>{{ round($percentage, 2) }}</td>
                                              <td>{{$exam_result_updates->rank ?? '-'}}</td>
                                               <td>
                                                   @if( $percentage >=60 && $percentage <=100 ) First 
                                                   @elseif( $percentage>=48 && $percentage <=59.99) Second 
                                                   @elseif( $percentage>=36 && $percentage <=47.99) Third
                                                   @elseif( $percentage>=0 && $percentage <=35.99) Fail 
                                                   @else - @endif 
                                               </td>
                                               
                                                            </tr>
                                            
                                         @endforeach
                                     @endif
                                        </tbody>
                                </table>
                             
    
</body>
<style>
    @page
{
size: landscape;
}
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        function stripeRows() {
            $('.strippedTable tbody tr:even').addClass('striped');
        }
        stripeRows();
    });
</script>
<script type="text/javascript">
//window.print();
</script>
</html>