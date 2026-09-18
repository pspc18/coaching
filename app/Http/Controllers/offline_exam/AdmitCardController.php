<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\ExaminationAdmitCard;

use App\Models\exam\Exam;
use App\Models\Admission;
use App\Models\Setting;
use App\Models\AdmitCardNote;
use App\Models\exam\AssignExam;
use Session;

use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PDF;

class AdmitCardController extends Controller
{
   

    public function download_admit_card(Request $request){
        $search['class_type_id'] = $request->class_type_id;
        $search['section_id'] = $request->section_id;
        $search['exam_id'] = $request->exam_id;

        
         $students = Admission::where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)->where('status',1);
              
                

       if($request->isMethod('post')){
           
       if($request->class_type_id != '')
       {
           $students = $students->where("class_type_id", $request->class_type_id);
           $exam =  AssignExam::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
                    	    ->leftjoin('exams as exam','assign_exams.exam_id','exam.id')
                            ->where('assign_exams.class_type_id',$request->class_type_id)
                            ->where('assign_exams.session_id',Session::get('session_id'))
                            ->where('assign_exams.branch_id',Session::get('branch_id'))
                            ->get();
       }
       
         $students = $students->orderBy("first_name", "ASC")->get();
         
         return view('examination.offline_exam.admit_card.download_admit_card',['search'=>$search, 'exam'=>$exam,'data1'=> $students,'exam_id'=>$request->exam_id ?? '']);
   
          
      }
        
        return view('examination.offline_exam.admit_card.download_admit_card',['search'=>$search, 'data1'=> $students,'exam_id'=>$request->exam_id ?? '']);
    }
    


    
     public function downloadAdmitCard(Request $request,$exam_id,$class_type_id,$admission_id){
            $arr ;
        if ($admission_id != ""){
            foreach(explode(',', $admission_id) as $info){
                $arr[] = $info;
            }
        }
      if($request->isMethod('get')){
         
        $data =  Admission::select('admissions.*','class.name as class_name','admissions.image as student_profile_image','admissions.mother_name','admissions.father_mobile','admissions.father_name','admissions.admissionNo','admissions.first_name','admissions.mobile')
	    ->leftjoin('class_types as class','class.id','admissions.class_type_id')
        ->where('admissions.class_type_id',$class_type_id)
        ->whereIn('admissions.id',$arr)->where("admissions.session_id", Session::get("session_id"))
                ->where("admissions.branch_id", Session::get("branch_id"))->orderBy('id', 'ASC')->get();
        
           $school_data = Setting::where("branch_id", Session::get("branch_id"))->first();
              $printPreview =    Helper::printPreview('Admit Card');
            //   dd($printPreview);
           return view($printPreview, ['data'=>$data,'school_data'=>$school_data,'exam_id'=>$exam_id]);

         
            
              
        }
          
      }
     public function without_subject_admit_card(Request $request,$exam_id,$class_type_id,$admission_id){
            $arr ;
        if ($admission_id != ""){
            foreach(explode(',', $admission_id) as $info){
                $arr[] = $info;
            }
        }
      if($request->isMethod('get')){
         
        
           
                      $data =  Admission::select('admissions.*','class.name as class_name','admissions.image as student_profile_image','admissions.mother_name','admissions.father_mobile','admissions.father_name','admissions.admissionNo','admissions.first_name','admissions.mobile')
	    ->leftjoin('class_types as class','class.id','admissions.class_type_id')
        ->where('admissions.class_type_id',$class_type_id)
        ->whereIn('admissions.id',$arr)->where("admissions.session_id", Session::get("session_id"))
                ->where("admissions.branch_id", Session::get("branch_id"))->orderBy('id', 'ASC')->get();
        
           
           $school_data = Setting::where("branch_id", Session::get("branch_id"))->first();
          
              //$pdf = PDF::loadView('print_file.pdf.admit_card_all',['data'=>$data1,'school_data'=>$school_data]);
              $printPreview =    Helper::printPreview('Admit Card');
               //dd($printPreview);
           return view("master.printFilePanel.ExaminationManagement.without_sub_admit_card", ['data'=>$data,'school_data'=>$school_data]);
            // return view('print_file.pdf.admit_card_all',['data'=>$data,'school_data'=>$school_data]);

         
            
              
        }
          
      }
      
          public function AdmitCardNotes(Request $request){
                $data = AdmitCardNote::find($request->id);
                if(!empty($data)){
                    $data->user_id = Session::get('id');
                    $data->session_id = Session::get('session_id');
                    $data->branch_id = Session::get('branch_id');
                    $data->note = $request->note;
                    $data->status = 1;
                    $data->save();
                    return redirect::to('download_admit_card')->with('message', 'Note Edit Successfully');
                }
                else{
                    $note = new AdmitCardNote;
                    $note->user_id = Session::get('id');
                    $note->session_id = Session::get('session_id');
                    $note->branch_id = Session::get('branch_id');
                    $note->note = $request->note;
                    $note->status = 1; 
                    $note->save();
                    return redirect::to('download_admit_card')->with('message', 'Note Add Successfully');
                }
            }
            
            
}