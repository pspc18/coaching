<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\WebUser;
use App\Models\User;
use App\Models\ReactClassType;
use App\Models\Subject;
use App\Models\exam\Question;
use Illuminate\Support\Facades\Auth;
use Validator;
use Hash;
use File;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class QuestionController extends BaseController
{

	public function getSubjectList(Request $request)
	{
	    
	    try{
	    $subjectName = Subject::orderBy('name','ASC')->get();
	    
	     $data = array();
            foreach ($subjectName as $item) {
                $data[] = array(
                    'label' => $item->name,
                    'value' => $item->id,
                );
            }
	     return $this->sendResponseData($data, 'success');
        } catch (Exception $e) {
            return $this->sendError('Validation Error.', 'Error');
        }
	}
	
	public function getQuestionList(Request $request)
	{
	    
	    $questionList = Question::orderBy('name','ASC');
	    
	    if($request->isMethod('post')){
                if (!empty($request->name)){
                    $questionList = $questionList->where("name", $request->name);
                }
                if (!empty($request->subject_id)){
                    $questionList = $questionList->where("subject_id", $request->subject_id);
                }
                if (!empty($request->question_type_id)){
                    $questionList = $questionList->where("question_type_id", $request->question_type_id);
                }
            }
            $questionList = $questionList->get();
            
            $list = array();
            foreach ($questionList as $key => $item) {
                $list[] = array(
                    'key' => $key+1,
                    'id' => $item->id,
                    'name' => $item->name,
                    'question_type_id' => $item->question_type_id,
                    'subject_id' => $item->subject_id,
                    'ans1' =>$item->ans1,
                    'ans2' =>$item->ans2,
                    'ans3' =>$item->ans3,
                    'ans4' =>$item->ans4,
                    'correct_ans' => $item->correct_ans
                );
            }
            
            return response()->json(['status' => true, 'message' => 'Data Found','data'=>$list], 200);
            
            return $this->sendResponseData($data, 'success');
	}
	
	public function getQuestionEdit(Request $request,$id){
	    $data = Question::find($id);
	    
	    if($request->isMethod('post')){
	        $data->name = $request->name;
	        $data->class_type_id = $request->class_type_id;
	        $data->subject_id = $request->subject_id;
	        $data->ans1 = $request->ans1;
	        $data->ans2 = $request->ans2;
	        $data->ans3 = $request->ans3;
	        $data->ans4 = $request->ans4;
	        $data->correct_ans = $request->correct_ans;
	        $data->save();
	    }
	    return response()->json(['status' => true, 'message' => 'Data Found','data'=>$data], 200);
        return $this->sendResponseData($data, 'success');
	}



}