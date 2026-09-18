<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\ExaminationSchedule;
use App\Models\exam\ExaminationScheduleDetail;
use App\Models\Master\TeacherSubject;
use App\Models\Teacher;
use App\Models\Subject;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExamScheduleController extends Controller
{
    public function addExaminationSchedule(Request $request){
        $search['class_type_id'] = $request->class_type_id;
        $search['exam_id'] = $request->exam_id;
        $data = [];
       $exam = AssignExam::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
	    ->leftjoin('exams as exam','assign_exams.exam_id','exam.id')
	    ->where("exam.session_id", Session::get("session_id"))
        ->where("exam.branch_id", Session::get("branch_id"))
        ->where('assign_exams.class_type_id',$request->class_type_id)->get();

        if($request->isMethod('post')){
              
              $data = Subject::where('class_type_id',$request->class_type_id)->orderBy('name','ASC');
            if(Session::get('role_id') == 2)
            {
                  $checkClassTeacher= Teacher::where('id',Session::get('teacher_id'))->where('class_type_id',$request->class_type_id)->first();
                
           if(empty($checkClassTeacher))
              {
                 $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->where('class_type_id',$request->class_type_id)->groupBy('subject_id')->get();
                
              if(!empty($classes))
              {
                   $att = array();
                  foreach($classes as $item)
                  {
                      $att[] = $item->subject_id;
                  }
                  
                  $data =$data->whereIn('id',$att);
              }
              }
            }
            $data = $data->get();
        }
      
        return view('examination.offline_exam.exam_schedule.add',['search'=>$search, 'data'=> $data,'exam'=>$exam]);
    }
    
    
     public function SubmitSchedule(Request $request){
        if($request->isMethod('post')){
            if(!empty($request->examination_schedule_id)){
                for($i=0; $i<count($request->examination_schedule_id); $i++){
                    if($request->examination_schedule_id[$i] != ''){
                        $add = ExaminationSchedule::find($request->examination_schedule_id[$i]);
                    }else{
                        $add = new ExaminationSchedule();
                    }
                      if($request->date[$i] == null && $request->examination_schedule_id[$i] != ''){
                             $add = $add->delete();

                      }else{
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
                    $add->class_type_id = $request->class_type_id;
                    $add->exam_id = $request->exam_id;
                    $add->date = $request->date[$i];
                    $add->from_time = $request->from_time[$i];
                    $add->to_time = $request->to_time[$i];
                    $add->subject_id = $request->subject_id[$i];
                    
                    if(!empty($request->date[$i])){
                        $add->save();
                    }
                      }
                }
            }
      
    }
    return response()->json(['status' => 'success', 'message' => 'Schedule Update Successfully.']);
        // return redirect::to('add/examination_schedule')->with('message', 'Schedule Update Successfully.');
        
    }
    
}