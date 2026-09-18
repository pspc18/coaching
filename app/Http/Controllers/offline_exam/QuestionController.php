<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;

use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class QuestionController extends Controller
{
        public function viewQuestion(Request $request){
        
        $search['name'] = $request->name;
        $search['subject_id'] = $request->subject_id;
        $search['question_type_id'] = $request->question_type_id;
        
        $data = Question::select('questions.*','subjects.name as subject_name')
        ->leftjoin('subject as subjects','subjects.id','questions.subject_id');
        
        if(Session::get('role_id') > 1){
           $data = $data->where('questions.branch_id',Session::get('branch_id'));
        }
        
            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("subjects.name",'like','%'.$request->name.'%');
                }
                if (!empty($request->subject_id)){
                    $data = $data->where("questions.subject_id",'like','%'.$request->subject_id.'%');
                }
                if (!empty($request->question_type_id)){
                    $data = $data->where("questions.question_type_id",'like','%'.$request->question_type_id.'%');
                }                
            }else
            {
                 $data = $data->whereDate('questions.created_at', Carbon::today());
            }
            $data = $data->orderBy('id','DESC')->get();
            
        return view('examination.offline_exam.question_bank.view',['data'=>$data,'search'=>$search]);
    }
	
    public function addQuestion(Request $request){
         if($request->isMethod('post')){
                 $request->validate([
                     
        // 'class_type_id'  => 'required',
         'subject_id'  => 'required',
         'name'  => 'required',
         'question_type_id'  => 'required',

         ]);
         
         
         $add = new Question;//model name
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');
		 $add->class_type_id =$request->class_type_id;
		 $add->subject_id =$request->subject_id;
		 $add->name =$request->name;
		 $add->question_type_id =$request->question_type_id;
	     $add->ans1 = $request->ans1;
         $add->ans2 = $request->ans2;
         $add->ans3 = $request->ans3;
         $add->ans4 = $request->ans4;
         $add->correct_ans = $request->correct_ans;
	     $add->save();
	
		  return redirect::to('view/question')->with('message', 'Question added Successfully.');
        }

        return view('examination.offline_exam.question_bank.add');
    } 

    public function editQuestion(Request $request, $id){
         $data = Question::find($id);
          
      
            if($request->isMethod('post')){
                $request->validate([

         /*'class_type_id'  => 'required',*/
         'subject_id'  => 'required',
         'name'  => 'required',
         'question_type_id'  => 'required',
   
         ]);

	     $data->user_id = Session::get('id');
	     $data->session_id = Session::get('session_id');
         $data->branch_id = Session::get('branch_id');
		 $data->class_type_id =$request->class_type_id;
		 $data->subject_id =$request->subject_id;
		 $data->name =$request->name;
		 $data->question_type_id =$request->question_type_id;
	     $data->ans1 = $request->ans1;
         $data->ans2 = $request->ans2;
         $data->ans3 = $request->ans3;
         $data->ans4 = $request->ans4;
         $data->correct_ans = $request->correct_ans;
	     $data->save();

            return redirect::to('view/question')->with('message', 'Question Updated Successfully.');
        }

        return view('examination.offline_exam.question_bank.edit',['data'=>$data]);
    } 

    public function deleteQuestion(Request $request){
        $question = Question::find($request->delete_id)->delete();
        return redirect::to('view/question')->with('message', 'Question Deleted Successfully.');
    }
    
}