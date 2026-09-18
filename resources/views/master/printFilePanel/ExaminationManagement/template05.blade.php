<!DOCTYPE html>
<html>
<head>
<title>Marksheet</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
@php
$getSetting=Helper::getSetting();
$account=Helper::getQRCode($getSetting->account_id);

@endphp
<body>
    <style>
        
         body {
       /*font-family: Arial, sans-serif;*/
      /*font-size:12px;*/
      font-family: none;
      max-width:985px;
      margin: 25px auto ;
      padding: 10px;
    /*border: 0.5px solid; */
   }
    .student_img {
    width: 80px; 
    height:100; 
    margin-top: 23%;
    margin-left:20%;
    padding-bottom: 0px;
        
    }
    
    .row{
        margin-right: 0px;
    }
    .img_background_fixed{
      position: relative;
    }
    
    .img_absolute{
        position: absolute;
        top: -52px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        right: 0;
    }
    
    .backhround_img{
        opacity: 0.1;
        width: 76%;
    }
    
    .fontweight{
            font-weight: 600;
    font-size: 14px;
    }
    .ltr{
        text-align: left; 
    }
    .rtr{
       text-align: right; 
    }
    .ctc{
       text-align: center; 
    }
    
    .AreaBorder{
        border: 1px solid black;
    }
    .tableBorder{
            border: 1px solid black;
    }
    .subpadding{
        padding: 6px;
    }
    .table_bg{
       background: silver;
    }
    .back_bg{
        background-color: #6639b5;
        color: white;
        padding: 10px;
        border-radius: 12px 12px 12px 12px;
    }
    #result td{
    border:1px solid black;
    }
    
    @page {
    margin: 0 !important;
    }
    </style>
      @if(!empty($admission_id))
    @foreach($admission_id as $admission_id)
    <div style="border: 1px solid black;margin-top: 20px;">
  
	    <table>
			<tbody >
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 116px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
      </td>
  
      <td   style=" font-size:28px;text-align:center;width:100%;color: #b9282f;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
   
     
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="font-size:14px;text-align:center;">
          <p style="border-bottom: 2px solid black;font-size: 18px;margin-bottom: 0px;"><b>A-Co-Educational English Medium School (Reg. No.: 885-920/11-12)</b></p>
          <p style="margin-bottom: 0px;font-size: 16px;"><b >Address </b> {{$getSetting['address'] ?? ''}}</p></td>

      </tr>

  </tbody>
  </table>
  
    @php
        $className = DB::table('class_types')
        ->where('id',$admission_id->class_type_id)
        ->where('session_id',Session::get('session_id'))
         ->where('branch_id',Session::get('branch_id'))
        ->whereNull('deleted_at')->first();
        
         $session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
         $gender  = DB::table('gender')->where('id',$admission_id->gender_id)->whereNull('deleted_at')->first();
    @endphp
    
        <div class="mt-2" style="padding: 7px;">
      <h6 class="text-center"><b class="">Academic Card :- Session {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}</b></h6>
      
        <div class="img_background_fixed">
            <div class="img_absolute">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
            </div>
  <table class="" style="width: 100%;">
    
			<tbody >
			<tr >
			      <td class="fontweight" style="width: 18%;">Class & Section :</td>
			    <td style="width: 13%;">{{$className->name ?? '' }}</td>
			    <td class="fontweight ctc" style="width: 18%;">Student Name :</td>
			    <td style="width: 16%;">{{$admission_id->first_name ?? '' }}</td>
			     <td class="fontweight ctc" style="width: 18%;">Father's Name :</td>
			    <td style="width: 16%;">{{ $admission_id->father_name ?? '' }}</td>
			     <td class="fontweight ctc"  style="width: 18%;">Roll No. :</td>
			    <td style="width: 16%;">1</td>
			    <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 116px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
      </td>
			    
			</tr>	
			<tr>
			      <td class="fontweight" style="width: 16%;">S.R.No. :</td>
			    <td style="width: 16%;">{{$admission_id->admissionNo ?? ''}}</td>
			    <td class="fontweight ctc" style="width: 17%;">Date Of Birth :</td>
			    <td style="width: 19%;">{{date('d-M-Y', strtotime($admission_id->dob ?? '')) ?? '' }}</td>
			    <td class="fontweight ctc" style="width: 19%;">Mother's Name :</td>
			    <td style="width: 16%;">{{$admission_id->mother_name ?? '' }}</td>
			    <td class="fontweight ctc" style="width: 16%;"></td>
			    <td style="width: 16%;"></td>
			</tr>	
	
  </tbody>
  </table>
  
 
 
 
 <table class="" style="border: 2px solid;width: 100%;">
<thead>
  <tr>
  <th class="table_bg subpadding " rowspan='3'>
      Subject
    </th>
  
    @foreach($list_exam as $item)
   <th  class="table_bg tableBorder ctc">{{$item->name ?? ''}}</th>
     @endforeach
    <th class="table_bg tableBorder ctc">Total</th>
    <!--<th class="AreaBorder ctc">Grade</th>-->
  </tr>

  <tr> 
  @foreach($list_exam as $item)
   <th  class="table_bg tableBorder ctc"></th>
     @endforeach  
  <th class="table_bg tableBorder ctc">Grand Grade</th></tr>

  <tr>
   
   
                                @php
                                $max = [];
                                $getMaximumTotal =0;
                                @endphp
                                 @foreach($list_exam as $item)
                                @php
                                $getMaximum = DB::table('fill_min_max_marks')
                                ->where('exam_id',$item->id)
                                ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))
                                ->whereNull('deleted_at')->first();
                                 $max[$item->id] = $getMaximum->exam_maximum_marks;
                                   if ($getMaximum) {
        $getMaximumTotal += $getMaximum->exam_maximum_marks;
    }
                                @endphp
                               <th class="AreaBorder ctc">{{$getMaximum->exam_maximum_marks ?? ''}}</th>
                                @endforeach
                               
                                <th class="AreaBorder ctc">{{$getMaximumTotal}}</th>
                          

   
  </tr>

  @if(!empty($list_exam))
  @php
  $total_maximum_possible =0;
  $total_obtained =0;

  @endphp
   @foreach($list_subject as $key => $item_subject)
   @php
   $maximum_marks_total_right =0;
   $obtained_total_right =0;
   @endphp
   
   
   @if(!$item_subject->other_subject == 1)

  
 <tr>
     <td class="AreaBorder subpadding">{{$item_subject->name ?? '' }}</td>
     
      @foreach($list_exam as $item_td)
      @php
                                $number = DB::table('fill_marks')
                                ->where('exam_id',$item_td->id)
                                ->where('subject_id',$item_subject->id ?? '')
                                ->where('admission_id',$admission_id->id)
                                ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))
                                ->where('deleted_at',null)
                                ->first();
                                $number_max = DB::table('fill_min_max_marks')
                                ->where('exam_id',$item_td->id)
                                ->where('subject_id',$item_subject->id ?? '')
                                ->where('class_type_id',$admission_id->class_type_id ?? '')
                                ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))
                                ->where('deleted_at',null)
                                ->first();
                            
                                $obtained_total_right += $number->student_marks ?? 0;
                              $maximum_marks = $number_max->exam_maximum_marks ?? 0;
                              $maximum_marks_total_right += $number_max->exam_maximum_marks ?? 0;
                              
                              $total_maximum_possible += $number_max->exam_maximum_marks ?? 0;
                              $total_obtained += $number->student_marks ?? 0;
                                 @endphp
                                
                                
     <td class="AreaBorder ctc ">{{$number->student_marks ?? ''}}</td>
     @endforeach
      <td class="AreaBorder ctc ">
     {{$obtained_total_right}}
    </td> 
    <!-- <td class="AreaBorder ctc ">
         @php
     //    dd($maximum_marks_total_right);
         $grade = (($obtained_total_right == 0 ? 1 : 1) / $maximum_marks_total_right)*100;
         @endphp
           @if( $grade >=91 && $grade <=100 )
                                    A+
                                @elseif( $grade >=76 && $grade <=90.99)
                                A
                                @elseif( $grade >=61 && $grade <=75.99)
                                B
                                @elseif( $grade >=41 && $grade <=60.99)
                                C
                                @elseif( $grade >=0 && $grade <=40.99)
                               D
                                @endif
     </td>-->
 </tr>
 @endif
 @endforeach
 @endif
  <tr>
    <th class="table_bg tableBorder">Total</th>

    @php

    $g_total_obtain_right = 0;
    $g_total_possible_right = 0;
    $g_total =0;
    @endphp
    @foreach($list_exam as $item)
                                   @php
                                
                                $fill_marks = DB::table('fill_marks')
                                ->where('exam_id',$item->id)
                                ->where('admission_id',$admission_id->id)
                                ->whereIn('subject_id',$subject_id)
                                ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))
                                ->where('deleted_at',null);

                                $number_max2 = DB::table('fill_min_max_marks')
                                ->where('exam_id',$item_td->id)
                                ->whereIn('subject_id',$subject_id)
                                ->where('class_type_id',$admission_id->class_type_id ?? '')
                                ->where('session_id',Session::get('session_id'))
                                ->where('branch_id',Session::get('branch_id'))
                                ->where('deleted_at',null);
                               
                                
                                $number2 = $fill_marks->sum('student_marks');
                                $max2 = $number_max2->sum('exam_maximum_marks');
                                $count_exam_subject = $fill_marks->count();
                                
                                @endphp
                                
                                
                                
                               <td class="table_bg tableBorder ctc">

                            
                                   @if(!empty($number2))  
                                  
                                   @php
                              
                                   $g_grade_bottom = ($number2/($max[$item->id]*$count_exam_subject))*100 ?? 0;
                                  
                                   $g_total +=  ($number2 ?? 0);
                                   $g_total_obtain_right = ($g_total/$total_maximum_possible)*100;
                                   @endphp
                                  
                                  {{$number2 ?? 0}}
                                 
                                @endif
                               </td>
                               
                                @endforeach
                                <td class="table_bg tableBorder ctc">
                                
                                 {{ $g_total}} 
                               
                           <!--     @if( $g_total_obtain_right >=91 && $g_total_obtain_right <=100 )
                                    A+
                                @elseif( $g_total_obtain_right >=76 && $g_total_obtain_right <=90.99)
                                A
                                @elseif( $g_total_obtain_right >=61 && $g_total_obtain_right <=75.99)
                                B
                                @elseif( $g_total_obtain_right >=41 && $g_total_obtain_right <=60.99)
                                C
                                @elseif( $g_total_obtain_right >=0 && $g_total_obtain_right <=40.99)
                               D
                                @endif-->
                              </td>
   
    

  </tr>
</thead>
</table>

 
  
   <table id='result'style="width:100%;margin-top: 15px;border:1px solid black">
  <tr class="">
    <td style="width: 2%;"><b>RESULT: </b></td>
    <td style="width: 10%;"> 
     @if( $g_total_obtain_right >=33 )
                                    Pass
                             @else
                             Fail
                                @endif
                                </td>
    <td style="width: 2%;"><b>PERCENTAGE: </b></td>
    <td style="width: 10%;">{{ number_format(($g_total_obtain_right ?? 0), 2, '.', ',')}}%
</td>
         
    </tr>
  <tr class="">
    <td style="width: 2%;"><b>DIVISION: </b></td>
    <td style="width: 10%;">
    @if( $g_total_obtain_right >=60 && $g_total_obtain_right <=100 )
                                    First Division
                                @elseif( $g_total_obtain_right >=45 && $g_total_obtain_right <=59.99)
                                Second Division
                                @elseif( $g_total_obtain_right >=33 && $g_total_obtain_right <=44.99)
                            Third Division
                                
                                @endif
    </td>
    <td style="width: 2%;"><b>PROMOTED: </b></td>
    <td style="width: 10%;"> 
    @if( $g_total_obtain_right >=33 )
                                    Promoted for next class
                                @endif
                                </td>
    
    </tr>
  <tr class="">
    <td style="width: 2%;"><b>RANK: </b></td>
    <td style="width: 10%;">0</td>
    <td style="width: 2%;"><b>ATTENDANCE: </b></td>
    <td style="width: 10%;">0</td>
    
    </tr>
    </table>
    
   <table style="width:100%;margin-top: 15px;">
  <tr class="">
    <td style="width: 20%"><b>RESULT DATE:- </b></td>
    <td >29-MARCH-2019</td>
    
    </tr>
  <tr class="">
    <td style="width: 20%;"><b>REMARK:- </b></td>
    <td ></td>
    
    </tr>
    </table>
    
<!--  <table style="width:100%;margin-top: 15px;">
 
  <tr class="">
    <td class="" style="width: 12%;"></td>
    <td class="tableBorder  ctc">
        <p style="margin-bottom: 0px;"><b>Grades are awarded on a 5-point grading scale as follows -</b></p>
    </td>
    <td class="" style="width: 12%;"></td>
  </tr>
  
  </table>-->
  
<!--  <table style="width:100%;margin-top: 0px;">-->
 
<!--  <tr class="">-->
<!--    <td class="" style="width: 12%;"></td>-->
<!--    <td class="tableBorder  ctc" style="width: 22%;"><b>Grade</b></td>-->
<!--    <td class="tableBorder  ctc ctc">A+</td>-->
<!--    <td class="tableBorder  ctc">A</td>-->
<!--    <td class="tableBorder  ctc">B</td>-->
<!--    <td class="tableBorder  ctc">C</td>-->
<!--    <td class="tableBorder  ctc">D</td>-->
<!--    <td class="" style="width: 12%;"></td>-->
<!--  </tr>-->
<!--  <tr class="">-->
<!--    <td class=""></td>-->
<!--    <td class="tableBorder ctc"><b>MARKS RANGE</b></td>-->
<!--    <td class="tableBorder ctc">91-100</td>-->
<!--    <td class="tableBorder ctc">76-90</td>-->
<!--    <td class="tableBorder ctc">61-75</td>-->
<!--    <td class="tableBorder ctc">41-60</td>-->
<!--    <td class="tableBorder ctc">0-40</td>-->
<!--  </tr>-->
<!--</table>  -->
  
  <table style="width:100%;margin-top: 65px;text-align:center">
 
  <tr class="">
<!--   <td>
       <small>Note : [T] - Trivial, [Ab] - Absent, [M] - Medical, [JL] - Join Late, [F] - Fail, Grade, Trivial, Medical and Delay subjects marks are not added in total marks.</small>
   </td>-->
   <td ><p>Class Teacher's Signature</p></td>
   <td ><p>Principal's Signature</p></td>
  </tr>
</table>  
    
  </div>
  </div>

</div>
   @endforeach
    @endif
</body>

</html>