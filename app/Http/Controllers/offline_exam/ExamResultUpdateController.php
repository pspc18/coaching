<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\Admission;
use Session;
use Hash;
use PDF;
use Helper;
use App\Models\exam\AssignExam;
use App\Models\exam\ExamResultUpdate; 
use File;
use DB;
use Redirect;
use Auth;
use App\Imports\YourImportClassName;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamResultUpdateController extends Controller
{
    
     public function examResultUpdate(Request $request){
        
        $search['name'] = $request->name;
        $search['class_type_id'] = $request->class_type_id;
        $search['section_id'] = $request->section_id;
          $search['exam_id'] = $request->exam_id;

     $data = Admission::select('admissions.*')->where('admissions.session_id', Session::get('session_id'))->where('admissions.branch_id', Session::get('branch_id'));
        
        if($request->isMethod('post')){
           if(!empty($request->class_type_id)){
               $data = $data ->where("class_type_id", $request->class_type_id);
           }           
            $data = $data->orderBy('first_name','ASC')->get();
        }
        $exam = AssignExam::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
	    ->leftjoin('exams as exam','assign_exams.exam_id','exam.id')
        ->where('assign_exams.class_type_id',$request->class_type_id)->get();

      return  view('examination/offline_exam/exam_result_update/exam_result_update',['data'=>$data,'exam'=>$exam,'search'=>$search]);
    }
    
     public function examResultUpdateSave(Request $request){
         
         if(!empty($request->admission_id)){
        foreach($request->admission_id as $key=>  $item)
                       {
                           
                           //dd($request);

                                $oldData = ExamResultUpdate::where('admission_id',$item)->where('exam_id',$request->exam_ids)->first();
                               
                                if(!empty($oldData)){
                                    $ExamResultUpdate = $oldData;
                                }else{
                                    $ExamResultUpdate = new ExamResultUpdate;//model name
                                }  
                                $ExamResultUpdate->user_id = Session::get('id');
                                $ExamResultUpdate->session_id = Session::get('session_id');
                                $ExamResultUpdate->branch_id = Session::get('branch_id');
                            	$ExamResultUpdate->class_type_id= $request->class_type_id;
                            	 $ExamResultUpdate->exam_id= $request->exam_ids;
                        		$ExamResultUpdate->admission_id= $item;
                        		$ExamResultUpdate->roll_no= $request->roll_no[$key];
                        		$ExamResultUpdate->permote_to= $request->permote_to[$key];
                        		$ExamResultUpdate->attendence= $request->attendence[$key];
                        		$ExamResultUpdate->rank= $request->rank[$key];
                        		$ExamResultUpdate->remark= $request->remark[$key];
                                $ExamResultUpdate->save();
                                
                        	
                            } 
                            
         }
         
                 return redirect::to('exam_result_update')->with('message', 'Exam Result Update Successfully.');

    }
    
    
    
    
}
