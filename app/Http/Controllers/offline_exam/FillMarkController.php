<?php

namespace App\Http\Controllers\offline_exam;
use Illuminate\Validation\Validator;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\exam\AssignExam;
use App\Models\exam\FillMarksByExcel;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\FillMarks;
use App\Models\Admission;
use App\Models\exam\ExaminationSchedule;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\exam\ExaminationScheduleDetail;
use App\Models\examoffline\PerformanceMarks;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassType;
use App\Models\Master\TeacherSubject;
use Session;
use Helper;
use Str;
use Redirect;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;
class FillMarkController extends Controller
{


     public function fillMarksByExcel(Request $request){
        
       // dd($request);
        $search["name"] = "";
        $search["class_type_id"] = $request->class_type_id;
        $search["class_type_id"] = $request->class_name;
        $search["exam_id"] = $request->exam_id ?? "";

        if ($request->isMethod("post")) {
            
            $the_file = $request->file('excel');
            
            try {
                $spreadsheet = IOFactory::load($the_file->getRealPath());
            
                $sheet        = $spreadsheet->getActiveSheet();
                $row_limit    = $sheet->getHighestDataRow();
                $column_limit = $sheet->getHighestDataColumn();
                $row_range    = range(1, $row_limit);
                $column_range = range('F', $column_limit);
                $startcount = 2;
                $data = array();
                
                
                $searchString = "Total";
$columnIndex = -1; 
$subjects =[];
$columns =[];

for ($col = 1; $col <= $row_range; $col++) {
    $cellValue = $sheet->getCellByColumnAndRow($col, 1)->getValue();
    
    if ($cellValue === $searchString) {
        $columnIndex = $col; 
        break; 
    }
    if($col>7)
        {
        $subjects[] =$cellValue;
        $columns[]=$col;
        }
    }
    //dd($subjects);
    $subject_ids_array = [];
    foreach($subjects as $item)
    {
    $subject_ids = Subject::where('class_type_id', $request->class_name)->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($item) . '%'])->first('id');
    
    if(!empty($subject_ids))
    {
        $subject_ids_array[] =$subject_ids->id;
    }

    }
    
    //dd($subjects);
                foreach ($row_range as $key => $row) {
         
                   if($key >0)
{
    $bytes = random_bytes(5);
     foreach ($subject_ids_array as $index => $ids) {
                                $subject_name = Subject::find($ids);

                            
                            $fillMarkByExcel = new FillMarksByExcel(); //model name
                            
                            $fillMarkByExcel->user_id = Session::get("id");
                            $fillMarkByExcel->session_id = Session::get("session_id");
                            $fillMarkByExcel->branch_id = Session::get("branch_id");
                            $fillMarkByExcel->exam_id = $request->exam_id;
                            $fillMarkByExcel->class_type_id = $request->class_name;
                            $fillMarkByExcel->unique_token = bin2hex($bytes);
                            $fillMarkByExcel->subject_id = $ids;
                            $fillMarkByExcel->subject_name = $subject_name->name;
                            $fillMarkByExcel->test_rank = (!empty($sheet->getCellByColumnAndRow(($columnIndex+1), $row)->getValue())) ? $sheet->getCellByColumnAndRow(($columnIndex+1), $row)->getValue() : '' ;
                            $fillMarkByExcel->group_rank = (!empty($sheet->getCellByColumnAndRow(($columnIndex+2), $row)->getValue())) ? $sheet->getCellByColumnAndRow(($columnIndex+2), $row)->getValue() : '' ;
                            $fillMarkByExcel->percentage = (!empty($sheet->getCellByColumnAndRow(($columnIndex+3), $row)->getValue())) ? $sheet->getCellByColumnAndRow(($columnIndex+3), $row)->getValue() : '' ;
                            $fillMarkByExcel->total = (!empty($sheet->getCellByColumnAndRow(($columnIndex+0), $row)->getValue())) ? $sheet->getCellByColumnAndRow(($columnIndex+0), $row)->getValue() : '' ;
                            
                            
                            $fillMarkByExcel->admission_id = (!empty($sheet->getCell('A' . $row)->getValue())) ? $sheet->getCell('A' . $row)->getValue() : '';
                           
                            $fillMarkByExcel->student_marks = (!empty($sheet->getCellByColumnAndRow($columns[$index], $row)->getValue())) ? $sheet->getCellByColumnAndRow($columns[$index], $row)->getValue() : '' ;
                            $fillMarkByExcel->save();
                }
                
                $data = FillMarksByExcel::where('unique_token',bin2hex($bytes))->get();
                $admissions = Admission::where('admissionNo',$sheet->getCell('A' . $row)->getValue())->first();
                if(!empty($admissions)){
                
                     $template =  MessageTemplate::Select('message_templates.*','message_types.slug')
                            ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                           ->where('message_types.status',1)->where('message_types.slug','exam-result')->first();
            
            $branch = Branch::find(Session::get('branch_id'));
            $setting = Setting::where('branch_id',Session::get('branch_id'))->first();
            
            $sube = ''; // Initialize $sube to store accumulated data
$total = 0;
foreach($data as $da) {
    $sube   .= $da->subject_name . ':' . $da->student_marks . PHP_EOL; // Use PHP_EOL for line breaks
    $total =$da->total;
}
//dd($sube);

if (isset($data[0])) {
  
        $arrey1 =   array(
                        '{#name#}',
                        '{#school_name#}',
                        '{#support_no#}',
                        '{#sub#}',
                        '{#total#}',
                        '{#test_rank#}',
                        '{#date#}'
                        );
                       
        $arrey2 = array(
                        $admissions->first_name." ".$admissions->last_name,
                        $setting->name,
                        $setting->mobile,
                        $sube,
                        $total.'/'.$setting->total,
                        $data[0]->test_rank . ' out of'.$setting->rank,
                        $setting->test_date
                        );
                        
             // 480      
                    if($template->status != 1){
                            if($branch->whatsapp_srvc != 0){
                                if ($admissions->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        //dd($whatsapp);
                                        Helper::sendWhatsappMessage($admissions->mobile,$whatsapp);
                                        //dd('as');
                                       FillMarksByExcel::where('unique_token',bin2hex($bytes))->update(['message'=>1]);
                                    }
                                }
                            }
                    }
}
                }   
}
                }
            } catch (Exception $e) {
                $error_code = $e->errorInfo[1];
                return redirect('fill_marks_by_excel')->with('error', 'Error in Marks Fill !');
            }
           
            return redirect('fill_marks_by_excel')->with('message', 'Marks Filled Successfully !');            

        }
        return view("examination.offline_exam.fill_mark.fillMarksByExcel", [
            "search" => $search,
        ]);
    }
    public function fillMarks(Request $request)
    {
        $requestedClassId = $request->class_name ?: $request->class_type_id;
        $search["class_type_id"] = $requestedClassId;
        $search["subject_name_id"] = array_values(array_filter((array) $request->input('subject_name_id', [])));
        $search["exam_id"] = $request->exam_id ?? "";
        $search["subject_id"] = $request->subject_id ?? "";
        $showAllSubjects = (int) $request->input('show_all_subjects', 0) === 1;
        $selectedSubjectIds = $search["subject_name_id"];
        $showMarksSection = $showAllSubjects || !empty($selectedSubjectIds);

        $subjects = collect();
        $data2 = collect();
        $marks = "";

        if ($request->isMethod("post") || (!empty($requestedClassId) && !empty($request->exam_id))) {
            $request->validate([]);
            $classOrderBy = ClassType::where('id',$requestedClassId)->where("branch_id", Session::get("branch_id"))->first('orderBy');
            $subjects = Subject::where("class_type_id", $requestedClassId)->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC");
                        $Allsubjects = Subject::where("class_type_id", $requestedClassId)->where("branch_id", Session::get("branch_id"))->orderBy("sort_by", "ASC");
                $selectedSubjectIds = array_values(array_filter((array) $request->input('subject_name_id', [])));
                if (!empty($selectedSubjectIds)) {
                $subjects->whereIn('id', $selectedSubjectIds);
                }
                      if(Session::get('role_id') == 2)
            {
                  $checkClassTeacher= Teacher::where('id',Session::get('teacher_id'))->where('class_type_id',$requestedClassId)->first();
                  
                   if(empty($checkClassTeacher))
              {
                 $classes = TeacherSubject::where('teacher_id',Session::get('teacher_id'))->where("branch_id", Session::get("branch_id"))->where('class_type_id',$requestedClassId)->groupBy('subject_id')->get();
                
              if(!empty($classes))
              {
                   $att = array();
                  foreach($classes as $item)
                  {
                      $att[] = $item->subject_id;
                  }
                  
                  $subjects =$subjects->whereIn('id',$att);
                  $Allsubjects =$Allsubjects->whereIn('id',$att);
              }
                  }
            }
            if ($showMarksSection) {
                $subjects = $subjects->get();
            } else {
                $subjects = collect();
            }
            $Allsubjects = $Allsubjects->get();
            if ($showAllSubjects && empty($selectedSubjectIds)) {
                $selectedSubjectIds = $Allsubjects->pluck('id')->map(function ($id) {
                    return (int) $id;
                })->values()->all();
                $search['subject_name_id'] = $selectedSubjectIds;
                $showMarksSection = true;
            }
            $students = Admission::where("class_type_id", $requestedClassId)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)
                ->orderBy("first_name", "ASC")
                ->get();

            $studentSubjectAssignments = [];
            if (($classOrderBy->orderBy ?? 0) > 10) {
                $storedSubjectIds = $students->flatMap(function ($student) {
                    return collect(explode(',', (string) $student->stream_subject))
                        ->map(function ($id) {
                            return (int) trim($id);
                        })
                        ->filter();
                })->unique()->values();

                $storedSubjectNames = Subject::withTrashed()
                    ->whereIn('id', $storedSubjectIds)
                    ->get(['id', 'name'])
                    ->keyBy('id');

                $currentSubjectsByName = $Allsubjects->groupBy(function ($subject) {
                    return strtolower(trim(preg_replace('/\s+/', ' ', (string) $subject->name)));
                });

                foreach ($students as $student) {
                    $resolvedIds = collect(explode(',', (string) $student->stream_subject))
                        ->map(function ($id) {
                            return (int) trim($id);
                        })
                        ->filter()
                        ->flatMap(function ($storedId) use ($storedSubjectNames, $currentSubjectsByName, $Allsubjects) {
                            if ($Allsubjects->contains('id', $storedId)) {
                                return [$storedId];
                            }

                            $storedSubject = $storedSubjectNames->get($storedId);
                            if (!$storedSubject) {
                                return [];
                            }

                            $normalizedName = strtolower(trim(preg_replace('/\s+/', ' ', (string) $storedSubject->name)));
                            return $currentSubjectsByName->get($normalizedName, collect())->pluck('id')->all();
                        })
                        ->map(function ($id) {
                            return (int) $id;
                        })
                        ->unique()
                        ->values()
                        ->all();

                    $studentSubjectAssignments[$student->id] = array_fill_keys($resolvedIds, true);
                }
            }

            $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $requestedClassId)
                ->where('assign_exams.session_id',Session::get("session_id"))
                 ->where('exam.deleted_at',null)->where("assign_exams.branch_id", Session::get("branch_id"))->orderBy('exam.id','ASC')
                ->get();

            $fillMinMaxMarksMap = DB::table('fill_min_max_marks')
                ->where('exam_id', $search['exam_id'] ?? '')
                ->where('class_type_id', $requestedClassId)
                ->where('branch_id', Session::get('branch_id'))
                ->where('session_id', Session::get('session_id'))
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('subject_id');

            $existingMarksMap = collect();
            if ($students->isNotEmpty()) {
                $existingMarksMap = DB::table('fill_marks')
                    ->where('exam_id', $search['exam_id'] ?? '')
                    ->whereIn('admission_id', $students->pluck('id'))
                    ->where('branch_id', Session::get('branch_id'))
                    ->where('session_id', Session::get('session_id'))
                    ->whereNull('deleted_at')
                    ->get()
                    ->keyBy(function($item) {
                        return $item->admission_id . '_' . $item->subject_id;
                    });
            }

            $isPublished = DB::table('exam_result_publications')
                ->where('exam_id', $search['exam_id'] ?? '')
                ->where('class_type_id', $requestedClassId)
                ->whereNotNull('published_at')
                ->whereDate('published_at', '<=', now()->toDateString())
                ->exists();

            return Helper::view("examination.offline_exam.fill_mark.fill_marks", [
                "subjects" => $subjects,
                "Allsubjects" => $Allsubjects,
                "data2" => $students,
                "search" => $search,
                "examlist" => $examlist,
                "classOrderBy" => $classOrderBy->orderBy ?? '',
                "selectedSubjectIds" => $selectedSubjectIds,
                "studentSubjectAssignments" => $studentSubjectAssignments,
                "showMarksSection" => $showMarksSection,
                "fillMinMaxMarksMap" => $fillMinMaxMarksMap,
                "existingMarksMap" => $existingMarksMap,
                "isPublished" => $isPublished,
            ]);
        }
        return Helper::view("examination.offline_exam.fill_mark.fill_marks", [
            "search" => $search,
            "showMarksSection" => $showMarksSection,
        ]);
    }

    public function fillMarksSubmit(Request $request)
    {
        
        if ($request->isMethod("post")) {
            $request->validate([
                'class_type_id' => 'required|integer',
                'exam_id' => 'required|integer',
                'subject_id' => 'required|array|min:1',
                'subject_id.*' => 'required|integer|distinct',
                'exam_maximum_marks' => 'required|array',
                'exam_maximum_marks.*' => 'required|numeric|min:0',
                'exam_minimum_marks' => 'required|array',
                'exam_minimum_marks.*' => 'required|numeric|min:0',
                'admission_id' => 'required|array|min:1',
                'admission_id.*' => 'required|integer|distinct',
                'subject_id_fill' => 'required|array|min:1',
                'subject_id_fill.*' => 'required|integer',
                'student_marks' => 'required|array',
                'fill_marks_id' => 'required|array',
                'r_marks' => 'nullable|array',
                'r_marks.*' => 'nullable|string|max:20',
                'w_marks' => 'nullable|array',
                'w_marks.*' => 'nullable|string|max:20',
                'l_marks' => 'nullable|array',
                'l_marks.*' => 'nullable|string|max:20',
            ]);

            if (!empty($request->subject_id)) {
                for ($i = 0; $i < count($request->subject_id); $i++) {
                    $add = FillMinMaxMarks::withTrashed()
                        ->where('exam_id', $request->exam_id)
                        ->where('class_type_id', $request->class_type_id)
                        ->where('subject_id', $request->subject_id[$i])
                        ->where('session_id', Session::get("session_id"))
                        ->where('branch_id', Session::get("branch_id"))
                        ->first();
                    $add = $add ?: new FillMinMaxMarks();
                    $add->class_type_id = $request->class_type_id;
                    $add->exam_id = $request->exam_id;
                    $add->user_id = Session::get("id");
                    $add->session_id = Session::get("session_id");
                    $add->branch_id = Session::get("branch_id");
                    $add->subject_id = $request->subject_id[$i];
                    $add->exam_minimum_marks = $request->exam_minimum_marks[$i];
                    $add->exam_maximum_marks = $request->exam_maximum_marks[$i];
                    $add->deleted_at = null;
                    $add->save();
                }
            }
            $count = 0;
            if (!empty($request->admission_id)) {
                for ($i = 0; $i < count($request->admission_id); $i++) {
                    for ($j = 0; $j < count($request->subject_id); $j++) {
                        $fillMarksId = $request->fill_marks_id[$count] ?? null;
                        $subjectId = $request->subject_id_fill[$count] ?? null;

                        $add1 = !empty($fillMarksId)
                            ? FillMarks::withTrashed()->find($fillMarksId)
                            : null;

                        // A repeated/stale form submission can contain blank hidden IDs.
                        // Fall back to the record's natural key instead of inserting again.
                        if (!$add1) {
                            $add1 = FillMarks::withTrashed()
                                ->where('exam_id', $request->exam_id)
                                ->where('class_type_id', $request->class_type_id)
                                ->where('admission_id', $request->admission_id[$i])
                                ->where('subject_id', $subjectId)
                                ->where('session_id', Session::get('session_id'))
                                ->where('branch_id', Session::get('branch_id'))
                                ->first();
                        }

                        $add1 = $add1 ?: new FillMarks();

                        $exam_maximum_marks = FillMinMaxMarks::where('exam_id', $request->exam_id)
                            ->where('class_type_id', $request->class_type_id)
                            ->where('subject_id', $subjectId)
                            ->where('session_id', Session::get('session_id'))
                            ->where('branch_id', Session::get('branch_id'))
                            ->whereNull('deleted_at')
                            ->first();
                      
                        $add1->exam_id = $request->exam_id;
                        $add1->class_type_id = $request->class_type_id;
                        $add1->admission_id = $request->admission_id[$i];
                        $add1->user_id = Session::get("id");
                        $add1->session_id = Session::get("session_id");
                        $add1->branch_id = Session::get("branch_id");
                        $add1->subject_id = $subjectId;
                        $add1->student_marks = $request->student_marks[$count];
                        $add1->r_marks = $request->r_marks[$count] ?? null;
                        $add1->w_marks = $request->w_marks[$count] ?? null;
                        $add1->l_marks = $request->l_marks[$count] ?? null;
                        $add1->fill_min_max_marks_id = $exam_maximum_marks->id ?? null;
                        $add1->exam_maximum_marks = $exam_maximum_marks->exam_maximum_marks ?? '';
                        $add1->deleted_at = null;
                        $add1->save();
                        $count++;
                    }
                }
            }
            return redirect::to("fill_marks")->with(
                "message",
                "Marks Updated Successfully."
            );
        }
    }

    public function download_marksheet(Request $request)
    {
        $search["name"] = "";
        $search["class_type_id"] = $request->class_name;
        $search["exam_id"] = $request->exam_id ?? "";
        if ($request->isMethod("post")) {
            $students = Admission::where("class_type_id", $request->class_name)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->orderBy("first_name", "ASC")
                ->get();
            $examlist = AssignExam::select(
                "assign_exams.*",
                "exam.id as exam_id",
                "exam.name as exam_name"
            )
                ->leftjoin("exams as exam", "assign_exams.exam_id", "exam.id")
                ->where("assign_exams.class_type_id", $request->class_name)
                ->get();

            return view(
                "examination.offline_exam.download_marksheet.download_marksheet",
                [
                    "data" => $students,
                    "search" => $search,
                    "examlist" => $examlist,
                ]
            );
        }
        return view(
            "examination.offline_exam.download_marksheet.download_marksheet",
            ["search" => $search]
        );
    }
      public function printReportCard(Request $request){
        if($request->isMethod('post')){
             $student = Admission::where("id", $request->admission_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->first();
         $subjects = Subject::where("class_type_id", $request->class_type_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->orderBy("name", "ASC")
                ->get();
           return view('examination.offline_exam.download_marksheet.download_pdf',['data'=>$student,'subjects'=>$subjects,'exam_id'=>$request->exam_id]);
        }
    }
    
       public function bulk_marksheet(Request $request){
          // dd($request);
 $search['class_type_id'] = $request->class_type_id ?? '';
 Session()->has('result_date',null);

  if($request->isMethod('post')){
        $student_list= Admission::where('class_type_id',$request->class_type_id)
         ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
        ->where('status',1)->get();
             $subjects = Subject::where("class_type_id", $request->class_type_id)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
                ->orderBy("sort_by", "ASC")
                ->get();
               
          $exam =  AssignExam::select('assign_exams.*','exam.name as exam_name','exam.id as exam_id')
    	    ->leftjoin('exams as exam','exam.id','assign_exams.exam_id')->
    	    where('exam.deleted_at',null)->
    	    where('assign_exams.class_type_id',$request->class_type_id)->where("assign_exams.branch_id", Session::get("branch_id"))->where('assign_exams.session_id',Session::get('session_id'))->groupBy('assign_exams.exam_id')
    	    ->orderBy('exam.id','ASC')->get();
      
     return view('examination.offline_exam.bulk_marksheet.bulk_marksheet',['subject'=>$subjects,'exam'=>$exam ,'search'=>$search,'student_list'=>$student_list]);
  }
        return view('examination.offline_exam.bulk_marksheet.bulk_marksheet',['search'=>$search]);
    }
      public function bulk_marksheet_generate(Request $request){
 //dd($request);
 $search['class_type_id'] = $request->class_type_id ?? '';
 $subject ='';
 $exam ='';
  if($request->isMethod('post')){
              Session::put('result_date', $request->result_date); 
//dd(Session::get('result_date'));
        $classOrderBy = ClassType::where('id',$request->class_type_id)->where("branch_id", Session::get("branch_id"))->first('orderBy');
      $admission_id = Admission::where('class_type_id',$request->class_type_id)
        ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))
      ->where('status',1);
          if($request->single_student != '')
            {
               
          $admission_id  = $admission_id ->whereIn('id',$request->single_student);
            }
            
          $admission_id =$admission_id->get();
         $a = $request->exam_array;
         $b = $request->subject_array;
         $exam_id ;
         $subject_id ;
         $subject_id_exculded=[] ;
         $other_subject_id=[] ;
         $count=0;
         $count1=0;
        
        foreach($a as $key=>$item)
        {
            $count++;
            $exam_id[] =array_search($count, $a);
        }
      //  dd($b);
        foreach($b as $key=>$item)
        {
            //Subject::where('id',$key)->update(['sort_by'=>$item]);
         
            $count1++;
            $subject_id[] =array_search($count1, $b);
        }
  
        
        foreach($exam_id as $key=>$item)
        {
        $list_exam[$key] = Exam::where('id',$item)->first();
        }
        foreach($subject_id as $key=>$item)
        {
        $list_subject[$key] = Subject::select('subject.*')->where('subject.id',$item)->first();
        $value= Subject::select('subject.*')->where('subject.id',$item)->first();
        
        if(!empty($value))
        {
            if($value->other_subject == 0){
                            $subject_id_exculded[] = $value->id;

            }else{
                 $other_subject_id[] = $value->id;
            }
        }
        }
      
 
 //  dd($list_exam);
      
         if($classOrderBy->orderBy > 8){

            return view('print_file.exam.marksheet_1', ['classOrderBy'=>$classOrderBy->orderBy,'exam_id'=>$exam_id,'other_subject_id'=>$other_subject_id,'subject_id'=>$subject_id_exculded,'admission_id'=>$admission_id,'list_subject'=>$list_subject,'subject'=>$subject,'exam'=>$exam,'list_exam'=>$list_exam]);     

         }else{

        return view('print_file.exam.marksheet_1', ['classOrderBy'=>$classOrderBy->orderBy,'exam_id'=>$exam_id,'subject_id'=>$subject_id_exculded,'other_subject_id'=>$other_subject_id,'admission_id'=>$admission_id,'list_subject'=>$list_subject,'subject'=>$subject,'exam'=>$exam,'list_exam'=>$list_exam]);     

         }
       

   
     }
    }
    
    
    
     public function performanceMarksSubmit(Request $request)
    {
        
        if ($request->isMethod("post")) {
      
     
            $count = 0;
            if (!empty($request->admission_id)) {
                for ($i = 0; $i < count($request->admission_id); $i++) {
                    for ($j = 0; $j < count($request->subject_id); $j++) {
                        if (!empty($request->performance_marks_id)) {
                            
                            if($request->performance_marks_id[$count] != '')
                            {
                                  $add1 = PerformanceMarks::find(
                                $request->performance_marks_id[$count]
                            );
                            }
                            else
                            {
                                 $add1 = new PerformanceMarks();
                            }
                        } 
                        $add1->term_id = $request->term_id;
                        $add1->class_type_id = $request->class_type_id;
                        $add1->admission_id = $request->admission_id[$i];
                        $add1->user_id = Session::get("id");
                        $add1->session_id = Session::get("session_id");
                        $add1->branch_id = Session::get("branch_id");
                        if (isset($request->performance[$request->subject_id_fill[$count]])) {
    $add1->performance = $request->performance[$request->subject_id_fill[$count]] == 1 ? 1 : 0;
} else {
    $add1->performance = 0; 
}
                     
                        $add1->subject_id = $request->subject_id_fill[$count];
                        $add1->student_marks = $request->student_marks[$count];
                        
                        if($request->check_null[$count] != null)
                        {
                             $add1->save();
                        }
                        else{
                             if($request->student_marks[$count] != '')
                        {
                        $add1->save();
                        }
                        }
                        $count++;
                    }
                }
            }
            return redirect::to("performance_marks")->with(
                "message",
                "Marks Updated Successfully."
            );
        }
    }
    
    public function performanceMarks(Request $request)
    {
        
       
        $search["class_type_id"] = $request->class_name;
        $search["term_id"] = $request->term_id ?? "";

        $data1 = "";
        $data2 = "";
        $marks = "";

        if ($request->isMethod("post")) {
         
            $request->validate([]);

            $subjects = Subject::where("class_type_id", $request->class_name)
                ->where("branch_id", Session::get("branch_id"))
              
                ->orderBy("name", "ASC")
                ->get();
            $students = Admission::where("class_type_id", $request->class_name)
                ->where("session_id", Session::get("session_id"))
                ->where("branch_id", Session::get("branch_id"))->where('status',1)
                ->orderBy("first_name", "ASC")
                ->get();

           

            return view("examination.offline_exam.fill_mark.performance_marks", [
                "data1" => $subjects,
                "data2" => $students,
                "search" => $search,
            ]);
        }
        return view("examination.offline_exam.fill_mark.performance_marks", [
            "search" => $search,
        ]);
    }
   
}
