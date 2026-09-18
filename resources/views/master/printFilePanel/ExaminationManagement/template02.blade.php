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
      max-width:740px;
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
        opacity: 0.3;
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
        border: 2px solid black;
    }
    .subpadding{
        padding: 6px;
    }
    </style>
    @if(!empty($admission_id))
    @foreach($admission_id as $admission_id)
	    <table style="border: 2px solid;">
			<tbody >
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 97px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
      </td>
  
      <td   style=" font-size:28px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}}</strong></span></td>
   
      <td rowspan="4"> 
      <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }} " style="width: 97px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'">
      </td>
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="font-size:14px;text-align:center;"><p style="margin-bottom: 0px;"><b >Address </b> {{$getSetting['address'] ?? ''}}</p></td>

      </tr>
      <tr>
     
   
      <td  style="font-size:14px; text-align:center;"><p style="margin-bottom:6px;"><b >Phone:-</b> {{$getSetting['mobile'] ?? ''}} &nbsp;<b>Email :</b> {{$getSetting['gmail'] ?? ''}}  </p></td>
    </tr>
   	<tr style="text-align:center;">
       

   

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
    
        <div class="mt-2" style="border: 2px solid;padding: 25px;">
      <h6 class="text-center"><b><span style="color:red;">Term-1</span> Academic Performance - Session {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}</b></h6>
      <p class="mb-0"><b>Personal Profile</b></p>
        <div class="img_background_fixed">
            <div class="img_absolute">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/default/rukmani_logo.png' }}'" alt="" class="backhround_img">
            </div>
  <table class="" style="border: 2px solid;width: 100%;">
    
			<tbody >
			<tr>
			    <td class="fontweight">Enrollment No. :</td>
			    <td>{{$admission_id->admissionNo ?? ''}}</td>
			    <td class="fontweight ctc">ExamRollNo. :</td>
			    <td>{{$admission_id->exam_roll_no ?? '' }}</td>
			    <td class="fontweight ctc">Student Name :</td>
			    <td>{{$admission_id->first_name ?? '' }}</td>
			</tr>	
			<tr>
			    <td class="fontweight">Class & Section :</td>
			    <td>{{$className->name ?? '' }}</td>
			    <td class="fontweight ctc">Date Of Birth :</td>
			    <td>{{date('d-M-Y', strtotime($admission_id->dob ?? '')) ?? '' }}</td>
			    <td class="fontweight ctc">Mother's Name :</td>
			    <td>{{$admission_id->mother_name ?? '' }}</td>
			</tr>	
			<tr>
			    <td class="fontweight">House :</td>
			    <td>{{$admission_id->house ?? '' }}</td>
			    <td class="fontweight ctc">Blood Group :</td>
			    <td>{{ $admission_id->blood_group ?? ''}}</td>
			    <td class="fontweight ctc">Father's Name :</td>
			    <td>{{ $admission_id->father_name ?? '' }}</td>
			</tr>	
			<tr>
			    <td class="fontweight">Gender :</td>
			    <td>{{ $gender->name ?? '' }}</td>
			    <td class="fontweight ctc">Address :</td>
			    <td>{{$admission_id->address ?? '' }}</td>
			   <td></td>
			   <td></td>
			</tr>
		   <tr>
			    <td class="fontweight">Height :</td>
			    <td>{{$admission_id->height ?? '' }}</td>
			    <td class="fontweight ctc">Weight :</td>
			    <td>{{$admission_id->weight ?? '' }}</td>
			    <td class="fontweight ctc" style="color:yellow;">Attendance :</td>
			    <td>182/200</td>
			</tr>
		
					    
  </tbody>
  </table>
  
       <p class="mb-0"><b>Seholastic Area:</b></p>
  <table class="" style="border: 2px solid;width: 100%;">
<thead>
  <tr>
    <th class="AreaBorder subpadding">
      Subject
    </th>
    @foreach($list_exam as $item)
    <th class="AreaBorder ctc ">{{$item->name ?? ''}}</th>
    @endforeach
    <th class="AreaBorder ctc">Total</th>
    <th class="AreaBorder ctc">Grade</th>
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
                                
                                
     <td class="AreaBorder ctc ">{{$number->student_marks ?? ''}}/{{$maximum_marks}}</td>
     @endforeach
     <td class="AreaBorder ctc ">{{$obtained_total_right}}</td>
     <td class="AreaBorder ctc ">
         @php
        
         $grade = ($obtained_total_right / $maximum_marks_total_right)*100;
         @endphp
           @if( $grade >=91 && $grade <=100 )
                                    A1
                                @elseif( $grade >=81 && $grade <=90.99)
                                A2
                                @elseif( $grade >=71 && $grade <=80.99)
                                B1
                                @elseif( $grade >=61 && $grade <=70.99)
                                B2
                                @elseif( $grade >=51 && $grade <=60.99)
                                C1
                                @elseif( $grade >=41 && $grade <=50.99)
                                C2
                                @elseif( $grade >=33 && $grade <=40.99)
                                D
                                @elseif( $grade >=0 && $grade <=32.99)
                               E
                                
                                    @endif
         
     </td>
 </tr>
 @endif
 @endforeach
 @endif
</thead>

</table>

 <table>
  <tbody class="mt-3">
 
         <tr>
			    <td></td>
			    <td></td>
			    <td style="width: 45%;"></td> 
			    
			    @php
			    $percentage = ($total_obtained/$total_maximum_possible)*100;
			    @endphp
			    <td class="rtr"><b>Percentage : {{ round($percentage ,2) }}%</b></td>
			    <td class="rtr">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> Grand Total : {{$total_obtained}} / {{$total_maximum_possible}}</b></td>
			</tr>
  </tbody>
  </table>

  <table class="mt-2" style="border: 2px solid;width: 81%;">
    
			<tbody >
			<tr>
			    <th class=" AreaBorder"><b>Non Seholastic Performance</b> </th>
			    <th class="AreaBorder ctc"><b>Term 1</b></th>
			</tr>
			
			<tr>
			    <td class="fontweight AreaBorder">ART EDUCATION</td>
			    <td class="fontweight AreaBorder ctc">A</td>
			</tr>
			<tr>
			    <td class="fontweight AreaBorder">HEALTH AND PHYSICAL EDUCATION</td>
			    <td class="fontweight AreaBorder ctc">A</td>
			</tr>
			<tr>
			    <td class="fontweight AreaBorder">GENERAL KNOWLEDGE</td>
			    <td class="fontweight AreaBorder ctc">C</td>
			</tr>
			<tr>
			    <td class="fontweight AreaBorder">MORAL SCIENCE </td>
			    <td class="fontweight AreaBorder ctc">A</td>
			</tr>
			<tr>
			    <td class="fontweight AreaBorder">WORK EDUCATION</td>
			    <td class="fontweight AreaBorder ctc">A</td>
			</tr>
  </tbody>
  </table>
  
  <table class="mt-3" style="border: 2px solid;width: 50%;">
    
			<tbody >
			<tr>
			    <th class=" AreaBorder"><b>Non Co-Seholastic Performance</b> </th>
			    <th class="AreaBorder ctc"><b>Term 1</b></th>
			</tr>
			<tr>
			    <td class="fontweight AreaBorder">DISCIPLINE</td>
			    <td class="fontweight AreaBorder ctc">C</td>
			</tr>
		
  </tbody>
  </table>
  <table class="" style="border: 2px solid;width: 100%;margin-top: 11%;">
    
			<tbody >
			<tr style="border-bottom: 2px solid;">
			    <td class="fontweight">Percentage Grade(Scholastic): A1(91%-100%),</td>
			    <td class="fontweight">A2(81%-90%),</td>
			    <td class="fontweight">B1(71%-80%),</td>
			    <td class="fontweight">B2(61%-70%),</td>
			</tr>
			<tr>
			    <td class="fontweight rtr">C1(51%-60%),</td>
			    <td class="fontweight"> &nbsp; &nbsp;     C2(41%-50%),</td>
			    <td class="fontweight">&nbsp;&nbsp;D(33%-40%),</td>
			    <td class="fontweight">&nbsp;&nbsp;E(0%-32%),</td>
			</tr>
		
  </tbody>
  </table>
  
  <table class="mt-5" style="border-top: 2px solid;width: 100%;">
    
			<tbody>
			<tr>
			    <td class="fontweight" style="width: 11%;padding: 25px 0px 0px 0px;">Remarks</td>
			    <td class="fontweight" style="border-bottom: 2px solid;padding: 25px 0px 0px 0px;">Aavi kumawat</td>
			</tr>
		
		
  </tbody>
  </table>
  
  <table class="mt-4" style="width: 100%;">
    
			<tbody>
			<tr>
			    <td class="fontweight">31/10/2023</td>
			    <td class="fontweight ctc">Ms. Tarunlata Makker</td>
			    <td class="fontweight ctc"></td>
			</tr>
			<tr>
			    <td class="fontweight">Result Date</td>
			    <td class="fontweight ctc">Class Teacher</td>
			    <td class="fontweight ctc">Principal</td>
			</tr>
		
		
  </tbody>
  </table>
  
  </div>
  </div>
    @endforeach
    @endif


</body>

</html>