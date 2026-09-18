<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator;
use App\Models\User;
use App\Models\Admission;
use App\Models\exam\Question;
use App\Models\exam\Exam;
use App\Models\StudentGrow;
use App\Models\Subject;
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
use App\Models\exam\ExamResult;
use App\Models\exam\FillMinMaxMarks;
use App\Models\exam\FillMarks;
use App\Models\exam\ExamResultDetail;
use App\Models\exam\AssignQuestion;
use App\Models\exam\AssignExam;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
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

class ExaminationController extends Controller

{
          
   
    

            public function examinationDashboard(){
                return view('examination.examination_dashboard ');
            }
            











            




        
      

        
    
    
} 