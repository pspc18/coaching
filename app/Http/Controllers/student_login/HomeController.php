<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Rule;
use App\Models\Admission;
use App\Models\Teacher;
use App\Models\Master\SchoolDesk;
use App\Models\Master\TeacherSubject;
use App\Models\Master\BooksUniformShop;
use App\Models\Master\GatePass;
use App\Models\IdCardTemplate;
use App\Models\Master\Uniform;
use App\Models\Master\Prayer;
use App\Models\Master\Gallery;
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
           
            public function schoolDeskView(Request $request){
                $data = SchoolDesk::where('id',1)->first();
                return view('student_login.school_desk', ['data' => $data]);
            }
            public function myteachers(Request $request){
               
                $branch = Session::all();
               
               
           
                $data = Teacher::select('teachers.*','doc.photo')
                ->leftJoin('teacher_documents as doc','doc.teacher_id','teachers.id')
                 ->leftJoin('users as user','user.teacher_id','teachers.id')
                // ->where('teachers.class_type_id',Session::get('class_type_id'))
                ->whereNotNull('teachers.class_type_id')
                ->where('user.status',1);
         
            $data = $data->where('teachers.branch_id', Session::get('branch_id'));
            // if (!empty(Session::get('admin_branch_id'))) {
                //$data = $data->where('branch_id', Session::get('branch_id'));
            //}
         
                 $tea = DB::table('teacher_subjects')
                ->select('teacher_subjects.teacher_id')
                ->leftJoin('teachers as teacher', 'teacher.id', '=', 'teacher_subjects.teacher_id')
                ->leftJoin('users as user', 'user.teacher_id', '=', 'teacher.id')
                ->where('teacher_subjects.branch_id', session('branch_id'))
                ->where('teacher_subjects.class_type_id', session('class_type_id'))
                ->where('user.status', 1)
                ->whereNull('teacher_subjects.deleted_at')
                ->groupBy('teacher_subjects.teacher_id')
                ->pluck('teacher_id');
                $explode = [];
                    if ($tea->isNotEmpty()) {
                        $explode = $tea->toArray(); 
                    }
                    if (!empty($explode)) {
                        $data = $data->whereIn('teachers.id', $explode);
                    }
                
                $data = $data->orderBy('teachers.id', 'DESC')->get();
                return view('student_login.my_teachers', ['data' => $data]);
            }
            
             public function timetableView(){
                $data = TeacherSubject::select('teacher_subjects.*', 'subject.name as subjectName', 'teachers.first_name', 'teachers.last_name', 'class_types.name as className', 'time_periods.from_time', 'time_periods.to_time')
                    ->leftjoin('teachers', 'teachers.id', 'teacher_subjects.teacher_id')
                    ->leftjoin('class_types', 'class_types.id', 'teacher_subjects.class_type_id')
                    ->leftjoin('time_periods', 'time_periods.id', 'teacher_subjects.time_period_id')
                    ->leftjoin('subject', 'subject.id', 'teacher_subjects.subject_id')
                    ->where('teacher_subjects.class_type_id', Session::get('class_type_id'))
                    ->where('teacher_subjects.session_id', Session::get('session_id'))
                    ->get();
                return view('student_login.timetable', ['data' => $data]);
            }
              public function galleryView(Request $request){
                $data= Gallery::groupBy('img_category')->where('branch_id',Session::get('branch_id'));
                $barnch =Session::all();
                    $data = $data->where('type', 'gallery')->orderBy('id','DESC')->get();
                    return view('student_login.gallery',['data'=>$data]); 
            }   
              public function prayerView(){
                $data = Prayer::get();
                return view('student_login.prayer', ['data' => $data]);
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
            
             public function uniformView(){
                $data = Uniform::get();
                return view('student_login.uniform', ['data' => $data]);
            }
            
             public function booksView(Request $request){
                $data = BooksUniformShop::where('branch_id',Session::get('branch_id'))->orderBy('id','DESC')->get();
                return view('student_login.books',['data'=>$data]);
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
