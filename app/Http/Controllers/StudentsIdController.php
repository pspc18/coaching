<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Student;
use App\Models\Admission;
use App\Models\RollNumber;
use App\Models\StudentId;
use App\Models\StudentAction;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use Session;
use Hash;
use Helper;
use Str;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentsIdController extends Controller

{
            public function studentsIdData(Request $request){
        	     $admission_no = $request->get('admission_no');
        	     $class_type_id = $request->get('class_type_id');
        	     $country_id = $request->get('country_id');
        	     $state_id = $request->get('state_id');
        	     $city_id = $request->get('city_id');
                 $data =  Admission::with('ClassTypes');
                if(Session::get('role_id') > 1){
                    $data = $data->where('branch_id', Session::get('branch_id'));
                }
                if (!empty(Session::get('admin_branch_id'))) {
                    $data = $data->where('branch_id', Session::get('admin_branch_id'));
                }
                if(!empty($admission_no )){
                    $data = $data ->where("admissionNo", $admission_no);
                }
                if(!empty($class_type_id )){
                    $data = $data ->where("class_type_id", $class_type_id);
                } 
                if(!empty($country_id)){
                    $data = $data ->where("country_id", $country_id);
                }
                if(!empty($state_id)){
                    $data = $data ->where("state_id", $state_id);
                }
                if(!empty($city_id)){
                    $data = $data ->where("district_id", $city_id);
                }
                $alladmission = $data->orderBy('id','DESC')->get();
                return  view('students.student_id.student_id_Search',['data'=>$alladmission]);
            }    
    
            public function studentIdIndex(Request $request){
                $data = Admission::with('ClassTypes')->where('session_id',Session::get('session_id'))->where('status',1);
                if(Session::get('role_id') > 1){
                    $data = $data->where('branch_id',Session::get('branch_id'));
                }
                if (!empty(Session::get('admin_branch_id'))) {
                    $data = $data->where('branch_id', Session::get('admin_branch_id'));
                }
                if (Session::get('role_id') == 2) {
                    $data = $data->where('class_type_id', Session::get('class_type_id'));
                }
                $search['admission_id'] = $request->admission_id;
                $search['class_search_id'] = $request->class_search_id;
                if ($request->isMethod('post')) {
                    if(!empty($request->admission_id)){
                        $data = $data->where('id',$request->admission_id);
                    }
                    if(!empty($request->class_search_id)){
                        $data = $data ->where("class_type_id", $request->class_search_id);
                    }
                }
                $alladmission =  $data->where('school',1)->orderBy('id','DESC') ->get();
                return view('students.student_id.index',['data'=>$alladmission,'search'=>$search]);
            }
}