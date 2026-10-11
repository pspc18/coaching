<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Rule;
use App\Models\Admission;
use App\Models\Master\TeacherSubject;
use App\Models\Master\GatePass;
use App\Models\IdCardTemplate;
use App\Models\Subject;
use Session;
use Hash;
use Str;
use Redirect;
use Auth;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller

{
           
            public function myteachers(Request $request){
                return view('student_login.my_teachers', ['data' => collect([])]);
            }
            
            public function galleryView(Request $request){
                return view('student_login.gallery',['data'=>[]]); 
            }   
             public function subjectView(Request $request){
                $data = Subject::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                ->where('class_type_id',Session::get('class_type_id'))->orderBy('id', 'DESC')->get();
                return view('student_login.subject',compact('data'));
            }
            public function ruleView(){
                $data = Rule::get();
                return view('student_login.rules', ['data' => $data]);
            }
            
            public function gatePassView(){
                $admissionNo = Admission::where('session_id', Session::get('session_id'))->where('id', Session::get('id'));
                $count = $admissionNo->count();
                $data = "";
                if ($count > 0) {
                    $admissionNo = $admissionNo->first();
                    $data = GatePass::where('admissionNo', $admissionNo->id)->get();
                }
                return view('student_login.gate_pass', ['data' => $data]);
            }
            
            
            
            
            public function my_id_card(Request $request){
   
            $student = Admission::select('admissions.*', 'class_types.name as class_name')
                ->leftJoin('class_types', 'class_types.id', '=', 'admissions.class_type_id')
                ->where('admissions.id', Session::get('id'))
                ->firstOrFail();
        
            $template = IdCardTemplate::where('branch_id', Session::get('branch_id'))
                ->latest()
                ->first();
        
            if (!$template) {
                return back()->with('error', 'No ID Card template found.');
            }
        
            $design = is_string($template->design_content)
                ? json_decode($template->design_content, true)
                : $template->design_content;
        
            return view('student_login.my_id_card', compact('student', 'template', 'design'));
        }

}
