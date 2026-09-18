<?php

namespace App\Http\Controllers\student_login;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Teacher;
use App\Models\TeacherDocuments;
use App\Models\SalaryDocument;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\Setting;
use App\Models\Subject;
use App\Models\WhatsappSetting;
use App\Models\PermissionManagement;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\ClassType;
use App\Models\Master\TeacherSubject;
use App\Models\Master\TimePeriods;
use Helper;
use Session;
use Hash;
use Str;
use PDF;
use Redirect;
use Auth;
use File;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Spatie\DataUrl\DataUrl;
use Detection\MobileDetect;


class TeacherController extends Controller

{
           
               public function index(Request $request){
                $detect = new MobileDetect;
                $search['name'] = $request->name;
                $branch = Session::all();
                //  dd($branch);
                
                
           
            $data = Teacher::select('teachers.*','doc.photo')
            ->leftJoin('teacher_documents as doc','doc.teacher_id','teachers.id')
             ->leftJoin('users as user','user.teacher_id','teachers.id')
            // ->where('teachers.class_type_id',Session::get('class_type_id'))
            ->whereNotNull('teachers.class_type_id')
            ->where('user.status',1);
          
            $data = $data->where('teachers.branch_id', Session::get('branch_id'));
            
          
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
                
                if ($detect->isMobile() && !$detect->isTablet()) {
                     return view('student_login.mobile_view.teachers', ['data' => $data, 'search' => $search]);
                }
                
                return view('staff.add_teachers.index', ['data' => $data, 'search' => $search]);
            }
    
            
            
            public function checkClassTeacher(Request $request){
                $data = Teacher::select('teachers.*','doc.photo','class.name as class_name')
                ->leftJoin('teacher_documents as doc','doc.teacher_id','teachers.id')->leftJoin('class_types as class','teachers.class_type_id','class.id')
                ->where('teachers.class_type_id',$request->class_type_id)->first();
                if(!empty($data)){
                    return response()->json([
                        'teacher' =>  $data,
                    ]);
                }
                else{
                    return response()->json([
                        'teacher' =>  null,
                    ]);
                }
            }
            
           
            
public function getDataClassSubject(Request $request)
{
    $class_type_id = $request->class_type_id;
    $teacher_id = $request->teachers_id;

    // Subjects
    $subjects = Subject::select('subject.*', 'classtype.name as class_name')
        ->leftJoin('class_types as classtype', 'classtype.id', 'subject.class_type_id')
        ->where('subject.class_type_id', $class_type_id)
        ->get();

    $class_name = ClassType::find($class_type_id);

    // Periods
    $getTimePeriod = TimePeriods::orderBy('id', 'ASC')
        ->where('branch_id', Session::get('branch_id'))
        ->whereNull('deleted_at')
        ->get();

    // Already assigned periods (for whole class, not just subject)
    $alreadyAssignedPeriods = TeacherSubject::where('class_type_id', $class_type_id)
        ->pluck('subject_id', 'time_period_id') // period_id => subject_id
        ->toArray();

    $html = '';
    $html .= '<div class="class-subject-box border p-2 mb-3 class_type_id' . $class_name->id . '">';
    $html .= '<h4 class="text-primary">' . ($class_name->name ?? 'Class') . '</h4>';
    $html .= '<div class="row">';

    if ($subjects->isNotEmpty()) {
        foreach ($subjects as $subject) {
            $html .= '<div class="col-12 mb-3">';
            $html .= '<h6 class="mb-1">' . $subject->name . '</h6>';

            if ($getTimePeriod->isNotEmpty()) {
                foreach ($getTimePeriod as $period) {
                    $checkboxId = 'sub_' . $subject->id . '_per_' . $period->id;

                    // check if this subject already assigned to this period
                    $isChecked = TeacherSubject::where('teacher_id', $teacher_id)
                        ->where('class_type_id', $class_type_id)
                        ->where('subject_id', $subject->id)
                        ->where('time_period_id', $period->id)
                        ->exists();
                    // check if this period is already taken by some other subject
                    $isDisabled = isset($alreadyAssignedPeriods[$period->id]) && $alreadyAssignedPeriods[$period->id] != $subject->id;

                    $html .= '<div class="form-check form-check-inline">';
                    $html .= '<input class="form-check-input" type="checkbox" 
                                id="' . $checkboxId . '" 
                                name="subject_period[' . $subject->id . '][]" 
                                value="' . $period->id . '" 
                                ' . ($isChecked ? 'checked="checked"' : '') . ' 
                                ' . ($isDisabled ? 'disabled' : '') . '>';
                    $html .= '<label class="form-check-label" for="' . $checkboxId . '">' . date("h:i A", strtotime($period->from_time)) . ' to ' . date("h:i A", strtotime($period->to_time)) . '</label>';
                    $html .= '</div>';
                }
            } else {
                $html .= '<p class="text-danger small">No Periods Found</p>';
            }

            $html .= '</div>';
        }
    } else {
        $html = '<p class="text-danger">No Subjects Found</p>';
    }

    $html .= '</div>'; // row
    $html .= '</div>'; // class-subject-box

    return response()->json(['html' => $html]);
}








}
