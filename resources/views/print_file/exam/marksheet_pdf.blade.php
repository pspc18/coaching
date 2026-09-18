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
use Illuminate\Support\Arr;
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
    
    td{
        /*font-size:17px;
        padding:5px;*/
        font-size: 15px;
        padding: 4px;
    }
    th{
        font-size:17px !important;
        padding:5px;
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
        padding: 7px;
    }
    .border_percentage{
       border: 2px solid black;
    }
   
    @media print {
    }    
        
        @page {
            border: 2px solid black;
          size: 21cm 29.7cm;
          margin: 0;
        }
        @page { size: auto; size: A4 portrait; }
    </style>
    @if(!empty($admission_id))
    @foreach($admission_id as $admission_id)
	    <table style="border: 2px solid;width: 100%;">
			<tbody >
					<tr>
      <td  rowspan="4">
          <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }}" style="width: 97px;margin-top: -20px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/rukmani_logo.png' }}'">
      </td>
  
      <td   style=" font-size:25px;text-align:center;width:100%;"><span class="style71"><strong>{{$getSetting['name'] ?? ''}} </strong></span></td>
   
      <td rowspan="4"> 
<!--      <img  rowspan="4" src="{{ env('IMAGE_SHOW_PATH').'/setting/left_logo/'.$getSetting['left_logo'] }} " style="width: 97px;" onerror="this.src='{{ env('IMAGE_SHOW_PATH').'/common_images/rukmani_logo.png' }}'">
-->      </td>
     
    
     
   </tr>
	<tr style="text-align:center;">
       
 
   
      <td   style="text-align:center;"><p style="margin-bottom: 0px;"><b >Address </b> {{$getSetting['address'] ?? ''}}</p></td>

      </tr>
      <tr>
     
   
      <td  style="text-align:center;"><p style="margin-bottom:2px;"><b>Email :</b> {{$getSetting['gmail'] ?? ''}}  </p></td>
    </tr>


    
  </tbody>
  </table>
  
    @php
        $className = DB::table('class_types')
        ->where('id',$admission_id->class_type_id)
        
         ->where('branch_id',Session::get('branch_id'))
        ->whereNull('deleted_at')->first();
        
         $session  = DB::table('sessions')->where('id',Session::get('session_id'))->whereNull('deleted_at')->first();
         $gender  = DB::table('gender')->where('id',$admission_id->gender_id)->whereNull('deleted_at')->first();
    @endphp
    
        <div class="" style="border: 2px solid black;padding: 23px;margin-top:-22px">
      <h6 class="text-center" style="margin-top: -14px;margin-bottom: 0px;"><b>@if(count($list_exam) < 3 )
       @foreach($list_exam as $key=>$item)
        @if($key == 1)    
         {{$item->name ?? ''}}
        @endif
    @endforeach
      
      @endif</span> Academic Performance - Session {{$session->from_year ?? '' }}{{"-"}}{{$session->to_year ?? ''}}</b></h6>
      <p class="mb-0"><b>Personal Profile</b></p>
        <div class="img_background_fixed">
            <div class="img_absolute">
            <img src="{{ env('IMAGE_SHOW_PATH').'/setting/watermark_image/'.$getSetting['watermark_image'] }}" alt="bg_logo" class="backhround_img">
            </div>
  <table class="" style="padding:2px;border:2px solid black;width: 100%;">
    
			<tbody >
			<tr>
			    <td class="fontweight">S.R. No. :</td>
			    <td>{{$admission_id->admissionNo ?? ''}}</td>
			    
			    <td class="fontweight ">Student Name :</td>
			    <td>{{$admission_id->first_name ?? '' }} {{$admission_id->last_name ?? '' }}</td>
			    <td class="fontweight">Class:</td>
			    <td>{{$className->name ?? '' }}</td>
			</tr>	
			<tr>
			    
			    <td class="fontweight ">ExamRollNo. :</td>
			    <td>{{$admission_id->exam_roll_no ?? '' }}</td>
			    <td class="fontweight ">Date Of Birth :</td>
			    <td>{{date('d-M-Y', strtotime($admission_id->dob ?? '')) ?? '' }}</td>
			    <td></td>
			    <td></td>
			</tr>	
			<tr>
			   
			    <td class="fontweight ">Father's Name :</td>
			    <td>{{ $admission_id->father_name ?? '' }}</td>
			    <td class="fontweight ">Mother's Name :</td>
			    <td>{{$admission_id->mother_name ?? '' }}</td>
			    <td></td>
			    <td></td>
			</tr>	
		
		
  </tbody>
  </table>
  
       <p class="mb-0" style='margin-top:2px;margin-bottom:20px'><b>Scholastic Area:</b></p>
  <table class="" style="border: 2px solid;width: 100%;">
<thead>
  <tr>
    <th class="AreaBorder subpadding">
      Subject
    </th>
    @foreach($list_exam as $item)
    <th class="AreaBorder ctc ">{{$item->name ?? ''}}</th>
    @endforeach
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
   @if(($item_subject->other_subject ?? '') != 1)
 <tr>
     <td class="AreaBorder subpadding">{{$item_subject->name ?? '' }}</td>
     @php
     $array=[];
     
     @endphp
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
                                
                                
                               
                                 $maximum_marks = $number_max->exam_maximum_marks ?? 0;
                                 
                                  if($maximum_marks != 0)
                                  {
                                  
                                  if($number->student_marks != ''  )
                                  {
                                  
                                  if($number->student_marks == 'AB')
                                  {
                                  
                                   $obtained_total_right += 0;
                                $total_obtained += 0;
                                  
                                  }
                                  
                                  else{
                                  $obtained_total_right += $number->student_marks ?? 0;
                                $total_obtained += $number->student_marks ?? 0;
                             
                                  }
                                     $maximum_marks_total_right += $number_max->exam_maximum_marks ?? 0;
                              
                              
                              $total_maximum_possible += $number_max->exam_maximum_marks ?? 0;
                                  }
                                  
                                  
                                  }
                                  else
                                  {
                                  $array[] = $number->student_marks ?? '';
                                  }
                                  
                              
                                @endphp
     <td class="AreaBorder ctc ">
         @if($maximum_marks ==0 )
         {{$number->student_marks ?? '-'}}
         @else
         
         @if($number->student_marks != '')
         
           @if($number->student_marks == 'AB')
                                 AB 
                                  @else
                                  
                                      <!-- {{$number->student_marks ?? 0}}/{{$maximum_marks}}       -->     
                                    @php
                                   $total_number =  $number->student_marks / $maximum_marks*100;
                                    @endphp
                                    
                                        @if( $total_number >=91 && $total_number <=100 )
                                        A+
                                        @elseif( $total_number >=76 && $total_number <=90.99)
                                        A
                                        @elseif( $total_number >=61 && $total_number <=75.99)
                                        B
                                        @elseif( $total_number >=41 && $total_number <=60.99)
                                        C
                                        @elseif( $total_number >=0 && $total_number <=40.99)
                                        D
                                        @else
                                          -
                                        @endif
                                  @endif
        
         @else
         -
         @endif
           
         @endif
       </td>
     @endforeach

     <td class="AreaBorder ctc ">
         
         @php
         $grade ='';
         if($maximum_marks == 0)
         {
          $grade = '' ;
         }
         else
         {
         if($number->student_marks != '')
         {
          $grade = ($obtained_total_right / $maximum_marks_total_right)*100;
          }
          
         }
         
        
         @endphp
         @if($maximum_marks == 0)
            @if(!empty($array))
        
             @if(in_array('A1', $array))
                                    A1
                                @elseif(in_array('A2', $array))
                                A2
                                @elseif(in_array('B1', $array))
                                B1
                                @elseif(in_array('B2', $array))
                                B2
                                @elseif(in_array('C1', $array))
                                C1
                                @elseif(in_array('C2', $array))
                                C2
                                @elseif(in_array('D', $array))
                                D
                                @elseif(in_array('E', $array))
                               E
                               
                               @endif
           
            @endif
         @else
         
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
                                       
                               @else
                              @php
                              $grade ='';
                              if($number->student_marks != '')
                              {
                               $grade = ($obtained_total_right / $maximum_marks_total_right)*100;
                              }
                              else
                              {
                              $grade ='';
                              }
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
                                @else
                                  -
                                @endif
                               
                               
                               
                               
                                    @endif
                                    
         @endif
                                    
                                    
     </td>
 </tr>
 @endif
 @endforeach
 @endif
 

</thead>
</table>
 <table width='100%'>
  <tbody class="mt-3" >
         <tr>
			   
			    @php
			
			    $percentage = ($total_obtained/($total_maximum_possible == 0 ? 1 : $total_maximum_possible))*100;
			    @endphp
			    <td class="rtr subpadding"><b>Percentage : {{ round($percentage ,2) }}%</b> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> <!--Grand Total : {{$total_obtained}} / {{$total_maximum_possible}}--></b></td>
			    
			</tr>
  </tbody>
  </table>
  <table class="mt-2" style="border: 2px solid;width: 81%;">
			<tbody>
			<tr>
			    <th class=" AreaBorder"><b>Non Scholastic Performance</b> </th>
			    
			  
			     @foreach($list_exam as $item)
			      	   
                     <th class="fontweight AreaBorder ctc"> {{$item->name ?? ''}}                
                       

                @endforeach
			</tr>
     			      	  <tr>
			   @foreach($list_subject as $key => $item_subject)
  
   @if(($item_subject->other_subject ?? '') == 1)
    <td class="fontweight AreaBorder">{{$item_subject->name ?? 'd'}}</td>
    
     	       @foreach($list_exam as $item)
			      	     @php
			      	     
		 	 $scholastic_number =    DB::table('performance_marks')->where('term_id',$item->id)->where('admission_id',$admission_id->id)
		 	 ->where('performance',0)
		 	 ->where('session_id',Session::get('session_id'))
                                    ->where('branch_id',Session::get('branch_id'))
                                    ->where('deleted_at',null)
                                    ->get();
                                    
                                  
	

			    
			    @endphp
			      	   
			      	  @if(!empty($scholastic_number))
			      @foreach($scholastic_number as $number)
			      
			      
	    

            <td class="fontweight AreaBorder ctc">{{$number->student_marks ?? 0}}</td> 
           
            
            
                           @endforeach
                           
                          
                 @endif
                    
                @endforeach
   @endif
		@endforeach	 
			 
			 
	 </tr>		 
			 
	
  </tbody>
  </table>
  
  

  <table class="border_percentage" style="width: 100%;margin-top: 2%;">
			<tbody >
			<tr style="height: 84px;">
			    <td class="fontweight">Percentage Grade(Scholastic): </td>
			    <td class="fontweight">A+(91%-100%), A(76%-90%),</td>
			    <td class="fontweight">B(61%-75%),</td>
			    <td class="fontweight">C(41%-60%),</td>
			     <td class="fontweight">D(0%-40%)</td> 
			</tr>
		
  </tbody>
  </table>
  <table class="mt-5" style="border-top: 2px solid;width: 100%;">
			<tbody>
			<tr>
			    <td class="fontweight" style="width: 11%;padding: 25px 0px 0px 0px;">Remarks</td>
			    <td class="fontweight" style="border-bottom: 2px solid;padding: 25px 0px 0px 0px;"></td>
			</tr>
  </tbody>
  </table>
  <table class="mt-4" style="width: 100%;">
			<tbody>
			<tr>
			    <td style='width:33.3%'class="fontweight"></td>
			    <td style='width:33.3% mt-4'class="fontweight ctc"></td>
			    <td style='width:33.3%'class="fontweight ctc"></td>
			</tr>
			<tr>
			    <td class="fontweight pt-4">Result Date</td>
			    <td class="fontweight ctc pt-4">Principal</td>
			    <td class="fontweight ctc pt-4">Exam Controller</td>
			</tr>
  </tbody>
  </table>
  </div>
  </div>
 
    @endforeach
    @endif
</body>
</html>