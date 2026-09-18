<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\exam\digital\ExamDigital;
use App\Models\exam\digital\ExamResultDigital;
use App\Models\exam\digital\QuestionDigital;

use App\Models\Subject;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class ExaminationController extends BaseController
{
    public function examTerminal(Request $request){
         if($request->isMethod('post')){
             $search['from_date'] = $request->from_date ?? null;
         $search['to_date'] = $request->to_date ?? null;
         $search['exam_pattern'] = $request->exam_pattern ?? null;
         $search['subject'] = $request->subject ?? null;
         
         
         $data = ExamDigital::where('session_id',3)->whereIn('class_type_id',[1,3])->orderBy('id','DESC');
         $exam_count = ExamResultDigital::where('admission_id',$request->admission_id)->get();
        
          if($search['from_date'] != '')
          {
           $data = $data->whereDate('exam_date','>=',$search['from_date'])->whereDate('exam_date','<=',$search['to_date']);
          }
           
          if($search['from_date'] != '')
          {
           $data = $data->whereDate('exam_date','>=',$search['from_date'])->whereDate('exam_date','<=',$search['to_date']);
          }
          
          if($search['exam_pattern'] != '')
          {
           $data = $data->where('pattern_id',$search['exam_pattern']);
          }
          
          if($search['subject'] != '')
          {
              $searchSubject = (array) $search['subject'];
           $data = $data->whereIn('subject_id',$searchSubject);
          }
           
           $data = $data->get();
           
          
            
             $data1 = array();
            foreach ($data as $key => $item) {
                $count = 0;
                foreach ($exam_count as $key2 => $item2) {
                    if($item2->exam_id == $item->id )
                    {
                     $count++;    
                    }
                }
                
                $data1[] = array(
                   'key'=> $key+1,
            'name'=> $item->name,
            'navigation'=> 'Student Dashboard',
            'exam_id'=> $item->id,
            'created_on'=>  date('d/m/Y h:m A', strtotime($item->created_at)),
            'start'=> date('d/m/Y h:m A', strtotime($item->exam_date)) ,
            'exam_given'=> $count,
            'exam_duration'=> $item->duration.' hr :: '. $item->duration_minute.' min',
                );
            }
           
   //     return  view('examination.online_exam.examTerminal',['data'=>$data,'exam_count'=>$exam_count,'search'=>$search]);
         
		    return response()->json(['status' => true, 'message' => 'Exam Fetched Successfully','data'=>$data1,'exam_count'=>$exam_count,'search'=>$search], 200);
		 
        }
    }
    
    
         public function startExam(Request $request){
        
        
       
                $exam_id =$request->exam_id;
                
                
               // dd($exam_id);
                $exam_name = ExamDigital::where('id',$exam_id)->first();
                
        $questions_ids = ExamDigital::where('id',$exam_id)->first();
          $decode = explode(',', $questions_ids->questions_id);
          shuffle($decode);
         $questions = [] ;
         if(!empty($decode))
         {
             
            foreach($decode as $item)
            {
                
                $questions[] = QuestionDigital::select('questions_digital.*','subject.name as subject_name')
                    ->leftjoin('subject','subject.id','questions_digital.subject_id')->where('questions_digital.id',$item)->first();
            }
             
         }
        
   $total_min = ($exam_name->duration*60)+$exam_name->duration_minute ;
   $total_sec = ($exam_name->duration*3600)+($exam_name->duration_minute*60) ;
        
        $currentDateTime = now();
        $futureDateTime = $currentDateTime->addMinutes($total_min);
        $currentDateTimeFormatted = $currentDateTime->format('Y-m-d H:i:s');
        $futureDateTimeFormatted = $futureDateTime->format('Y-m-d H:i:s');


  $data1 = array();
            foreach ($questions as $key => $item) {
               $data1[] = array(
                   'key'=> $key+1,
            'que_id'=> $item->id ,
            'name'=> strip_tags($item->name) ,
            'hi_name'=> strip_tags($item->hi_name),
            'ans_a'=> strip_tags($item->ans_a),
            'ans_b'=> strip_tags($item->ans_b),
            'ans_c'=> strip_tags($item->ans_c),
            'ans_d'=> strip_tags($item->ans_d),
            'hi_ans_a'=> strip_tags($item->hi_ans_a),
            'hi_ans_b'=> strip_tags($item->hi_ans_b),
            'hi_ans_c'=> strip_tags($item->hi_ans_c),
            'hi_ans_d'=> strip_tags($item->hi_ans_d),
            'correct_ans'=> $item->correct_ans,
            'subject_id'=> $item->subject_id,
                );
            }
 

// if(empty(Session::get('exam_'.$exam_id)) )
// {
//       Session::put('exam_'.$exam_id, $futureDateTimeFormatted);
// }
// else
// {
//      $exam_time = \Carbon\Carbon::parse(Session::get('exam_'.$exam_id));
     
//         if($exam_time->isPast())
//         {
//              Session::put('exam_'.$exam_id, $futureDateTimeFormatted);
//         }
// }


        	    return response()->json(['status' => true, 'message' => 'Exam Fetched Successfully','exam_id'=>$exam_id,'total_sec'=>$total_sec,'questions'=>$data1,'exam_name'=>$exam_name], 200);
		 
      
         
            
    }
    
         public function resultExam(Request $request){
         

          if($request->isMethod('post')){
              
              $add = new ExamResultDigital;
                $add->user_id = 1;
               $add->session_id = 3;
               $add->branch_id = 1;
              $add->total_ques = $request->total_ques;
              $add->correct_ans = $request->total_correct;
              $add->wrong_ans = $request->total_wrong;
              $add->skip_ques = $request->total_skip;
              $add->time = $request->spend_time;
              $add->admission_id =$request->admission_id;
              $add->exam_id = $request->exam_id;
            //   $add->skip_ques = $request->not_visited;
              $add->result = $request->result;
              $add->subject_id_groupBy = $request->subject_id_groupBy;
              
              $add ->save();
              
        $exam = ExamDigital::where('id',$request->exam_id)->first();

                $scheduleTime =\Carbon\Carbon::createFromTimestampUTC($request->spend_time)->second;
                
                   // Create a Carbon instance with the duration in minutes

     $minutes = (int)$exam->duration;
   $exam_duration=   intdiv($minutes, 60).' hr : '. ($minutes % 60). ' min';

             //  Session::forget('exam_'.$request->exam_id);
             
              return response()->json(['status' => true, 'message' => 'Exam saved successfully','data'=>$add,'per_question_marks'=>4,'spend_time'=>$scheduleTime,'exam_data'=>$exam,'exam_duration'=>$exam_duration], 200);
		           }
         
    }
    
    public function getExamResult(Request $request){
         

          if($request->isMethod('post')){
              
              $add = ExamResultDigital::find($request->id);
              
               $decode = json_decode($add->result);
                $subject_id_in_use = explode(',', $add->subject_id_groupBy);
             $suject_id = Subject::all();
             
              $subjectArray = array();
            foreach ($suject_id as $key => $item) {
             
               $subjectArray[$item->id] = $item->name; 
            }
              $percentageArray = array();
              $aaa = array();
              $bbb = array();
            foreach ($subject_id_in_use as $key => $item) {
                $bbb[$item] =0;
                
            foreach ($decode as $key => $item1) {
               
             if($item1->subject_id == $item)
             {
                 
                  $aaa[$item][] = $item1;
                  
                  if($item1->correct == 1)
                  {
                       $bbb[$item] += 4;
                  }
                  if($item1->correct == 2)
                  {
                       $bbb[$item] -= 1;
                  }
                 
             }
             
            }
               $percentageArray[$item] = ($bbb[$item]/(count($aaa[$item])*4))*100;
            }
        // $percentageArray['1'.''] = ($bbb[1]/(count($aaa[1])*4))*100;
        // $percentageArray['2'.''] = ($bbb[2]/(count($aaa[2])*4))*100;
 
        $exam = ExamDigital::where('id',$add->exam_id)->first();

     $scheduleTime =\Carbon\Carbon::createFromTimestampUTC($add->time)->second;
                
                   // Create a Carbon instance with the duration in minutes

//      $minutes = (int)$exam->duration;
//   $exam_duration=   intdiv($minutes, 60).' hr : '. ($minutes % 60). ' min';
$exam_duration=   $exam->duration.' hr : '. $exam->duration_minute. ' min';
             //  Session::forget('exam_'.$request->exam_id);
             
              return response()->json(['status' => true, 'message' => 'Exam fetched successfully','percentageArray'=>$percentageArray,'data'=>$add,'decodeResult'=>$decode,'subjectArray'=>$subjectArray,'per_question_marks'=>4,'spend_time'=>$scheduleTime,'exam_data'=>$exam,'exam_duration'=>$exam_duration], 200);
		           }
         
    }
    
      public function analysisPanel(Request $request){
         
            $search['from_date'] = $request->from_date ?? null;
         $search['to_date'] = $request->to_date ?? null;
         $search['exam_pattern'] = $request->exam_pattern ?? null;
          
         
             $admission_id = $request->admission_id;
        
        
         $exam_id = $request->exam_id ?? '';
         
            $data = ExamResultDigital::select('exam_results_digital.*','exams_digital.name as exam_name','exams_digital.id as exam_id','exams_digital.exam_date','exams_digital.duration as exam_time')
        ->leftjoin('exams_digital','exam_results_digital.exam_id','exams_digital.id')->where('exam_results_digital.admission_id',$admission_id)->orderBy('exam_results_digital.id','DESC');
        
        if($exam_id != '')
            {
                $data = $data ->where('exam_results_digital.exam_id',$exam_id);
            }        
        $data = $data->get();
        
        
        




   $data1 = array();
        
        foreach ($data as $key => $item) {
              
                
                $data1[] = array(
                  'key'=> $key+1,
                 'id'=> $item->id,
            'exam_id'=> $item->exam_id,
            'admission_id'=> $item->admission_id,
            'subject_id_groupBy'=> $item->subject_id_groupBy,
            'total_ques'=> $item->total_ques,
            'correct_ans'=> $item->correct_ans,
         'wrong_ans'=> $item->wrong_ans,
'skip_ques'=> $item->skip_ques,
'percentage'=> $item->percentage,
'time'=> $item->time,
'result'=> $item->result,
'status'=> $item->status,
'exam_name'=> $item->exam_name,

'exam_time'=> $item->exam_time,
            'created_on'=>  date('d/m/Y h:m A', strtotime($item->created_at)),
            'exam_date'=> date('d/m/Y h:m A', strtotime($item->exam_date)) ,
                );
            }
           
          return response()->json(['status' => true, 'message' => 'Exam fetched successfully','data'=>$data1,'search'=>$search], 200);
		     
       
         
     }
     
          public function examAnalysis(Request $request){
         
     
              $data = ExamResultDigital::select('exam_results_digital.*','exams_digital.name as exam_name','exams_digital.id as exam_id','exams_digital.exam_date','exams_digital.duration as exam_time')
        ->leftjoin('exams_digital','exam_results_digital.exam_id','exams_digital.id')->where('exam_results_digital.id',$request->id)->first();
         $exam_name = ExamDigital::where('id',$data->exam_id)->first();
         $decode = json_decode($data->result);
                
          $questions = array();
            foreach ($decode as $key => $item) {
                $ques_id = QuestionDigital::where('id',$item->que_id)->first();
                $questions[] =$ques_id;
            }
            
             return response()->json(['status' => true, 'message' => 'Exam fetched successfully','data'=>$data,'decode'=>$decode,'questions'=>$questions,'exam_name'=>$exam_name,'status'=>1], 200);
		     
       

         
     }
    
}