<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admin;
use App\Models\State;
use App\Models\City;
use App\Models\WhatsappApiResponse;
use App\Models\Classs;
use App\Models\Student;
use App\Models\Sessions;
use App\Models\Admission;
use App\Models\exam\AssignExam;
use App\Models\Master\TeacherSubject;
use App\Models\Subject;
use Session;
use Hash;
use Str;
use App;
use Redirect;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller

{
    public function updateSingleField(Request $request){
        if($request->isMethod("post")){
            $field_name = $request->name;
            $value = $request->value;
            $modal = 'App\Models\\' . $request->modal ?? '';
            $id = $request->id;
            $data = $modal::where('id',$id)->first();
            $data->$field_name = $value;
            $data->save();
            
            if(!empty($data)){
                return Response::json(array('status' => true,'message' => "Field Save Successfully")); 
            }else{
                return Response::json(array('status' => false,'message' => "Something Went Wrong")); 
            }
        
            
        }
    }
    
    
    public function countryData(Request $request,$id){
        if(!empty($id)){
            $getState = array();      
         
            $getState = State::where('country_id',$id)->get();
            
            $stateData ='<option value="">Select</option>';
            foreach($getState as $state)
            {
               $stateData.='
               <option value="'.$state->id.'">'.$state->name.'</option>';
            }
           echo $stateData;
        } 
    }
       
    
    public function stateData(Request $request,$id){
        if(!empty($id)){
            $getState = array();      
         
            $getState = City::where('state_id',$id)->get();
            
            $cityData ='<option value="">Select</option>';
            foreach($getState as $city){
                $cityData.='<option value="'.$city->id.'">'.$city->name.'</option>';
            }    
            echo $cityData;
        } 
    }
    
    
     public function examData(Request $request,$class_type_id){
     $data = array();    
         if(!empty($class_type_id)){

	       $data =  AssignExam::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name','assign_exams.exam_date as assign_exam_date')
	    ->leftjoin('exams as exam','assign_exams.exam_id','exam.id')
        ->where('assign_exams.class_type_id',$class_type_id)
        ->where('assign_exams.session_id',Session::get('session_id'))
        ->where('assign_exams.branch_id',Session::get('branch_id'))
        ->whereNull('assign_exams.deleted_at')
        ->whereNull('exam.deleted_at')
        ->get();
	 
        $examData ='<option value="">Select</option>';
            foreach($data as $exam){
                $examLabelDate = !empty($exam->assign_exam_date) ? ' ('.date('d-m-Y', strtotime($exam->assign_exam_date)).')' : '';
           $examData.='
           <option value="'.$exam['exam_id'].'">'.$exam['exam_name'].$examLabelDate.'</option>';
           }
        echo $examData;
       } 
    }    
    
       
     public function studentData ($id){
         $data = array();    
         if(!empty($id)){
        $data = Student::where('section_id',$id)->get();
        $sectionData ='';
            foreach($data as $class){
           $sectionData.='
           <option value="'.$class['id'].'">'.$class['name'].'</option>';
           }
        echo $sectionData;
       } 
     }


    
     
      public function change(Request $request)
    {
        App::setLocale($request->lang);
        session()->put('locale', $request->lang);
       
        return redirect()->back();
    }
    
    public function subjectGetData(Request $request, $id){ 
        if(!empty($id)){
            // If exam_id is provided, prioritize the exam's exact configured subjects
            if ($request->filled('exam_id')) {
                $exam = \App\Models\exam\Exam::find($request->exam_id);
                if ($exam) {
                    $examSubjects = collect();

                    // 1. Check subject_question_ranges
                    $rawRanges = $exam->subject_question_ranges;
                    if (is_string($rawRanges)) $rawRanges = json_decode($rawRanges, true) ?: [];
                    if (is_array($rawRanges) && !empty($rawRanges)) {
                        foreach ($rawRanges as $r) {
                            $sId = (int)($r['subject_id'] ?? 0);
                            $sName = trim((string)($r['subject_name'] ?? ''));
                            if ($sId > 0) {
                                $examSubjects->push((object)[
                                    'id' => $sId,
                                    'name' => (!empty($sName) && !str_starts_with($sName, 'Subject #')) ? $sName : null,
                                ]);
                            }
                        }
                    }

                    // 2. Check selected_subject_ids
                    $selIds = $exam->selected_subject_ids;
                    if (is_string($selIds)) $selIds = json_decode($selIds, true) ?: [];
                    if (is_array($selIds)) {
                        foreach ($selIds as $sid) {
                            $sId = (int)$sid;
                            if ($sId > 0 && !$examSubjects->contains('id', $sId)) {
                                $examSubjects->push((object)[
                                    'id' => $sId,
                                    'name' => null,
                                ]);
                            }
                        }
                    }

                    // 3. Check single subject_id
                    if (!empty($exam->subject_id) && !$examSubjects->contains('id', (int)$exam->subject_id)) {
                        $examSubjects->push((object)[
                            'id' => (int)$exam->subject_id,
                            'name' => null,
                        ]);
                    }

                    // Resolve names for any subjects without a human-readable title
                    $missingIds = $examSubjects->whereNull('name')->pluck('id')->all();
                    if (!empty($missingIds)) {
                        $csList = \App\Models\CourseSubject::whereIn('id', $missingIds)->get();
                        $subList = Subject::whereIn('id', $missingIds)->whereNull('deleted_at')->get();
                        $nameMap = [];
                        foreach ($csList as $cs) {
                            $nameMap[$cs->id] = $cs->name ?? ($cs->subject_name ?? null);
                        }
                        foreach ($subList as $s) {
                            $nameMap[$s->id] = $s->name ?? ($s->subject_name ?? null);
                        }
                        foreach ($examSubjects as &$es) {
                            if (empty($es->name) && isset($nameMap[$es->id])) {
                                $es->name = $nameMap[$es->id];
                            }
                            if (empty($es->name)) {
                                $es->name = 'Subject #' . $es->id;
                            }
                        }
                        unset($es);
                    }

                    if ($examSubjects->isNotEmpty()) {
                        $examSubjects = $examSubjects->unique('id')->values();
                        if ($request->wantsJson() || $request->header('Accept') === 'application/json' || $request->input('format') === 'json') {
                            return response()->json($examSubjects);
                        }
                        $subjectData = '';
                        foreach($examSubjects as $item){
                            $subjectData .= '<option value="'.$item->id.'" selected>'.htmlspecialchars($item->name).'</option>';
                        }
                        return response($subjectData);
                    }
                }
            }

            $subject = Subject::where('class_type_id', $id)->whereNull('deleted_at');
            if(Session::get('role_id') == 2)
            {
                $checkClassTeacher= null;
                $subjects = TeacherSubject::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('teacher_id',Session::get('teacher_id'))->where('class_type_id', $id)->groupBy('subject_id')->get();
                if(empty($checkClassTeacher))
                {
                    if(!empty($subjects))
                    {
                        $att = array();
                        foreach($subjects as $item)
                        {
                            $att[] = $item->subject_id;
                        }
                        $subject =$subject->whereIn('id',$att);
                    }
                }
            }
          
            $subject = $subject->orderBy('sort_by', 'ASC')->orderBy('name', 'ASC')->get();
            if ($subject->isEmpty()) {
                $csQuery = \App\Models\CourseSubject::where('course_id', $id);
                $subject = $csQuery->orderBy('sort_by', 'ASC')->orderBy('subject_name', 'ASC')->get();
            }
         
            if ($request->wantsJson() || $request->header('Accept') === 'application/json' || $request->input('format') === 'json') {
                return response()->json($subject);
            }

            $subjectData ='<option value="">Select</option>';
            foreach($subject as $item){
                $subName = $item->name ?? ($item->subject_name ?? ('Subject #' . $item->id));
                $subjectData.='
                <option value="'.$item->id.'">'.htmlspecialchars($subName).'</option>';
            }
            return response($subjectData);
        } 
        return response()->json([]);
    }  
    public function sectionDataId(Request $request){
         
             if(!empty($request->sessionData)){
            $data = Sessions::find($request->sessionData);
           Session::put('session_id', $data['id']);
           
           return back()->with('message', 'Session Changed Successfully.');
           }else{
              return back()->with('error', 'Session Not Changed ! ');
           }
 
}

    public function calendarElement(Request $request){
        return view('dashboard.calendar');
    }
 
}
