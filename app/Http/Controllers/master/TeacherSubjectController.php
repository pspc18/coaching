<?php

namespace App\Http\Controllers\master;

use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\TeacherCategory;
use App\Models\Subject;
use App\Models\Master\TeacherSubject;
use Session;
use Hash;
use Str;
use Arr;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TeacherSubjectController extends Controller

{
            
public function teacherSubjectAdd(Request $request)
{
    if ($request->isMethod('post')) {

        $data = $request->schedule ?? [];

        foreach ($data as $classId => $periods) {
            foreach ($periods as $periodId => $val) {

                // ✅ Check: teacher_id या subject_id में से कोई भी missing हो तो skip
                if (empty($val['teacher_id']) || empty($val['subject_id'])) {
                    continue;
                }

                TeacherSubject::updateOrCreate(
                    [
                        'class_type_id'  => $classId,
                        'time_period_id' => $periodId,
                    ],
                    [
                        'teacher_id' => $val['teacher_id'],
                        'subject_id' => $val['subject_id'],
                        'session_id'  => Session::get('session_id'),
                        'branch_id'   => Session::get('branch_id'),
                        'user_id'     => Session::get('id'),
                    ]
                );
            }
        }

        return redirect()->back()->with('message', ' Time Table saved successfully!'); 
    }

    $data = TeacherSubject::select(
            'teacher_subjects.*',
            'class_types.name as class_name',
            'subject.name as subject_name',
            'teachers.first_name',
            'teachers.last_name',
            'time_periods.from_time',
            'time_periods.to_time'
        )
        ->leftJoin('class_types', 'class_types.id', 'teacher_subjects.class_type_id')
        ->leftJoin('subject', 'subject.id', 'teacher_subjects.subject_id')
        ->leftJoin('teachers', 'teachers.id', 'teacher_subjects.teacher_id')
        ->leftJoin('time_periods', 'time_periods.id', 'teacher_subjects.time_period_id')
        ->where('teacher_subjects.branch_id', Session::get('branch_id'))
        ->orderBy('class_types.orderBy', 'ASC')
        ->orderBy('time_periods.from_time', 'ASC')
        ->get();

    return view('master.TeacherSubject.add', ['data' => $data]);
}


 

      

            public function printTimeTable(Request $request){
                if($request->isMethod('post')){
                    $data = TeacherSubject::select('teacher_subjects.*','class_types.name as class_name','subject.name as subject_name','teachers.first_name','teachers.last_name',
                    'time_periods.from_time','time_periods.to_time')
                    ->leftjoin('class_types','class_types.id','teacher_subjects.class_type_id')
                    ->leftjoin('subject','subject.id','teacher_subjects.subject_id')
                    ->leftjoin('teachers','teachers.id','teacher_subjects.teacher_id')
                    ->leftjoin('time_periods','time_periods.id','teacher_subjects.time_period_id')->where('teacher_subjects.branch_id',Session::get('branch_id'));
                    if(!empty($request->class_type_id_print)){
                        $data = $data->where('teacher_subjects.class_type_id', $request->class_type_id_print)->orderBy('class_types.orderBy','ASC')->orderBy('time_periods.from_time','ASC')->get();
                    }
                    else{
                        $data = $data->orderBy('class_types.orderBy','ASC')->orderBy('time_periods.from_time','ASC')->get();
                    }
                    $printPreview =    Helper::printPreview('Time Table');
                }
                return view($printPreview, ['data' => $data]);                       
            }
            
          






    
}
