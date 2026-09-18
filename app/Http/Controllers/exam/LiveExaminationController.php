<?php

namespace App\Http\Controllers\exam;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Admission;
use App\Models\exam\digital\QuestionDigital;
use App\Models\exam\digital\ExamDigital;
use App\Models\exam\digital\ExamSettingDigital;
use App\Models\StudentGrow;
use App\Models\Subject;
use App\Models\exam\digital\ChapterDigital;
use App\Models\exam\digital\TopicDigital;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Setting;
use App\Models\ClassType;
use App\Models\Classs;
use App\Models\Student;
use App\Models\Master\MessageTemplate;
use App\Models\Master\MessageType;
use App\Models\Master\Branch;
use App\Models\AdmitCardNote;
use App\Models\ExaminationAdmitCard;
use App\Models\ExaminationSchedule;
use App\Models\ExaminationScheduleDetail;
use App\Models\exam\digital\ExamResultDigital;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\FillMarks;
use App\Models\exam\ExamResultDetail;
use App\Models\exam\AssignQuestion;
use App\Models\exam\AssignExam;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpWord\IOFactory;
use Helper;
use Session;
use Hash;
use PDF;
use Str;
use Redirect;
use Response;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LiveExaminationController extends Controller

{
    public function examChapterData(Request $request){

        if($request->isMethod('post')){
            /*if($request->class_type_id == 3){
                $chapterData = ChapterDigital::with('ClassTypes')->with('Subject')->whereIn('class_type_id', ['1', '2'])->whereIn('subject_id', $request->subject_id)->get();
            }else{
                $chapterData = ChapterDigital::with('ClassTypes')->with('Subject')->where('class_type_id', $request->class_type_id)->where('subject_id', $request->subject_id)->get();
            }*/
            $requestSubjectId = (array) $request->subject_id;
           
            $subjectData = Subject::with('ClassTypes')->whereIn('id', $requestSubjectId)->get();
            
            return view('examination.digital.exam.examChapterData', ['subjectData' => $subjectData]);
        }
        
    }
    
    public function examTopicData(Request $request){

        if($request->isMethod('post')){
            /*if($request->class_type_id == 3){
                $topicData = TopicDigital::with('ClassTypes')->with('Subject')->with('Chapter')->whereIn('class_type_id', ['1', '2'])->where('subject_id', $request->subject_id)->get();
            }else{
                $topicData = TopicDigital::with('ClassTypes')->with('Subject')->with('Chapter')->where('class_type_id', $request->class_type_id)->where('subject_id', $request->subject_id)->get();
            }*/
            $chapterData = ChapterDigital::whereIn('id', $request->chapter_id)->get();
            
            return view('examination.exam.examTopicData', ['chapterData' => $chapterData]);
        }
        
    }
    
    public function checkQuestionAvailability(Request $request){
        
        $questionCount = QuestionDigital::all()->count();
        $requestQuesCount = $request->totalQuesSum;
        //status => 0 = Sufficiant Question Count, 1 = Not Sufficiant Question Count
        if($questionCount < $requestQuesCount){
            return response()->json(['status' => 1]);
        }else{
           return response()->json(['status' => 0]);
        }
        
    }
    
    public function checkQuestionCount(Request $request){

 $old_data_chapter_id ='';
 $old_data_subject_id ='';
 $final_exam_questions_id ='';
 if(!empty($request->class_type_id))
 {
     
     if(!empty($request->pattern_id))
 {
     
     $old_data_chapter_id = ExamDigital::where('class_type_id',$request->class_type_id)->where('pattern_id',$request->pattern_id)->pluck('chapter_id')->implode(',');
 $old_data_subject_id = ExamDigital::where('class_type_id',$request->class_type_id)->where('pattern_id',$request->pattern_id)->pluck('subject_id')->implode(',');
 $final_exam_questions_id = ExamDigital::where('class_type_id',$request->class_type_id)->where('pattern_id',$request->pattern_id)->where('exam_type_id',3)->pluck('questions_id')->implode(',');

 }
    
      }
 $old_chapter_ids = explode(',', trim($old_data_chapter_id,',')) ;
 $old_subject_ids = explode(',', trim($old_data_subject_id,',')) ;
 //$final_exam_questions_id = explode(',', trim($final_exam_questions_id,',')) ;
 $final_exam_questions_id= array_unique(str_getcsv(trim($final_exam_questions_id,','))) ;


            $modified_questions[0] = [];
            $modified_questions[1] = [];
            $modified_questions[2] = [];
            $modified_questions[3] = [];
            
            // $questionCountObjective = $request->questionCountObjective;
            $questionCount = $request->questionCount;
            $questionTypeId = $request->questionTypeId;
            $sukas = $request->sukas;
            $suka_count = 0;
        
        if($request->suka_status == 'active')
        {
              foreach($sukas as $key =>$item )
       {
           if($item > 0)
           {
           $suka_count = $suka_count+$item;
            $modified_questions[$key] =  QuestionDigital::where($request->topic_status, $request->topic_status == 'chapter_id' ? $request->chapter_id : $request->topic_id)
          ->where('suka_id',$key+1)->where('question_type_id',$questionTypeId);
          
          if($request->level_id != 0)
          {
          $modified_questions[$key] =  $modified_questions[$key]->where('level_id',$request->level_id ?? '');
          }
          if($request->chapter_wise != 'on')
          {
              if(!empty($old_chapter_ids[0]))
              {
                 
          $modified_questions[$key] =  $modified_questions[$key]->whereNotIn('chapter_id',$old_chapter_ids);
          }
          }
          if($request->subject_wise != 'on')
          {
              if(!empty($old_subject_ids[0]))
              {
                 
                 
          $modified_questions[$key] =  $modified_questions[$key]->whereNotIn('subject_id',$old_subject_ids);
          }
          }
          if($request->final_test != 'on')
          {
              if(!empty($final_exam_questions_id[0]))
              {
                 
                 
          $modified_questions[$key] =  $modified_questions[$key]->whereNotIn('id',$final_exam_questions_id);
          }
          }
           $modified_questions[$key] =  $modified_questions[$key]->inRandomOrder()->limit($suka_count)->distinct()->get();
          if(count($modified_questions[$key]) > 0)
          {
          if(count($modified_questions[$key]) != $item )
          {
              $suka_count = $suka_count - count($modified_questions[$key]);
          }
          else
          {
              $suka_count = 0;
          }
          }
          else
          {
              $q_type = '';
              if($key==0){ $q_type ='Skill';}
              if($key==1){ $q_type ='Understanding';}
              if($key==2){ $q_type ='Knowledge';}
              if($key==3){ $q_type ='Application';}
            
               return response()->json(['status' => 1,'error' => 'No questions in Percentage of Question Type '.$q_type]);
          }
           }
       }
       
        }
        else
        {
             foreach($sukas as $key =>$item )
       {
           if($item > 0)
           {
           $suka_count = $suka_count+$item;
            $modified_questions[$key] =  QuestionDigital::where($request->topic_status, $request->topic_status == 'chapter_id' ? $request->chapter_id : $request->topic_id)
         ->where('question_type_id',$questionTypeId);
         
         
      if($request->level_id != 0)
          {
          $modified_questions[$key] =  $modified_questions[$key]->where('level_id',$request->level_id ?? '');
          }
            if($request->chapter_wise != 'on')
          {
              if(!empty($old_chapter_ids))
              {
                  
          $modified_questions[$key] =  $modified_questions[$key]  ->whereNotIn('chapter_id',$old_chapter_ids );
          }
          }
               if($request->subject_wise != 'on')
          {
              if(!empty($old_subject_ids))
              {
                 
                 
          $modified_questions[$key] =  $modified_questions[$key]->whereNotIn('subject_id',$old_subject_ids );
          }
          }
          if($request->final_test != 'on')
          {
              if(!empty($final_exam_questions_id))
              {
                 
                 
          $modified_questions[$key] =  $modified_questions[$key]->whereNotIn('id',$final_exam_questions_id );
          }
          }
        $modified_questions[$key] =  $modified_questions[$key]->inRandomOrder()->limit($suka_count)->distinct()->get();
          
          if(count($modified_questions[$key]) != $item )
          {
              $suka_count = $suka_count - count($modified_questions[$key]);
          }
          else
          {
              $suka_count = 0;
          }
          
      
           }
       }
       //dd($modified_questions);
        }
       
          $ques_id  = '';
          $data_array  = [];
          $suka_id = [];
          $total_ = 0;
            if($request->suka_status == 'active')
        {
            $total_ = count($modified_questions);
            
        }
        else
        {
             $total_ = count($modified_questions[0]) + count($modified_questions[1])+ count($modified_questions[2])+count($modified_questions[3]);
        }
          
          if($total_ > 0)
            {
                
                $fetch_que = 0;
                foreach($modified_questions as $item)
                {
                    foreach($item as $subitem)
                    {
                        $fetch_que ++;
                        $ques_id = $ques_id.','.$subitem->id;
                        $suka_id[] = $subitem->suka_id;
                        $data_array[] = array(
                       'ques_id' => $subitem->id,
                       'suka_id' => $subitem->suka_id,
                       'chapter_id' => $subitem->chapter_id,
                       'question_type_id' => $subitem->question_type_id
                   );
                  
                     
                    }
                }
                $ques_id = trim($ques_id, ',');
                    
                    if($fetch_que >= $questionCount )
                    {
                         return response()->json(['status' => 2,'question_selected' => $ques_id, 'suka_ids' => $suka_id ,'suka_status'=>$request->suka_status ?? '','data'=>$data_array]);
                    }
                    else
                    {
                           return response()->json(['status' => 1, 'error'=> 'No sufficient questions in the table ']);
                    }
                
            }
            else
            {
                return response()->json(['status' => 1, 'error'=> 'No sufficient questions in the table ']);
            }
            
           
    }
    
    public function examinationDashboard(){
        
        return view('examination.examination_dashboard ');
    }
    
    public function viewQuestion(Request $request){
        
        $search['name'] = $request->name;
        $search['subject_id'] = $request->subject_id;
        $search['question_type_id'] = $request->question_type_id;
        
        $data = QuestionDigital::with('QuestionTypeDigital');
        
            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("name",'like','%'.$request->name.'%')->orWhere("solution",'like','%'.$request->name.'%')->orWhere("solution",'like','%'.$request->name.'%')->orWhere("ans_a",'like','%'.$request->name.'%')->orWhere("ans_b",'like','%'.$request->name.'%')->orWhere("ans_c",'like','%'.$request->name.'%')->orWhere("ans_d",'like','%'.$request->name.'%');
                }
                if (!empty($request->class_type_id)){
                    $data = $data->where("class_type_id",$request->class_type_id);
                }
                if (!empty($request->subject_id)){
                    $data = $data->where("subject_id",$request->subject_id);
                }
                if (!empty($request->chapter_id)){
                    $data = $data->where("chapter_id",$request->chapter_id);
                }    
                if (!empty($request->topic_id)){
                    $data = $data->where("topic_id",$request->topic_id);
                }
                if (!empty($request->level_id)){
                    $data = $data->where("level_id",$request->level_id);
                }
                if (!empty($request->suka_id)){
                    $data = $data->where("suka_id",$request->suka_id);
                }
                if (!empty($request->question_type_id)){
                    $data = $data->where("question_type_id",$request->question_type_id);
                }
            }
            $data = $data->orderBy('id','DESC')->get();
            
        return view('examination.digital.question_bank.view',['data'=>$data,'search'=>$search]);
    }
	
    public function addQuestion(Request $request){
        if($request->isMethod('post')){

            if($request->upload_by_id == 1){
                $request->validate([
                    'docs' => 'required|mimes:docx',  
                ]);      
        
                $file = $request->file('docs');
        
                if ($file->getClientOriginalExtension() !== 'docx') {
                     return back()->withErrors(['file' => 'Invalid file type. Only DOCX files are allowed.']);
                 }
        
                $phpWord = IOFactory::load($file);
             
                $tableContents = [];
        
                $sections = $phpWord->getSections();
        
                $tableCount = 0;
                foreach ($sections as $section) {
                     
                    foreach ($section->getElements() as $element) {
                        
                        if ($element instanceof \PhpOffice\PhpWord\Element\Table) {
                           
                            $tableCount++;
        
                            $tableRows = $element->getRows();
                         
                            foreach ($tableRows as $row) {
                                $tableCells = $row->getCells();
                                
                                // Process each cell's text content
                                $rowData = [];
                                foreach ($tableCells as $cell) {
                                    $cellElements = $cell->getElements();
                                    if (!empty($cellElements[0])) {
                                        if ($cellElements[0] instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                            $rowData[] = $cellElements[0]->getText();
                                          
                                        } elseif ($cellElements[0] instanceof \PhpOffice\PhpWord\Element\Text) {
                                            $rowData[] = $cellElements[0]->getText();
                                           
                                        } elseif ($cellElements[0] instanceof \PhpOffice\PhpWord\Element\TextBreak) {
                                            
                                        } else {
                                            // Handle other element types if needed
                                        }
                 
                                    }
        
                                }
        
                                $tableContents[] = $rowData;
                                
                            }
        
                        }
                        
                    }
                    $chunkedData = array_chunk($tableContents, 14);
                    
                }
                
                foreach($chunkedData as $storeData){
                    $add = new Question;//model name
                    $add->user_id = Session::get('id');
                    $add->session_id = Session::get('session_id');
                    $add->branch_id = Session::get('branch_id');
                    
                    foreach($storeData as $key => $tableData){
        
                        
                        if($key == 0){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->name = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_name = $data;                                    
                                }
                            }                              
                        }
                        if($key == 1){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->ans_a = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_ans_a = $data;                                    
                                }
                            }                              
                        }
                        if($key == 2){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->ans_b = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_ans_b = $data;                                    
                                }
                            }                              
                        }
                        if($key == 3){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->ans_c = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_ans_c = $data;                                    
                                }
                            }                              
                        }
                        if($key == 4){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->ans_d = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_ans_d = $data;                                    
                                }
                            }                              
                        }
                        if($key == 5){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->correct_ans = $data;                                    
                                }
                            }                              
                        }
                        if($key == 6){
                            foreach($tableData as $key1 => $data){
                                if($key1 == 1){
                                    $add->solution = $data;                                    
                                }
                                if($key1 == 2){
                                    $add->hi_solution = $data;                                    
                                }
                            }                              
                        }
                        if($key == 7){
                            foreach($tableData as $key1 => $data){
                              
                                $add->class_type_id = array_key_exists(1, $tableData) ? $data : (!empty($request->class_type_id) ? $request->class_type_id : 1);                                    
                             
                            }                              
                        }
                        if($key == 8){
                            foreach($tableData as $key1 => $data){
                               
                                    $add->subject_id = array_key_exists(1, $tableData) ? $data : (!empty($request->subject_id) ? $request->subject_id : 1);                               
                               
                            }                              
                        }
                        if($key == 9){
                            foreach($tableData as $key1 => $data){
                               
                                    $add->chapter_id = array_key_exists(1, $tableData) ? $data : (!empty($request->chapter_id) ? $request->chapter_id : 1);                                   
                               
                            }                              
                        }
                        if($key == 10){
                            foreach($tableData as $key1 => $data){
                               
                                    $add->topic_id = array_key_exists(1, $tableData) ? $data : (!empty($request->topic_id) ? $request->topic_id : 1);                                 
                                
                            }                              
                        }
                        if($key == 11){
                            foreach($tableData as $key1 => $data){
                               
                                    $add->level_id = array_key_exists(1, $tableData) ? $data : (!empty($request->level_id) ? $request->level_id : 1);                                
                               
                            }                              
                        }
                        if($key == 12){
                            foreach($tableData as $key1 => $data){
                               
                                    $add->suka_id = array_key_exists(1, $tableData) ? $data : (!empty($request->suka_id) ? $request->suka_id : 1);                                  
                              
                            }                              
                        }
                        if($key == 13){
                            foreach($tableData as $key1 => $data){
                        
                                    $add->question_type_id = array_key_exists(1, $tableData) ? $data : (!empty($request->question_type_id) ? $request->question_type_id : 1);                                 
                              
                            }                              
                        }
        
                    }
                    $add->save();
                }
                return redirect()->back()->with('message', 'DOCX file imported successfully!');
                
            }else if($request->upload_by_id == 2){
                $request->validate([
                    'class_type_id'  => 'required',
                    'subject_id'  => 'required',
                    'chapter_id'  => 'required',
                    'topic_id'  => 'required',
                    'level_id'  => 'required',
                    'suka_id'  => 'required',
                    'question_type_id'  => 'required',
                ]);    
                
                for($count = 0; $count <= count($request->name); $count++){ 
                    if(isset($request->name[$count])){
                        
                        $add = new Question;//model name
                        $add->user_id = Session::get('id');
                        $add->session_id = Session::get('session_id');
                        $add->branch_id = Session::get('branch_id');
                    	$add->class_type_id = $request->class_type_id;
                    	$add->subject_id = $request->subject_id;
                    	$add->chapter_id = $request->chapter_id;
                    	$add->topic_id = $request->topic_id;
                    	$add->level_id = $request->level_id;
                    	$add->suka_id = $request->suka_id;
                    	$add->question_type_id = $request->question_type_id;
                    	$add->name = $request->name[$count];
                    	$add->hi_name = $request->hi_name[$count];
                        $add->ans_a = $request->ans_a[$count];
                        $add->ans_b = $request->ans_b[$count];
                        $add->ans_c = $request->ans_c[$count];
                        $add->ans_d = $request->ans_d[$count];
                        $add->hi_ans_a = $request->hi_ans_a[$count];
                        $add->hi_ans_b = $request->hi_ans_b[$count];
                        $add->hi_ans_c = $request->hi_ans_c[$count];
                        $add->hi_ans_d = $request->hi_ans_d[$count];
                        $add->correct_ans = $request->correct_ans[$count];
                        $add->solution = $request->solution[$count];
                        $add->hi_solution = $request->hi_solution[$count];
                        $add->save();
                        
                    }
                }
                return redirect()->back()->with('message', 'Questions added successfully!');
            }else{
                
            }
            
        }

        return view('examination.digital.question_bank.add');
    } 

    public function editQuestion(Request $request, $id){
        $data = QuestionDigital::find($id);

        if($request->isMethod('post')){
            $request->validate([

                'class_type_id'  => 'required',
                'subject_id'  => 'required',
                'chapter_id'  => 'required',
                'topic_id'  => 'required',
                'level_id'  => 'required',
                'suka_id'  => 'required',
                'question_type_id'  => 'required',
       
            ]);
        
            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
        	$data->class_type_id = $request->class_type_id;
        	$data->subject_id = $request->subject_id;
        	$data->chapter_id = $request->chapter_id;
        	$data->topic_id = $request->topic_id;
        	$data->level_id = $request->level_id;
        	$data->suka_id = $request->suka_id;
        	$data->question_type_id = $request->question_type_id;
        	$data->name = $request->name;
        	$data->hi_name = $request->hi_name;
            $data->ans_a = $request->ans_a;
            $data->ans_b = $request->ans_b;
            $data->ans_c = $request->ans_c;
            $data->ans_d = $request->ans_d;
            $data->hi_ans_a = $request->hi_ans_a;
            $data->hi_ans_b = $request->hi_ans_b;
            $data->hi_ans_c = $request->hi_ans_c;
            $data->hi_ans_d = $request->hi_ans_d;
            $data->correct_ans = $request->correct_ans;
            $data->solution = $request->solution;
            $data->hi_solution = $request->hi_solution;
            $data->save();

            return redirect::to('digital/view/question')->with('message', 'Question Updated Successfully.');
        }

        return view('examination.digital.question_bank.edit',['data'=>$data]);
    } 

    public function deleteQuestion(Request $request){
        $question = QuestionDigital::find($request->delete_id)->delete();
        return redirect::to('digital/view/question')->with('message', 'Question Deleted Successfully.');
    }

    public function addExam(Request $request){
        
        if($request->isMethod('post')){
         
            
         /*   $chapterId = [];
          //     $modified_questions = [];
               
              
            foreach($request->objectiveQuestion as $key => $objQues){
                if($objQues != null){
                    $chapterId[] = $request->chapter_id[$key];
                  //  $modified_questions[] =  QuestionDigital::whereIn('chapter_id', $chapterId)->where('question_type_id',1)->take($objQues)->get();
        
                }
        
                 
            }
          
            foreach($request->numericQuestion as $key => $numQues){
                if($numQues != null){
                    $chapterId[] = $request->chapter_id[$key];
                    // $modified_questions[] =  QuestionDigital::whereIn('chapter_id', $chapterId)->where('question_type_id',2)->take($numQues)->get();
                }
                 
            }
            
             // dd(count($modified_questions[0])+count($modified_questions[1]));
            $chapterId = array_unique($chapterId);
            
         */
          

            $setting = ExamSettingDigital::where('branch_id', Session::get('branch_id'))->pluck('exam_code')->first();
            $oldExamCount = ExamDigital::all()->count();
            
            
            /*
            if($request->topic_id){
                $question = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id);
            }else{
                $question = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId);
            }
            
            $percentageQuestionsCount = [];
            $thisPercentageQuestionsCount = '';
            $newVar = '';
            $takenQuestions = '';
            $questionCount = $request->totalQuesSum;
               
            if($request->selection_priority == 0){
                
                foreach ($request->suka_id as $key => $suka) {
                   
                    $percentageQuestionsCount[$key] = number_format($suka * $questionCount / 100);
                    if($request->level_id != 0){
                        
                        if($request->topic_id){
                            $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->where('level_id', $request->level_id)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                        }else{
                            $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->where('level_id', $request->level_id)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                        
                           // dd($thisPercentageQuestionsCount);
                        }
                        
                    }else{
                        
                        if($request->topic_id){
                            $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                        }else{
                            $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                        }
                        
                    }
                    $newVar .= $thisPercentageQuestionsCount . ',';
                    
                }

                $dataWithoutComma = rtrim($newVar, ',');

                if(count(explode(',', $dataWithoutComma)) < $request->totalQuesSum){

                    $moreQues = $request->totalQuesSum - count(explode(',', $dataWithoutComma));
                    
                    if($request->level_id != 0){
                        
                        if($request->topic_id){
                            $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->where('level_id', $request->level_id)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                        }else{
                            $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->where('level_id', $request->level_id)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                        }
                        
                    }else{
                        
                        if($request->topic_id){
                            $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                        }else{
                            $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                        }
                        
                    }
                    foreach($quesId as $que){
                        $takenQuestions = $takenQuestions . ',' . $que;
                    }
                    $takenQuestions = $dataWithoutComma . $takenQuestions; 
                    
                }else{
                    
                    if(count(explode(',', $dataWithoutComma)) == $request->totalQuesSum){
                        $takenQuestions = $dataWithoutComma;
                    }else{
                        $lessQues = count(explode(',', $dataWithoutComma)) - $request->totalQuesSum;
                        $randomKeys = array_rand(explode(',', $dataWithoutComma), $lessQues);
                        $randomKeys = is_array($randomKeys) ? $randomKeys : [$randomKeys];
                        $filteredData = array_diff_key(explode(',', $dataWithoutComma), array_flip($randomKeys));
                        $takenQuestions = implode(',', $filteredData);
                        
                    }
                    
                }
                
            }elseif($request->selection_priority == 1){
                
                if($request->level_id != 0){
                    
            
                    
                    $takenQuestions = $question->where('level_id', $request->level_id)->take($questionCount)->pluck('id')->implode(',');
                }else{
                    $takenQuestions = $question->take($questionCount)->pluck('id')->implode(',');
                }                
                
            }elseif($request->selection_priority == 2) {
                
                foreach ($request->suka_id as $key => $suka) {
                   
                    $percentageQuestionsCount[$key] = number_format($suka * $questionCount / 100);
                    if($request->topic_id){
                        $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                    }else{
                        $thisPercentageQuestionsCount = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->where('suka_id',$key)->take($percentageQuestionsCount[$key])->selectRaw('group_concat(id) as ids')->pluck('ids')->first();
                    }
                    
                    $newVar .= $thisPercentageQuestionsCount . ',';
                    
                }
                
                $dataWithoutComma = rtrim($newVar, ',');
                
                if(count(explode(',', $dataWithoutComma)) < $request->totalQuesSum){
                    
                    $moreQues = $request->totalQuesSum - count(explode(',', $dataWithoutComma));
                    if($request->topic_id){
                        $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereIn('topic_id', $request->topic_id)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                    }else{
                        $quesId = QuestionDigital::whereIn('subject_id', $request->subject_id)->whereIn('chapter_id', $chapterId)->whereNotIn('id', explode(',', $dataWithoutComma))->take($moreQues)->pluck('id');
                    }
                    
                
                    foreach($quesId as $que){
                        $takenQuestions = $takenQuestions . ',' . $que;
                    }
                    $takenQuestions = $dataWithoutComma . $takenQuestions; 
                    
                }else{
                    
                    if(count(explode(',', $dataWithoutComma)) == $request->totalQuesSum){
                        $takenQuestions = $dataWithoutComma;
                    }else{
                        
                        $lessQues = count(explode(',', $dataWithoutComma)) - $request->totalQuesSum;
                        $randomKeys = array_rand(explode(',', $dataWithoutComma), $lessQues);
                        $randomKeys = is_array($randomKeys) ? $randomKeys : [$randomKeys];
                        $filteredData = array_diff_key(explode(',', $dataWithoutComma), array_flip($randomKeys));
                        $takenQuestions = implode(',', $filteredData);

                    }
                    
                }

            }else{}*/
            
            //$takenQuestions = substr($takenQuestions, 1);
            //dd('Final = ' . $takenQuestions);
            //status => 0 = Sufficiant Question Count, 1 = Not Sufficiant Question Count After Filters
            
            // if(count(explode(',', $takenQuestions)) < $request->totalQuesSum){
                
            //     return response()->json(['status' => 1]);
                
            // }else{
                
                
                
                 $decode = json_decode($request->question_objective_ordering);
                 $decode1 = json_decode($request->question_numeric_ordering);
                 $ids = '';
                 $ids1 = '';
                 
                 
                 if(empty($request->exam_by_question_id))
                 {
                 foreach($decode as $item)
                 {
                   $ids = $ids.','.$item; 
                 }
                 foreach($decode1 as $item)
                 {
                   $ids1 = $ids1.','.$item; 
                 }
                 
                 }
                 else
                 {
                     $ids1 = $request->exam_by_question_id;
                 }
                 
                $chapter_id_in_use = QuestionDigital::whereIn('id',explode(',',trim($ids.$ids1, ',')))->distinct()->pluck('chapter_id')->implode(',');
                
               
                $add = new ExamDigital;//model name
        	    $add->user_id = Session::get('id');
        	    $add->session_id = Session::get('session_id');
                $add->branch_id = Session::get('branch_id');
        		$add->name = $request->name;
        		$add->exam_date = $request->exam_date;
        		$add->pattern_id = $request->pattern_id;
        		$add->class_type_id = $request->class_type_id;
        		$add->exam_type_id = $request->exam_type_id;
        		$add->subject_id = implode(',', $request->subject_id);
        		$add->chapter_id = $chapter_id_in_use;
        		$add->duration = $request->duration_hour;
        		$add->duration_minute = $request->duration_minute;
        		$add->level_id = $request->level_id;
        		$add->suka_id = implode(',', $request->suka_id);
        		$add->selection_priority = $request->selection_priority;
        		$add->totalQuesSum = $request->totalQuesSum;
        		$add->objectiveSum = $request->objectiveSum;
        		$add->numericSum = $request->numericSum;
        		$add->mark = $request->mark;
        		$add->objectiveMark = $request->objectiveMark;
        		$add->numericMark = $request->numericMark;
        		$add->totalQuesMark = $request->totalQuesMark;
        	    $add->exam_code = $setting . $oldExamCount += 1;
        // 		$add->questions_id = $takenQuestions;
            	$add->questions_id = trim($ids.$ids1, ',');
        	    $add->save();
        	    return response()->json(['status' => 0]);
        	    
            // }
		  
        }

        return view('examination.digital.exam.add');
    } 
    
    public function viewExam(Request $request){
        
        
        $search['name'] = $request->name;
        
        
        
        if(Session::get('role_id') == 3 || Session::get('role_id') == 2){
         
            $data = ExamDigital::select('exams_digital.*')
            ->leftjoin('assign_exams as assign_exam','exams_digital.id','assign_exam.exam_id')
            ->where('exams_digital.session_id',Session::get('session_id'))
		 ->where('exams_digital.branch_id',Session::get('branch_id'))->where('assign_exam.class_type_id',Session::get('class_type_id'));
        }else{
            $data = ExamDigital::select('exams_digital.*','class_type.name as class_name','assign_exam.class_type_id as class_id')
                ->leftjoin('assign_exams as assign_exam','exams_digital.id','assign_exam.exam_id')
                ->leftjoin('class_types as class_type','class_type.id','assign_exam.class_type_id')
            ->where('exams_digital.session_id',Session::get('session_id'))
		 ->where('exams_digital.branch_id',Session::get('branch_id'));
        }
        
            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("exams_digital.name",'like','%'.$request->name.'%');
                }
            }
            $data = $data->orderBy('id','DESC')->get();
          
          
     //    dd(Session::get('role_id'));
        $examResult = ExamResultDigital::where('admission_id',Session::get('id'))->get()->first();
       
     
      
      
        return view('examination.digital.exam.view ',['data'=>$data,'examResult'=>$examResult,'search'=>$search]);
    }

    public function viewDeletedExam(Request $request){
        $deletedExam = DB::table('exams_digital')->whereNotNull('deleted_at')->get();
        return view('examination.digital.exam.viewDeletedExam ',['data'=>$deletedExam]);
    }

    public function restoreExam(Request $request){
        
     
        $restoreExam = ExamDigital::withTrashed()->find($request->restore_id)->restore();
        return redirect::to('digital/view/exam')->with('message', 'Exam Restored Successfully.');
    }
    
    public function download_marksheet(Request $request){

        $search['name'] = "";
        $search['class_type_id'] = $request->class_name;
        $search['exam_id'] = $request->exam_id ?? "";
      
        if($request->isMethod('post')){
      
           // $data = Admission::where('class_type_id',$request->class_name)->where('section_id',$request->section)->get();
            $data =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	 ->where('fill_marks.exam_id',$request->exam_id)->where('fill_marks.class_type_id', $request->class_name)
    	 ->where('fill_marks.session_id', Session::get('session_id'))->groupBy('fill_marks.admission_id')->get();
             
            
          
            return view('examination.download_marksheet',['data'=>$data,'search'=>$search]);
        }
        return view('examination.download_marksheet',['search'=>$search]);
    }
    
    public function printReportCard(Request $request){

        if($request->isMethod('post')){
            $data ='';
             
            if($request->class_type_id >10)
            {
                $data =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.image','admission.dob','admission.gender_id','admission.father_name','admission.father_name','admission.mother_name','admission.id as sr_no','sessions.from_year','sessions.to_year',
            'admission.admissionNo', 'admission.dob','type.name as class_name','subject.sub_name as subject_name')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	     ->leftjoin('sessions as sessions','sessions.id','fill_marks.session_id')
    	    ->leftjoin('class_types as type','type.id','fill_marks.class_type_id')
    	    ->where('fill_marks.exam_id',$request->exam_id)
            ->where('fill_marks.admission_id',$request->student_id)
            ->where('fill_marks.class_type_id', $request->class_type_id)
            ->where('fill_marks.session_id', Session::get('session_id'))->get();
            }
            else
            {
                 $data =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.image','admission.dob','admission.gender_id','admission.father_name','admission.father_name','admission.mother_name','admission.id as sr_no','sessions.from_year','sessions.to_year',
            'admission.admissionNo', 'admission.dob','type.name as class_name','subject.name as subject_name')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->leftjoin('sessions as sessions','sessions.id','fill_marks.session_id')
    	    ->leftjoin('class_types as type','type.id','fill_marks.class_type_id')
    	    ->leftjoin('subject as subject','subject.id','fill_marks.subject_id')
    	    ->where('fill_marks.exam_id',$request->exam_id)
            ->where('fill_marks.admission_id',$request->student_id)
            ->where('fill_marks.class_type_id', $request->class_type_id)
            ->where('fill_marks.session_id', Session::get('session_id'))->get();
            }
            
           
           
       
        
      if($request->class_type_id < 9)
      {  
           return view('examination.download_till_8_pdf',['data'=>$data]);
      }
      else
      {
           return view('examination.download_pdf',['data'=>$data]);
      }
       
        }
    }
    
    public function fillMarks(Request $request){
         $search['name'] = "";
         $search['class_type_id'] = $request->class_type_id;
         $search['class_type_id'] = $request->class_name;
         $search['exam_id'] = $request->exam_id ?? "";
        //  $search['class_type_id'] = $request->class_name ?? "";
         
         $data1='';
         $data2='';
         $marks='';
       
        if($request->isMethod('post')){
            $request->validate([

         ]);
        
           if((Int)$request->class_name >10)
            {
                     $data = FillMarks::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_name)->where('session_id', Session::get('session_id'))->first();
            }
            else
            {
                     $data = FillMarks::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_name)->where('session_id', Session::get('session_id'))->first();
            }
         
   
        $data_null = true;

        if(empty($data)){
            
            if((Int)$request->class_name >10)
            {
                
               $data2 = Admission::where('class_type_id',$request->class_name)->get();
                $marks =  FillMarks::select('fill_marks.*','admission.name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->get();
          
        
            }
            else
            {
                
              $data1 = Subject::where('class_type_id',$request->class_name)->orderBy('name','ASC')->get();
               $data2 = Admission::where('class_type_id',$request->class_name)->get();
                $marks =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->get();
            }
               
            }
        else
        {
    
        $data_null =false;
          if((Int)$request->class_name >10)
            {
                 $data1 =  FillMinMaxMarks::select('fill_min_max_marks.*','subjects.sub_name')
	    ->where('fill_min_max_marks.exam_id',$request->exam_id)->where('fill_min_max_marks.class_type_id', $request->class_name)->where('fill_min_max_marks.session_id', Session::get('session_id'))->get();
	     $marks =  FillMarks::select('fill_marks.*','admission.name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->get();
    	    
    
    	    $data2 =  FillMarks::select('fill_marks.*','admission.name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.exam_id',$request->exam_id)->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->groupBy('admission.admissionNo')->get();
      
            }
            else
            {
                 
             $data1 =  FillMinMaxMarks::select('fill_min_max_marks.*','subjects.name')
            ->leftjoin('subject as subjects','subjects.id','fill_min_max_marks.subject_id')
            ->where('fill_min_max_marks.exam_id',$request->exam_id)->where('fill_min_max_marks.class_type_id', $request->class_name)->where('fill_min_max_marks.session_id', Session::get('session_id'))->get();
            $marks =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.father_name','admission.admissionNo')
            ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->get();
    	    
    
    	    $data2 =  FillMarks::select('fill_marks.*','admission.first_name','admission.last_name','admission.father_name','admission.admissionNo')
    	    ->leftjoin('admissions as admission','admission.admissionNo','fill_marks.admission_id')
    	    ->where('fill_marks.exam_id',$request->exam_id)->where('fill_marks.class_type_id', $request->class_name)->where('fill_marks.session_id', Session::get('session_id'))->groupBy('admission.admissionNo')->get();
         
            }
        
    	       }

   $examlist =  AssignExamDigital::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
           ->leftjoin('exams_digital as exam','assign_exams.exam_id','exam.id')
           ->where('assign_exams.class_type_id',$request->class_name)->get();
//  $section = Classs::with('Section')->where('class_id',$request->class_name)->get();
		  return view('examination.fill_marks',['data1'=>$data1,'data2'=>$data2,'search'=>$search,'data_null'=>$data_null,'examlist'=>$examlist]);
        }
        return view('examination.fill_marks',['search'=>$search]);
    }
    
    public function fillMarksSubmit(Request $request){
                 if($request->isMethod('post')){
                 $request->validate([
                     
     //   'name'  => 'required',
      //   'exam_date'  => 'required',
        // 'to_date'  => 'required',

         ]);
         
          $data='';
          if((Int)$request->class_type_id >10)
            {
                  $data = FillMarks::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->where('session_id', Session::get('session_id'))->first();
        
         
            }
            else
            {
                  $data = FillMarks::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->where('session_id', Session::get('session_id'))->first();
        
         
                
            }
             

        if(empty($data))
        {
            
          
              if(!empty($request->subject_id))
         {
             for($i=0; $i<count($request->subject_id); $i++)
             {
                  $add = new FillMinMaxMarks;//model name
	    $add->class_type_id = $request->class_type_id;
	     $add->exam_id = $request->exam_id;
	  
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');
		 $add->subject_id =$request->subject_id[$i];
		 $add->exam_minimum_marks =$request->exam_minimum_marks[$i];
		 $add->exam_maximum_marks =$request->exam_maximum_marks[$i];
	     $add->save();
      
             }
         }
           
           
              if(!empty($request->admission_id))
         {
             
             
             $count= 0;
             for($i=0; $i<count($request->admission_id); $i++)
             {
                 
                 
                 for($j=0; $j<count($request->subject_id); $j++)
                 {
                     
                       $add = new FillMarks;//model name
	   
	        $add->exam_id = $request->exam_id;
	        $add->class_type_id = $request->class_type_id;
	     $add->admission_id = $request->admission_id[$i];
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');
		 $add->subject_id =$request->sub_id[$j];
		 $add->student_marks =$request->student_marks[$count];

	     $add->save();
	      $count++;
                 }
                
       
             }
         }

		 return redirect::to('fill_marks')->with('message', 'Marks Updated Successfully.');
            
        }
        else
        {
            
                if(!empty($request->fill_min_max_id))
            {
                
                
                for($i=0; $i<count($request->fill_min_max_id); $i++)
                {
                     $update = FillMinMaxMarks::where('id',$request->fill_min_max_id[$i])->update(['exam_minimum_marks'=>$request->exam_minimum_marks[$i],'exam_maximum_marks'=>$request->exam_maximum_marks[$i]]);
                }
               
            }
            
            if(!empty($request->fill_marks_id))
            {
                
                
                for($i=0; $i<count($request->fill_marks_id); $i++)
                {
                     $update = FillMarks::where('id',$request->fill_marks_id[$i])->update(['student_marks'=>$request->student_marks[$i]]);
                }
               
            }
            
            
            
            	 return redirect::to('fill_marks')->with('message', 'Marks already submitted.');
        }
       
    
       
        }
          
      }

    public function viewExamTeacher(Request $request,$id){
        
         
        if( Session::get('role_id') == 2){
            
            $data =  ExamResultDigital::select('exam_results.*','exam.name as exam_name','class.name as class_name','exam.exam_date as exam_date','student.first_name as student_fname','student.last_name as student_lname')
	    ->leftjoin('admissions as student','student.admissionNo','exam_results.admission_id')
	    ->leftjoin('exams_digital_digital as exam','exam.id','exam_results.exam_id')
	    ->leftjoin('class_types as class','class.id','student.class_type_id')
	
	    

	    ->where('exam_results.exam_id',$id)->get(); 
          
         
		  return view('examination.exam.teacherView ',['data'=>$data]);
        }
      
       
    }

    public function editExam(Request $request, $id){
         $data = ExamDigital::find($id);
         
        
        if($request->isMethod('post')){
            $request->validate([

         'name'  => 'required',
         'exam_date'  => 'required',
   
         ]);

	     $data->user_id = Session::get('id');
         $data->session_id = Session::get('session_id');
         $data->branch_id = Session::get('branch_id');	     
		 $data->name =$request->name;
		 $data->exam_date =$request->exam_date;
		 $data->duration =$request->duration ?? $data->duration;
		 $data->description =$request->description;
		 $data->exam_code = $request->exam_code ?? $data->exam_code;
	     $data->save();

            return redirect::to('digital/view/exam')->with('message', 'Exam Updated Successfully.');
        }

        return view('examination.digital.exam.edit',['data'=>$data]);
    } 

    public function deleteExam(Request $request){
        $deleteExam = ExamDigital::find($request->delete_id)->update(['user_id' => Session::get('id'), 'branch_id' => Session::get('branch_id'), 'session_id' => Session::get('session_id')]);
        $deleteExam = ExamDigital::find($request->delete_id)->delete();
        return redirect::to('digital/view/exam')->with('message', 'Exam Deleted Successfully.');
    }
    
    public function printExam($id){
        $exam = ExamDigital::find($id);
        $question  = [];
        $quesArray = explode(',', $exam->questions_id);
        foreach($quesArray as $ques){
            $question[] = QuestionDigital::find($ques);
        }
        return view('examination.digital.exam.print', ['data' => $exam, 'question' => $question]);
    }

    public function answerKey($id){
        $exam = ExamDigital::find($id);
        $question  = [];
        $quesArray = explode(',', $exam->questions_id);
        foreach($quesArray as $ques){
            $question[] = QuestionDigital::find($ques);
        }
        return view('examination.digital.exam.answerKey', ['data' => $exam, 'question' => $question]);
    }
    
    public function assignQuestion(Request $request){
        if($request->isMethod('post')){
           if($request->submit_id=="0"){
            $add = new AssignQuestion;//model name
            $add->user_id = Session::get('id');
            $add->session_id = Session::get('session_id');
            $add->branch_id = Session::get('branch_id'); 
            $add->exam_id = $request->exam_id;
            $add->question_id = $request->question_id;
            $add->subject_id = $request->subject_id;
    	    $add->save();
    	    
            }else{
            $id=$request->post('question_id');
            $data = AssignQuestionDigital::where('question_id',$id)->delete();
            } 
        }
    }

    public function searchkAssignedQuestion(Request $request){
       // dd($request);
	    $question_type_id = $request->get('question_type_id');
	    $subject_id = $request->get('subject_id');
        $data =  QuestionDigital::with('Subject')->orderBy('id',"DESC");
      
            if(!empty($question_type_id)){
               $data = $data ->where("question_type_id", $question_type_id);
            }           
            if(!empty($subject_id)){
               $data = $data ->where("subject_id", $subject_id);
            } 
            
           
            
        $allquestions = $data->where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->orderBy('id','DESC')->get();
        
        $assigned_questions = AssignQuestionDigital::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->where('exam_id',$request->exam_id1)->pluck('question_id');
     
    
      
      return  view('examination.exam.search_assign_question',['data'=>$allquestions,'assigned_questions'=>$assigned_questions]);
    }
    
    public function searchkAlreadyAssignedQuestion(Request $request){
      $already_assigned = QuestionDigital::select('questions.*','Exam.name as examName','Exam.duration as duration')
	    ->leftjoin('assign_questions as assignQuestion','assignQuestion.question_id','questions.id')
	    ->leftjoin('exams_digital_digital as Exam','assignQuestion.exam_id','Exam.id')
	    ->where('assignQuestion.exam_id',$request->id)->get();
      
      return  view('examination.exam.search_already_assign_question',['data'=>$already_assigned]);
    }
    
    public function assignExam(Request $request, $id){
        $examId = $id;
        
          $data2 = ExamDigital::where('id',$id)->first(['name','exam_date']);

         if($request->isMethod('post')){
                 $request->validate([
                     
            'class_type_id'  => 'required',
         ]);

         $add = new AssignExam; //model name
	     $add->user_id = Session::get('id');
	     $add->session_id = Session::get('session_id');
         $add->branch_id = Session::get('branch_id');    
		 $add->class_type_id = $request->class_type_id;
		 $add->exam_id = $examId;
	     $add->save();
	     
	    $users = Admission::where('class_type_id',$request->class_type_id)->where('session_id',Session::get('session_id'))
	                   ->where('branch_id',Session::get('branch_id'))->get();
	     
	    $template = MessageTemplate::Select('message_templates.*','message_types.slug')
                    ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                    ->where('message_types.status',1)->where('message_types.slug','exam')->first();
            
        $branch = Branch::find(Session::get('branch_id'));
        $setting = Setting::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->first();
        
        for($count = 0; $count < count($users); $count++){

        $arrey1 =   array(
                        '{#name#}',
                        '{#exam_name#}',
                        '{#exam_date#}',
                        '{#support_no#}',
                        '{#school_name#}');
                       
        $arrey2 = array(
                        $users[$count]->first_name." ".$users[$count]->last_name,
                        $data2->name,
                        date('d-m-Y',strtotime($data2->exam_date)),
                        $setting->mobile,
                        $setting->name);
                    
                    if($template->status != 1){
                            if($users[$count]->email != ""){
                                if($branch->email_srvc != 0){
                                    if($template->email_status != 0){
                                        $message = str_replace($arrey1,$arrey2,$template->email_content);
                                        $emailData = ['email' => $users[$count]->email, 'data' => $message, 'subject' => $template->title];
                                        Helper::sendMail('email_print.template_print', $emailData);
                                    } 
                                } 
                            }
                        
                            if($branch->whatsapp_srvc != 0){
                                if ($users[$count]->mobile != ""){
                                    if($template->whatsapp_status != 0){
                                        $whatsapp = str_replace($arrey1,$arrey2,$template->whatsapp_content);
                                        Helper::sendWhatsappMessage($users[$count]->mobile,$whatsapp);
                                    }
                                }
                            }
                            
                            if($branch->sms_srvc != 0){
                                if($users[$count]->mobile != ""){
                                    if($template->sms_status != 0){
                                        $sms = str_replace($arrey1,$arrey2,$template->sms_content);
                                        Helper::SendMessage($users[$count]->mobile, $sms);
                                    }
                                }
                            }    
                    }
                }	
                
		 return redirect::to('view/exam')->with('message', 'Exam Assigned Successfully.');
        }

        return view('examination.exam.assign',['data'=>$examId,'data2'=>$data2]);
    } 
    
    public function assignExamEdit(Request $request, $id,$class_type){
        $examId = $id;
            $data2 = ExamDigital::where('id',$id)->first('name');
        $oldData = AssignExamDigital::where('exam_id', $examId)->where('class_type_id', $class_type)->first();
         if($request->isMethod('post')){
                 $request->validate([
                     
            'class_type_id'  => 'required',

         ]);

           

            if (!empty($oldData)) {
                
                $new = AssignExamDigital::where('id', $request->id)->update(['class_type_id'=>$request->class_type_id]);
                return redirect::to('view/exam')->with('infor', 'Exam Reassigned successfully !');
            } 
         
      
	
		 return redirect::to('view/exam')->with('message', 'Exam Assigned Successfully.');
        }


        return view('examination.exam.assign_edit',['data'=>$oldData,'data2'=>$data2]);
    } 

    public function onlineExam($id){
        /*$data = QuestionDigital::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->orderBy('id', 'DESC')->get();*/
       
       
       
      $old_data = ExamResultDigital::where('admission_id',Session::get('id'))->where('exam_id',$id)->first();
      
      if(empty($old_data))
      {
           $add = new ExamResult;
	       $add->user_id = Session::get('id');
           $add->session_id = Session::get('session_id');
           $add->branch_id = Session::get('branch_id');	           
           $add->admission_id = Session::get('id');	           
           $add->exam_id = $id;
         
           $add->save();
      }
       
        
           
           
        $data = QuestionDigital::select('questions.*','Exam.name as examName','Exam.duration as duration')
	    ->leftjoin('assign_questions as assignQuestion','assignQuestion.question_id','questions.id')
	    ->leftjoin('exams_digital_digital as Exam','assignQuestion.exam_id','Exam.id')
	    ->where('assignQuestion.exam_id',$id)->get();     
	    

	    
            $data1 = array();
            $data2= array();

			foreach ($data as $item) {
			    $data2[0] = $item->ans1 ;
                $data2[1] = $item->ans2 ;
                $data2[2] =$item->ans3  ;
                $data2[3] = $item->ans4 ;
					$data1[] = array(
					'title' => $item->name,
			       	'choices' => $data2,
					'correctAnswer' => $item->correct_ans,
					'pointerEvents' => false,
					'secondsLeft' => 30,
					'AnsweredQue' => "",
					'question_id' => $item->id,
					'question_type_id' => $item->question_type_id,
					'exam_id' => $id,
					'duration' => $item->duration,
				
					
					);
				
			}
			

        return view('examination.online_exam.view',['data'=>$data,'data1'=>$data1,'id'=>$id]);
    }
   
    public function addExamResult(Request $request){
        
        
      
        $adminMail = User::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->where('role_id','1')->get()->first();        $stu = Admission::where('id',Session::get('id'))->get()->first();
        
        
        
        $id = ExamResultDigital::where('exam_id',$request->exam_id )->where('admission_id',Session::get('id') )->first();
        $add = ExamResultDigital::where('exam_id',$request->exam_id )->where('admission_id',Session::get('id') )->update([
            
            'total_ques'=>$request->total_que,
            'correct_ans'=>$request->correct_ans,
            'wrong_ans'=>$request->wrong_ans,
            'skip_ques'=>$request->skip,
            'percentage'=>($request->correct_ans/$request->total_que)*100,
            
            'time'=>$request->timer,
            
            ]);
        //   $add = new ExamResult;
	       //$add->user_id = Session::get('id');
        //   $add->session_id = Session::get('session_id');
        //   $add->branch_id = Session::get('branch_id');	           
        //   $add->admission_id = Session::get('id');	           
        //   $add->exam_id = $request->exam_id;
        //   $add->total_ques = $request->total_que;
        //   $add->correct_ans = $request->correct_ans;
        //   $add->wrong_ans = $request->wrong_ans;
        //   $add->skip_ques = $request->skip;
        //  $add->percentage = ($request->correct_ans/$request->total_que)*100;
        //   $add->time = $request->timer;
        //   $add->save();
           $resultId = $id->id;

            for ($count = 0; $count <= count($request->ques_name); $count++) {
                if (isset($request->ques_name[$count])) {   
                    
                  $detail = new ExamResultDetail;
        	       $detail->user_id = Session::get('id');
                  $detail->session_id = Session::get('session_id');
                  $detail->branch_id = Session::get('branch_id');	           
                  $detail->admission_id = Session::get('id');
                  $detail->exam_result_id = $resultId;
                  $detail->exam_id = $request->exam_id;
                  $detail->ques_id = $request->ques_id[$count];
                  $detail->ques_name = $request->ques_name[$count];
                  $detail->correct_ans = $request->ques_ans[$count];
                  $detail->submit_ans = $request->submit_ans[$count];                   
                  $detail->save();  
                }
            } 
           
        /*if (!empty($adminMail['email'])) {
            $emailData = ['email' => $adminMail['email'], 'first_name' => $stu['first_name'], 'last_name' => $stu['last_name'], 'father_name' => $stu['father_name'],
            'total_ques' => $request->total_que,'correct_ans' => $request->correct_ans, 'skip_ques' => $request->skip_ans, 'percentage' => $request->percentage,
            'time' => $add->time, 'dateTime' => date('l jS \of F Y h:i:s A'),'subject' => 'Today Exam Result'];
            Helper::sendMail('email_print.exam_result_student', $emailData);
        }*/
        
        /*if (!empty($stu['email'])) {
            $emailData = ['email' => $stu['email'], 'first_name' => $stu['first_name'], 'last_name' => $stu['last_name'], 'father_name' => $stu['father_name'],
            'total_ques' => $request->total_que,'correct_ans' => $request->correct_ans, 'skip_ques' => $request->skip_ans, 'percentage' => $request->percentage,
            'time' => $add->time, 'dateTime' => date('l jS \of F Y h:i:s A'),'subject' => 'Today Exam Result'];
            Helper::sendMail('email_print.exam_result_student', $emailData);
             
            
        } */           
   return redirect('view/exam')->with('message','Exam Submitted !');
        /*if(!empty($request->mobile)){
            $SmsSetting = SmsSetting::where('category','otp')->get()->first();
               $message = !empty($SmsSetting->message) ? $SmsSetting->message : '' ;
             
              $message_otp = str_replace("{#var#}", '4595', $message);
              Helper::SendMessage($request->mobile,$message_otp);
        }*/                

        /*if(!empty($request->mobile)){
            $whatsApp = WhatsappSetting::where('category','otp')->get()->first();
               $message = !empty($whatsApp->message) ? $whatsApp->message : '' ;
             
              $message_otp = str_replace("{#var#}", '4595', $message);
              Helper::sendWhatsapp($request->mobile,$message_otp);
        }*/   
  
    }
     
    public function viewResult(Request $request){
        
        $search['name'] = $request->name;

        $data = ExamResultDigital::select('exam_results.*')
		 ->leftjoin('assign_exams as assignExam','assignExam.exam_id','exam_results.exam_id')
		 ->leftjoin('exams_digital_digital as Exam','Exam.id','exam_results.exam_id')
		 ->where('exam_results.session_id',Session::get('session_id'))->where('exam_results.branch_id',Session::get('branch_id'));
            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("Exam.name",'like','%'.$request->name.'%')
                    ->orWhere("Exam.exam_date",'like','%'.$request->name.'%')
                    ->orWhere("Exam.description",'like','%'.$request->name.'%');
                }
            }
            $data = $data->groupBy('exam_id')->orderBy('id','DESC')->get();
        
        return view('examination.result.view ',['data'=>$data,'search'=>$search]);
    }

    public function viewExamResult(Request $request, $id){
        
        $search['name'] = $request->name;

        $data = ExamResultDigital::select('exam_results.*')
		 ->leftjoin('admissions as Admission','Admission.id','exam_results.admission_id')
		 ->leftjoin('exams_digital as Exam','Exam.id','exam_results.exam_id')
		 ->where('Admission.session_id',Session::get('session_id'))->where('Admission.branch_id',Session::get('branch_id'))
		 ->where('exam_results.exam_id',$id);
            if($request->isMethod('post')){
                if (!empty($request->name)){
                    $data = $data->where("Admission.first_name",'like','%'.$request->name.'%')
                    ->orWhere("Admission.last_name",'like','%'.$request->name.'%')
                    ->orWhere("Admission.father_name",'like','%'.$request->name.'%')
                    ->orWhere("Admission.mother_name",'like','%'.$request->name.'%')
                    ->orWhere("Admission.mobile",'like','%'.$request->name.'%')
                    ->orWhere("Admission.email",'like','%'.$request->name.'%')
                    ->orWhere("Admission.aadhaar",'like','%'.$request->name.'%');
                }
            }
            $data = $data->orderBy('id','DESC')->get();
  
        return view('examination.result.view_result',['data'=>$data,'id'=>$id,'search'=>$search]);
    }
    
    public function viewExamResultEmail(Request $request){
      
      $emailData = ['email' => "skwork91@gmail.com",'subject' => 'Today Exam Result'];
            Helper::sendMail('email_print.admin.exam_result_email', $emailData);
           
      $emailData = ['email' => "kanak8856@gmail.com",'subject' => 'Today Exam Result'];
            Helper::sendMail('email_print.admin.exam_result_email', $emailData);
         
    
    
        return view('email_print.admin.exam_result_email');
    }

    public function downloadMarksheet(Request $request,$admission_id,$exam_id){
        
     $data = ExamResultDigital::select('exam_results.*','Admission.first_name as first_name','ClassType.name as class')
	    ->leftjoin('admissions as Admission','Admission.id','exam_results.admission_id')
	    ->leftjoin('class_types as ClassType','ClassType.id','Admission.class_type_id')
	    ->where('exam_results.admission_id',$admission_id)->where('exam_id',$exam_id)->get()->first(); 
	   // dd($data);
	    
        return view('print_file.exam.marksheet_download',['data'=>$data]);
    } 

    public function viewMarksheet(Request $request,$admission_id,$exam_id){

        $data = ExamResultDigital::select('exam_results.*','Admission.first_name as first_name','ClassType.name as class')
	    ->leftjoin('admissions as Admission','Admission.id','exam_results.admission_id')
	    ->leftjoin('class_types as ClassType','ClassType.id','Admission.class_type_id')
	    ->where('exam_results.admission_id',$admission_id)->where('exam_id',$exam_id)->get()->first(); 
	   // dd($data);
        return view('print_file.exam.marksheet_print',['data'=>$data]);
    } 
    
    public function answerKeyTeacher(Request $request,$exam_id,$student_id){
     
    
        $resultDetail = ExamResultDetail::with('Exam')->with('Question')->with('Admission')->where('exam_id',$exam_id)->where('admission_id',$student_id)->get();  
        $result = ExamResultDigital::where('exam_id',$exam_id)->where('admission_id',$student_id)->get()->first();  
       
     $skip_answer  = ExamResultDetail::with('Exam')->with('Question')->with('Admission')->where('exam_id',$exam_id)->where('admission_id',$student_id)->where('submit_ans',null)->count();  
      
    
	   // dd($skip_answer)
        return view('examination.result.answer_key',['data'=>$resultDetail,'result'=>$result,'skip_answer'=>$skip_answer]);
    }  
    
    public function fetchQuestion(Request $request){
         
        $data = QuestionDigital::select('questions.*','Exam.name as examName')
	    ->leftjoin('assign_questions as assignQuestion','assignQuestion.question_id','questions.id')
	    ->leftjoin('exams_digital as Exam','assignQuestion.exam_id','Exam.id')
	   ->get();     
	   
	   
	     foreach($data as $key =>$item)
	     {
        
            $data1[] = array('title' => $item['name'],'choices'=>[$item['ans1'],$item['ans2'],$item['ans3'],$item['ans4']], 'correctAnswer' => $item['correct_ans'],'pointerEvents'=>'false','secondsLeft'=>20,'AnsweredQue'=>"");
        }
       
        return response()->json([
            'students' =>  $data1,
        ]);
    }
    
    public function addExamStudents(Request $request){
   
    if($request->isMethod('post')){
          
          $the_file = $request->file('upload_contant');
       
           try{
               $spreadsheet = IOFactory::load($the_file->getRealPath());
               $sheet        = $spreadsheet->getActiveSheet();
               $row_limit    = $sheet->getHighestDataRow();
               $column_limit = $sheet->getHighestDataColumn();
               $row_range    = range( 2, $row_limit );
               $column_range = range( 'F', $column_limit );
               $startcount = 2;
               $data = array();
         
               foreach ( $row_range as $row ) {
           
                   $admission_no = $sheet->getCell( 'A' . $row )->getValue();
                  
                   $student_id = Student::where('id',$admission_no)->pluck('id')->first();
                          
               if(!empty($student_id)){
                   $data[] = [
                      
                    
                       'student_id' => $student_id,
                       'marks' => $sheet->getCell( 'B' . $row )->getValue(),
                       'total_marks' => $sheet->getCell( 'C' . $row )->getValue(),
                       'percentage' => $sheet->getCell( 'D' . $row )->getValue(),
                       'rank' => $sheet->getCell( 'E' . $row )->getValue(),
                       'date' => $sheet->getCell( 'F' . $row )->getValue(),
                       'exam_id' => $request->exam_id,
                       'date' => $request->date,
                    
                      
                        
                   ];
                   
                   $startcount++;
                    }   
                        
               }
               DB::table('student_result')->insert($data);
           
           } catch (Exception $e) {
               $error_code = $e->errorInfo[1];
               return redirect('add_exam_result')->with('error','Error Result Not Added !');
           }
       
           return redirect('examination_dashboard')->with('message','Result Add Successful !');
        }
       
        return view('examination.add');
    }
    
    public function viewExamStudents(Request $request){
        
          $search['name'] = $request->name;
          $search['exam_id'] = $request->exam_id;
          
      
        
        
         if($request->isMethod('post')){
             
               $data =StudentGrow::select('student_result.*','enquirys.first_name as name','exams.name as exam_name')
        ->leftjoin('enquirys as enquirys','enquirys.id','student_result.student_id')
        ->leftjoin('exams_digital as exams','exams.id','student_result.exam_id');
        
             
            $request->validate([]);
            if(!empty($request->name)){
                $value = $request->name;
                 $data = $data->where(function($query) use ($value){
        		  $query->where('name', 'like', '%' .$value. '%');

        		});    
                
                
                
                
            }
           if(!empty($request->stu_id)){
               $data = $data ->where("enquirys.id", $request->stu_id);
            }     
           if(!empty($request->exam_id)){
               $data = $data ->where("exams.id", $request->exam_id);
            }    
             $data = $data->orderBy('rank','ASC')->get();
  
        return view('examination.view',['data'=>$data,'search'=>$search]);
    
               
        }

        return view('examination.view',['search'=>$search]);
    }
    
    public function examPrint(Request $request){
        
        $data = StudentGrow::select('student_result.*','students.first_name as name')
        ->leftjoin('students as students','students.id','student_result.student_id')
        ->leftjoin('exams_digital as exams','exams.id','student_result.exam_type_id')->where('date',date('Y-m-d'))->OrderBy('rank')->get();
        
        return view('examination.exam_print',['data'=>$data]);
    }
    
    public function StudentAdmitCard(Request $request,$id){
        
        $data=Student::where('id',$id)->get()->first();
        
        return view('examination.admit_card',['data'=>$data]);
    }
    
    public function addExaminationSchedule(Request $request){
        $search['class_type_id'] = $request->class_type_id;
        $search['exam_id'] = $request->exam_id;
       
        $exam_code ="";
    
         $old_data =[];
         $data1 ="";
         $data2 ="";
           $exam_code = ExamDigital::where('id',$request->exam_id)->first('exam_code'); 
                    
                
              
        
       
                if((Int)$request->class_type_id > 10)
              {
                  
              $old_data = ExaminationSchedule::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->get();
            
                
            
              }
              
              else
              {
              $old_data = ExaminationSchedule::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->get();
              }
            //  $old_data = ExaminationSchedule::where('exam_code',$exam_code->exam_code)->get();
            
       
        
        
        
        if(count($old_data) > 0)
        {
            
                 if((Int)$request->class_type_id > 10)
              {
                
                    $data1 = ExaminationScheduleDetail::select('examination_schedule_details.*','subject.sub_name as subject_name')
        ->where('examination_schedule_details.class_type_id',$request->class_type_id)->get();
      
      
            
             $data2 =  AssignExamDigital::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
	    ->leftjoin('exams_digital as exam','assign_exams.exam_id','exam.id')
        ->where('assign_exams.class_type_id',$request->class_type_id)->get();
              }
              
              else
              {
             $data1 = ExaminationScheduleDetail::select('examination_schedule_details.*','subject.name as subject_name','subject.id as subject_id')
	    ->leftjoin('subject as subject','subject.id','examination_schedule_details.subject_id')
        ->where('examination_schedule_details.class_type_id',$request->class_type_id)->get();
      
             $data2 =  AssignExamDigital::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
	    ->leftjoin('exams_digital as exam','assign_exams.exam_id','exam.id')
        ->where('assign_exams.class_type_id',$request->class_type_id)->get();
              }
        
      
        }
        else
        {
            

        $data1 = Subject::where('class_type_id',$request->class_type_id)->orderBy('name','ASC')->get();
            

        
         $data2 =  AssignExamDigital::select('assign_exams.*','exam.id as exam_id','exam.name as exam_name')
	    ->leftjoin('exams_digital as exam','assign_exams.exam_id','exam.id')
        ->where('assign_exams.class_type_id',$request->class_type_id)->get();
      
        }
         

        // if(!empty($shaduleData)){
        //     $data1 = $shaduleData;
        // }else{
        //     $data1 = Subject::where('class_type_id',$request->class_type_id)->orderBy('name','ASC')->get();
        // }
        return view('examination.examination_schedule.add',['search'=>$search, 'data1'=> $data1,'studentexamview'=>$data2,'exam_code'=>$exam_code->exam_code ?? '']);
    }
    
    public function SubmitSchedule(Request $request){
        // $shadule = ExaminationSchedule::where('class_type_id',$request->class_type_id__)->where('exam_id',$request->exam_id__)->get();
        if($request->isMethod('post')){
         
              $exam_code = "";
              $old_data = "";
              
              if((Int)$request->class_type_id__ > 10)
              {
                  
                   $exam_code = ExamDigital::where('id',$request->exam_id__)->first('exam_code'); 
              $old_data = ExaminationSchedule::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->get();
        
        
       
              }
              
              else
              {
                    $exam_code = ExamDigital::where('id',$request->exam_id__)->first('exam_code'); 
              $old_data = ExaminationSchedule::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->get();
              }
              if(count($old_data) > 0)
              {
                  
                 
                 
                  for($i=0; $i<count($request->exam_schedule_detail_id); $i++){
         $detail = ExaminationScheduleDetail::find($request->exam_schedule_detail_id[$i]);
           $detail->class_type_id = $request->class_type_id__;
           $detail->date = $request->date[$i];
           $detail->from_time = $request->from_time[$i];
           $detail->to_time = $request->to_time[$i];
           $detail->subject_id = $request->subject_id[$i];
           $detail->save();
        } 
                  
              }
              else
              {
                 
                    $data = new ExaminationSchedule;
           $data->class_type_id = $request->class_type_id__;
           $data->exam_id = $request->exam_id__;
           $data->exam_center = $request->exam_center;
           $data->exam_code = $exam_code->exam_code;
           $data->save();
                    for($i=0; $i<count($request->subject_id); $i++){
         
           
              $detail = new ExaminationScheduleDetail;
           $detail->examination_schedule_id = $data->id;
           $detail->class_type_id = $request->class_type_id__;
           $detail->date = $request->date[$i];
           $detail->from_time = $request->from_time[$i];
           $detail->to_time = $request->to_time[$i];
           
           $detail->subject_id = $request->subject_id[$i];
           $detail->save();
        } 
              }
       
    }
        return redirect::to('add/examination_schedule')->with('message', 'Schedule Update Successfully.');
        
    }

    public function addAdmitCard(Request $request){
        $search['class_type_id'] = $request->class_type_id;
        $search['exam_id'] = $request->exam_id;

        $data1 = "";
       
       
            $exam_code = "";
            $exam_id = "";
        
        
       if($request->isMethod('post')){
           
           
             $data = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'));
        
            $data = $data->orderBy('id','DESC')->get();
           
           
           $exam_code = ExamDigital::where('id',$request->exam_id)->first(); 
           
           if((Int)$request->class_type_id > 10)
           {
                $old_data = ExaminationAdmitCard::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->get();
             
           }
           else
           {
                $old_data = ExaminationAdmitCard::where('exam_id',$request->exam_id)->where('class_type_id',$request->class_type_id)->get();
             
           }
             
             
          
                $examSchedule = "";
                $exam_code = $exam_code->exam_code ?? "";
            $exam_id = $request->exam_id ?? '';
                $data1 = "";
                
             if(count($old_data) >0) 
              {
                  if((Int)$request->class_type_id > 10)
           {
                    $data1 =  ExaminationAdmitCard::select('examination_admit_cards.*','admission.father_mobile','admission.father_name','admission.admissionNo','admission.name','admission.mobile')
	    ->leftjoin('admissions as admission','admission.id','examination_admit_cards.admission_id')
        ->where('examination_admit_cards.class_type_id',$request->class_type_id)->where('examination_admit_cards.exam_id',$request->exam_id)->get();
            
           }else
           {
                   
         $data1 =  ExaminationAdmitCard::select('examination_admit_cards.*','admission.father_mobile','admission.father_name','admission.admissionNo','admission.first_name','admission.last_name','admission.mobile')
	    ->leftjoin('admissions as admission','admission.id','examination_admit_cards.admission_id')
        ->where('examination_admit_cards.class_type_id',$request->class_type_id)->where('examination_admit_cards.exam_id',$request->exam_id)->get();
           }
              }
            else
            {
                
                if((Int)$request->class_type_id > 10)
           {
               $data1 = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                    ->where('class_type_id',$request->class_type_id)->get();
           }
           else
           {
               $data1 = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))
                    ->where('class_type_id',$request->class_type_id)->get();
           }
                  
            }
    
        
      }
        
        return view('examination.admit_card.add',['search'=>$search, 'data1'=> $data1,'exam_id'=>$exam_id,'exam_code'=>$exam_code]);
    }
    
    public function SubmitAdmitCard(Request $request){
       
        if($request->isMethod('post')){
           
           $exam_code = ExamDigital::where('id',$request->exam_id__)->first('exam_code'); 
           $old_data =0;
           
           $schedule ="";
          
            if((Int)$request->class_type_id__ > 10)
           {
             
                          $old_data = ExaminationAdmitCard::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->get(); 
           
                  $schedule = ExaminationSchedule::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->first();
        
      
               
           }
           
           else
           {
                          $old_data = ExaminationAdmitCard::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->get(); 
         $schedule = ExaminationSchedule::where('exam_id',$request->exam_id__)->where('class_type_id',$request->class_type_id__)->first();
             
         
           }
           
           
           if(!empty($schedule))
           {
                          if(count($old_data)> 0)
           {
             
             
                for($i=0; $i<count($request->exam_admit_card_id); $i++){
               
           $data = ExaminationAdmitCard::find($request->exam_admit_card_id[$i]);
        //   $data->class_type_id = $request->class_type_id__;
        //   $data->exam_id = $request->exam_id__;
        //   $data->exam_code = $exam_code->exam_code;
        //   $data->admission_id = $request->admission_id[$i];
           $data->exam_roll_no = $request->exam_roll_no[$i];
           $data->examination_schedule_id = $schedule->id;
           $data->save();
        }
           }
           else
           {
               
                for($i=0; $i<count($request->admission_id); $i++){
               
           $data = new ExaminationAdmitCard;
           $data->class_type_id = $request->class_type_id__;
           $data->exam_id = $request->exam_id__;
           $data->exam_code = $exam_code->exam_code;
           $data->admission_id = $request->admission_id[$i];
           $data->exam_roll_no = $request->exam_roll_no[$i];
            $data->examination_schedule_id = $schedule->id;
           $data->save();
        }
           }
           }
           else
           {
                return redirect::to('add/admit_card')->with('error', 'Examination schedule not prepared for the exam.');
           }
           

           
         
    }
        return redirect::to('add/admit_card')->with('message', 'Examination Admit Card Update Successfully.');
        
    }
    
    public function downloadAdmitCard(Request $request,$exam_id,$class_type_id,$admission_id){

        $arr ;
        if ($admission_id != ""){
            foreach(explode(',', $admission_id) as $info){
                $arr[] = $info;
            }
        }

        if($request->isMethod('get')){
            $data1='';
            $data1 =  ExaminationAdmitCard::select('examination_admit_cards.*','class.name as class_name','admission.image as student_profile_image','admission.mother_name','admission.father_mobile','admission.father_name','admission.admissionNo','admission.first_name','admission.last_name','admission.mobile','examination_schedule.id as examination_schedule_id')
                    ->leftjoin('admissions as admission','admission.id','examination_admit_cards.admission_id')
                    ->leftjoin('class_types as class','class.id','examination_admit_cards.class_type_id')
                    ->leftjoin('examination_schedules as examination_schedule','examination_schedule.id','examination_admit_cards.examination_schedule_id')
                    ->where('examination_admit_cards.class_type_id',$class_type_id)->where('examination_admit_cards.exam_id',$exam_id);
        
             if(!empty($data1)){
                if((Int)$class_type_id > 10){
                    $data1 =$data1->whereIn('examination_admit_cards.admission_id',$arr)->get();
                }
                else{
                    $data1=$data1->whereIn('examination_admit_cards.admission_id',$arr)->get();
                }
    
                $school_data = Setting::first();
          
                $pdf = PDF::loadView('print_file.pdf.admit_card_all',['data'=>$data1,'school_data'=>$school_data]);
                return view('print_file.pdf.admit_card_all',['data'=>$data1,'school_data'=>$school_data]);
            }
            
            else{
                return redirect::to('add/admit_card')->with('error', 'No Data Found.');
            }
             
        }
    }
      
    public function AdmitCardNotes(Request $request){
            $data = AdmitCardNote::find(1);
            if(!empty($data))
            {
            $data->user_id = Session::get('id');
            $data->session_id = Session::get('session_id');
            $data->branch_id = Session::get('branch_id');
            $data->note = $request->note;
            $data->status = 1;
            $data->save();
            return redirect::to('add/admit_card')->with('message', 'Note Edit Successfully');
            }else
            {
               $note = new AdmitCardNote;
               $note->user_id = Session::get('id');
               $note->session_id = Session::get('session_id');
               $note->branch_id = Session::get('branch_id');
               $note->note = $request->note;
               $note->status = 1; 
               $note->save();
               return redirect::to('add/admit_card')->with('message', 'Note Add Successfully');
            }
      }
      
    public function singleDownloadAdmitCardPdf(Request $request,$class_type_id,$exam_id,$admission_id){
            
      if($request->isMethod('get')){
          
           $exam_code = ExamDigital::where('id',$exam_id)->first('exam_code');
           
             $data1="";
           
                 
                      $data1 =  ExaminationAdmitCard::select('examination_admit_cards.*','class.name as class_name','admission.image as student_profile_image','admission.mother_name','admission.father_mobile','admission.father_name','admission.admissionNo','admission.first_name','admission.last_name','admission.mobile','examination_schedule.id as examination_schedule_id')
	    ->leftjoin('admissions as admission','admission.id','examination_admit_cards.admission_id')
	    ->leftjoin('class_types as class','class.id','examination_admit_cards.class_type_id')
	    ->leftjoin('examination_schedules as examination_schedule','examination_schedule.id','examination_admit_cards.examination_schedule_id')
        ->where('examination_admit_cards.class_type_id',$class_type_id)->where('examination_admit_cards.exam_id',$exam_id)
        ->where('examination_admit_cards.admission_id',$admission_id);
        
                
 
           
              
            
    
    

             if(!empty($data1)) 
              {
                 
                 $data1 =$data1->get();

           $school_data = Setting::first();
          
              $pdf = PDF::loadView('print_file.pdf.admit_card_all',['data'=>$data1,'school_data'=>$school_data]);
             // return $pdf->download('students_admit_card.pdf');
                      return view('print_file.pdf.admit_card_all',['data'=>$data1,'school_data'=>$school_data]);

             }
             else
             {
                  return redirect::to('add/admit_card')->with('error', 'No Data Found.');
             }
             
             
              
        }
          
      }
    //  public function examTerminal(Request $request){
        
           
    //     return  view('examination.online_exam.examTerminal');
         
            
    // }

     public function resultExam(Request $request){
        
           
        return  view('examination.online_exam.resultExam');
         
            
    }


public function digitalExamTerminal(Request $request){
         
         $search['from_date'] = $request->from_date ?? null;
         $search['to_date'] = $request->to_date ?? null;
         $search['exam_pattern'] = $request->exam_pattern ?? null;
         $search['subject'] = $request->subject ?? null;
         
         
         $data = ExamDigital::where('session_id',Session::get('session_id'))->whereIn('class_type_id',[Session::get('class_type_id'),3])->orderBy('id','DESC');
         $exam_count = ExamResultDigital::where('admission_id',Session::get('id'))->get();
        
          if($search['from_date'] != '')
          {
           $data = $data->whereDate('exam_date','>=',$search['from_date'])->whereDate('exam_date','<=',$search['to_date']);
          }
           
          if($search['from_date'] != '')
          {
           $data = $data->whereDate('exam_date','>=',$search['from_date'])->whereDate('exam_date','<=',$search['to_date']);
          }
          
          if($search['exam_pattern'] != '')
          {
           $data = $data->where('pattern_id',$search['exam_pattern']);
          }
          
          if($search['subject'] != '')
          {
              $searchSubject = (array) $search['subject'];
           $data = $data->whereIn('subject_id',$searchSubject);
          }
           
           $data = $data->get();
        return  view('examination.digital.exam.examTerminal',['data'=>$data,'exam_count'=>$exam_count,'search'=>$search]);
         
            
    }    
    
    public function examResultStudentList(Request $request,$exam_id)
{
    $examData = ExamDigital::where('id',$exam_id)->first();
    $admission_ids = ExamResultDigital::select('admission_id')->where('exam_id', $exam_id)->groupBy('admission_id')->get();
 
 $data = [];
 foreach($admission_ids as $item)
 {
// $data[] = ExamResult::where('exam_id',$exam_id)->where('admission_id',$item->admission_id)->orderBy('id','DESC')->first();
     $data[] = ExamResultDigital::select('exam_results_digital.*','admissions.first_name as first_name','admissions.mobile as mobile')
        ->leftjoin('admissions','admissions.id','exam_results_digital.admission_id')->where('exam_results_digital.exam_id',$exam_id)->where('exam_results_digital.admission_id',$item->admission_id)->orderBy('exam_results_digital.id','DESC')->first();
 }          
    
    
    
    return view('examination.digital.exam.examResultStudentList',['data'=>$data,'exam_data'=>$examData]);
}


public function getChapters(Request $request)
{
    
     if($request->isMethod('post')){
         
         $getChapters = ChapterDigital::where('subject_id',$request->subject_id)->where('class_type_id',Session::get('class_type_id'))->get();
         
           return response()->json([
            'chapters' =>  $getChapters,
        ]);
         
     }
    
    
}

  public function digitalStartExam(Request $request){
        
        
       
                $exam_id =$request->exam_id;
                
                $exam_name = ExamDigital::where('id',$exam_id)->first();
                
        $questions_ids = ExamDigital::where('id',$exam_id)->first();
          $decode = explode(',', $questions_ids->questions_id);
          shuffle($decode);
         $questions = [] ;
         if(!empty($decode))
         {
             
            foreach($decode as $item)
            {
                
                $questions[] = QuestionDigital::select('questions_digital.*','subject.name as subject_name')
                    ->leftjoin('subject','subject.id','questions_digital.subject_id')->where('questions_digital.id',$item)->first();
            }
             
         }
        
   $total_min = ($exam_name->duration*60)+$exam_name->duration_minute ;
        
        $currentDateTime = now();
        $futureDateTime = $currentDateTime->addMinutes($total_min);
        $currentDateTimeFormatted = $currentDateTime->format('Y-m-d H:i:s');
        $futureDateTimeFormatted = $futureDateTime->format('Y-m-d H:i:s');


 
 

if(empty(Session::get('exam_'.$exam_id)) )
{
      Session::put('exam_'.$exam_id, $futureDateTimeFormatted);
}
else
{
     $exam_time = \Carbon\Carbon::parse(Session::get('exam_'.$exam_id));
     
        if($exam_time->isPast())
        {
             Session::put('exam_'.$exam_id, $futureDateTimeFormatted);
        }
}


        
        return  view('examination.digital.exam.startExam',['exam_id'=>$exam_id,'questions'=>$questions,'exam_name'=>$exam_name]);
         
            
    }
    
    public function digitalResultExam(Request $request){
         
            function convertMinutesToHisFormat($minutes)
{
    // Create a Carbon instance with the duration in minutes
    $duration = Carbon::now()->addMinutes($minutes);

    // Format the duration as "h i s"
    $formattedDuration = $duration->format('H:i');

    return $formattedDuration;
}
          if($request->isMethod('post')){
              
              $add = new ExamResultDigital;
                $add->user_id = Session::get('id');
               $add->session_id = Session::get('session_id');
               $add->branch_id = Session::get('branch_id');
              $add->total_ques = $request->total_ques;
              $add->correct_ans = $request->total_correct;
              $add->wrong_ans = $request->total_wrong;
              $add->skip_ques = $request->total_skip;
              $add->time = $request->spend_time;
              $add->admission_id = Session::get('id');
              $add->exam_id = $request->exam_id;
            //   $add->skip_ques = $request->not_visited;
              $add->result = $request->result;
              $add->subject_id_groupBy = $request->subject_id_groupBy;
              
              $add ->save();
              
        $exam = ExamDigital::where('id',$request->exam_id)->first();

                $scheduleTime =\Carbon\Carbon::createFromTimestampUTC($request->spend_time)->second;
                
                   // Create a Carbon instance with the duration in minutes

     $minutes = (int)$exam->duration;
   $exam_duration=   intdiv($minutes, 60).' hr : '. ($minutes % 60). ' min';

               Session::forget('exam_'.$request->exam_id);
               return  view('examination.digital.exam.resultExam',['data'=>$add,'per_question_marks'=>4,'spend_time'=>$scheduleTime,'exam_data'=>$exam,'exam_duration'=>$exam_duration]);
          }
         
    }
    
     public function digitalAnalysisPanel(Request $request){
         
            $search['from_date'] = $request->from_date ?? null;
         $search['to_date'] = $request->to_date ?? null;
         $search['exam_pattern'] = $request->exam_pattern ?? null;
          
         if(!empty($request->request_from_admin))
         {
             $admission_id = $request->admission_id;
         }else
         {
             $admission_id = Session::get('id');
         }
        
         $exam_id = $request->exam_id ?? '';
         
            $data = ExamResultDigital::select('exam_results_digital.*','exams_digital.name as exam_name','exams_digital.id as exam_id','exams_digital.exam_date','exams_digital.duration as exam_time')
        ->leftjoin('exams_digital','exam_results_digital.exam_id','exams_digital.id')->where('exam_results_digital.admission_id',$admission_id)->orderBy('exam_results_digital.id','DESC');
        
        if($exam_id != '')
            {
                $data = $data ->where('exam_results_digital.exam_id',$exam_id);
            }        
        $data = $data->get();
           
         
         return  view('examination.digital.exam.analysis_panel',['data'=>$data,'search'=>$search]);
         
     }
     public function digitalExamAnalysis(Request $request,$id){
         
     
              $data = ExamResultDigital::select('exam_results_digital.*','exams_digital.name as exam_name','exams_digital.id as exam_id','exams_digital.exam_date','exams_digital.duration as exam_time')
        ->leftjoin('exams_digital','exam_results_digital.exam_id','exams_digital.id')->where('exam_results_digital.id',$id)->first();
         $exam_name = ExamDigital::where('id',$data->exam_id)->first();
         $decode = json_decode($data->result);
                
          $questions = array();
            foreach ($decode as $key => $item) {
                $ques_id = QuestionDigital::where('id',$item->que_id)->first();
                $questions[] =$ques_id;
            }
            
           
         return  view('examination.digital.exam.examAnalysis',['data'=>$data,'questions'=>$questions,'exam_name'=>$exam_name,'status'=>1]);
         
     }
     public function digitalDownloadAnswerKey(Request $request)
{
    
    
    $data = ExamResultDigital::where('id' ,$request->exam_result_id)->first();
    // $exam_name= Exam::where('id' ,$data->exam_id)->first();
    
       $exam_name = ExamDigital::select('exams_digital.*','class_types.name as class_name')
        ->leftjoin('class_types','class_types.id','exams_digital.class_type_id')->where('exams_digital.id' ,$data->exam_id)->first();
    

    
     $decode = json_decode($data->result);
     $subject_decode = json_decode($data->subject_id_groupBy);
     $data_array = array();

   
     if(!empty($subject_decode))
     {
          
         
         foreach($subject_decode as $item)
         {
             
             $subject_name = Subject::where('id',$item)->first();
               $data_array1 = array();
              
             foreach($decode as $key=> $sub_item)
             {
                         
                 
               
                 if($item == $sub_item->subject_id)
                 {
                     $que_correct = QuestionDigital::where('id',$sub_item->que_id)->first();
                   
                   
 $data_array1[] = array(
                        'srno'=>$key+1,
                        'question_id'=>$sub_item->que_id,
                       'ans' =>$que_correct->question_type_id == 1 ? $que_correct->correct_ans : $que_correct->ans_a ,
                       'question_type_id'=>$que_correct->question_type_id
                   );
                   
                 }
                 
             }
             
             $data_array[] = array(
                       'subject_id' => $subject_name->name,
                       'data' => $data_array1
                   );
                   
                 }
         
     }
     
     
     
  
    
   /* $pdf = PDF::loadView('print_file.pdf.answer_key',['data'=>$data_array]);
     $pdf = $pdf->setOptions([
        'dpi' => 20,
        'isHtml5ParserEnabled' => false,
        'isPhpEnabled' => false,
        'isFontSubsettingEnabled' => false,
    ]);
   

 if (!Storage::exists('AnswerKey.pdf')) {
     Storage::put('AnswerKey.pdf', $pdf->output());
        
    }
    else
    {
       
      $pdf->save(storage_path('AnswerKey.pdf'));
      
    }
    
    $filePath = storage_path('AnswerKey.pdf');
    $fileName = 'AnswerKey.pdf';

   
    $headers = [
        'Content-Type' => 'application/octet-stream',
       
    ];

    return response()->download($filePath, $fileName, $headers);*/
    
       return view('examination.digital.exam.answer_key',['data'=>$data_array,'exam_name' =>$exam_name,'per_question_marks'=>4]);
}

  public function digitalQuestionKey(Request $request){
      
      $data = ExamResultDigital::where('id' ,$request->exam_result_id)->first();
   
    
     $exam_name = ExamDigital::select('exams_digital.*','class_types.name as class_name')
        ->leftjoin('class_types','class_types.id','exams_digital.class_type_id')->where('exams_digital.id' ,$data->exam_id)->first();
   
    
     $decode = json_decode($data->result);
     $subject_decode = json_decode($data->subject_id_groupBy);
     $data_array = array();
     
     if(!empty($subject_decode))
     {
          
         foreach($subject_decode as $item)
         {
             
             $subject_name = Subject::where('id',$item)->first();
               $data_array1 = array();
              
             foreach($decode as $key => $sub_item)
             {
               
                 if($item == $sub_item->subject_id)
                 {
                     $que_correct = QuestionDigital::where('id',$sub_item->que_id)->first();
                  
                     
 $data_array1[] = array(
                      
                        'srno'=>$key+1,
                        'question_id'=>$sub_item->que_id,
                        'question_name'=>$que_correct->name,
                        'hi_question_name'=>$que_correct->hi_name,
                        'ans_a'=>$que_correct->ans_a,
                        'ans_b'=>$que_correct->ans_b,
                        'ans_c'=>$que_correct->ans_c,
                        'ans_d'=>$que_correct->ans_d,
                        'hi_ans_a'=>$que_correct->hi_ans_a,
                        'hi_ans_b'=>$que_correct->hi_ans_b,
                        'hi_ans_c'=>$que_correct->hi_ans_c,
                        'hi_ans_d'=>$que_correct->hi_ans_d,
                        'correct_ans'=>$que_correct->correct_ans,
                        'ans' =>$que_correct->question_type_id == 1 ? $que_correct->correct_ans : $que_correct->ans_a ,
                        'user_ans' =>$sub_item->ans ,
                        'question_type_id' =>$que_correct->question_type_id ,
                       
                   );
                   
                 }
                 
             }
             
             $data_array[] = array(
                       'subject_id' => $subject_name->name,
                       'data' => $data_array1
                   );
                   
                 }
         
     }
     
    
        return  view('examination.digital.exam.analysis_question',['exam_result'=>$data,'data'=>$data_array,'exam_name' =>$exam_name,'per_question_marks'=>4]);
    }
 
} 
