<?php

namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\Master\LeaveManagement;
use App\Models\Master\TeacherSubject;
use App\Models\Teacher;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LeaveController extends Controller

{
    

            public function add(Request $request){
                $dataview = LeaveManagement::select('leave_management.*','admissions.first_name','admissions.last_name')
                ->leftjoin('admissions','admissions.id','leave_management.admission_id')
                ->orderBy('id','DESC');
                if(Session::get('role_id') == 2){
                    $classes = Teacher::where('id',Session::get('teacher_id'))->groupBy('class_type_id')->first();
                    $dataview =$dataview->where('leave_management.class_type_id',$classes->class_type_id ?? '');
                }
                $dataview = $dataview->where('leave_management.session_id',Session::get('session_id'))->where('leave_management.branch_id',Session::get('branch_id'))->get();
                return view('master.leave.add',['dataview'=>$dataview]);
            }    
    
            public function leaveStatus(Request $request){
                if($request->id > 0){
                    $data = LeaveManagement::where('id',$request->id)->update(['status'=>$request->status]);
                    if(!empty($data)){
                        echo json_encode(0);
                    }
                    else{
                        echo json_encode(1);
                    }
                }
                else{
                    echo json_encode(2);
                }
            }   
    
}
