<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\Salary;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\TeacherAttendance;
use App\Models\TeacherCategory;
use App\Models\StudentAttendance;
use App\Models\Teacher;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Weekendcalendar;
use App\Models\AttendanceStatus;
use App\Models\Setting;
use App\Models\Master\Branch;
use App\Models\MessageQueue;
use App\Jobs\SendMessageJob;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{

 // View Attendance Page
    public function view(Request $request)
    {
        $search['name'] = $request->name;
        $search['class_type_id'] = $request->class_type_id;
        $search['admissionNo'] = $request->admissionNo;
        $search['date'] = !empty($request->date) ? $request->date : date("m");
        $search['year'] = !empty($request->year) ? $request->year : date("Y");

        $allStudents = Admission::where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'));

        if (Session::get('role_id') == 3) {
            $allStudents = $allStudents->where('id', Session::get('id'));
        }

        $allStudents = $allStudents->orderBy('first_name', 'ASC')->get();

        // AJAX fetch for table view
        if ($request->isMethod('post')) {
            $data = json_decode($request->data);
            $attendance = [];
            if (!empty($data)) {
                foreach ($data as $key => $item) {
                    if ((intval($request->loop['from']) <= $key) && (intval($request->loop['to']) >= $key)) {
                        $attendance[] = StudentAttendance::where('admission_id', $item->id)
                            ->whereIn('date', $item->date)
                            ->get();
                    }
                }
            }
            return response()->json(['data' => $attendance]);
        }

        if (Session::get('role_id') == 3) {
            return view('students.attendance.studentPanelAttendanceView');
        } elseif (Session::get('role_id') == 1) {
            return redirect()->to('studentsAttendanceViewTable');
        } else {
            return view('students.attendance.attendance_view', [
                'search' => $search,
                'allStudents' => $allStudents
            ]);
        }
    }

    // Get Attendance Data for a Student
    public function getAttendanceDates(Request $request)
    {
        $admission_id = $request->admission_id ?? '';
        $month = $request->month ?? '';
        $user = $request->user ?? 'Student';
        $data = [];

        if ($user == 'Student') {
            $data = StudentAttendance::select('student_attendance.*', 'attendance_status.name as atten_status')
                ->leftJoin('attendance_status', 'student_attendance.attendance_status_id', 'attendance_status.id')
                ->where('student_attendance.session_id', Session::get('session_id'))
                ->where('student_attendance.branch_id', Session::get('branch_id'))
                ->where('student_attendance.admission_id', $admission_id)
                ->whereMonth('student_attendance.date', '=', $month)
                ->get();
        }

        $attendance = [];
        $total = [
            'Present' => 0,
            'Absent' => 0,
            'Holiday' => 0,
            'Leave' => 0,
            'Event' => 0,
            'Exam' => 0
        ];
//dd($data);
        if (!empty($data)) {
            foreach ($data as $item) {
                $attendance[$item->date] = $item->atten_status;

                switch ($item->atten_status) {
                    case 'In':
                    case 'Present':
                    case 'Out':
                        $total['Present'] += 1;
                        break;
                    case 'Absent':
                        $total['Absent'] += 1;
                        break;
                    case 'Holiday':
                        $total['Holiday'] += 1;
                        break;
                    case 'Leave':
                        $total['Leave'] += 1;
                        break;
                    case 'Event':
                        $total['Event'] += 1;
                        break;
                    case 'Exam':
                        $total['Exam'] += 1;
                        break;
                }
            }
        }

        return response()->json(['data' => $attendance, 'total' => $total]);
    }





















        
/*            public function sundayAutoSubmitAttendance(Request $request){
                $sundays = collect();
                $currentDate = Carbon::now();
                $firstDayOfMonth = $currentDate->copy()->startOfMonth();
                $lastDayOfMonth = $currentDate->copy()->endOfMonth();
                // Loop through the month to find all Sundays
                for ($date = $firstDayOfMonth; $date->lte($lastDayOfMonth); $date->addDay()) {
                    if ($date->isSunday()) {
                        $sundays->push($date->format('Y-m-d'));
                    }
                }
                $admissionNo = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
                    if(!empty($admissionNo)){
                       foreach($sundays as  $date)
                       {
                       foreach($admissionNo as $key=>  $item){
                            $oldData = StudentAttendance::where('admission_id',$item->id)->where('date',$date)->first();
                            if(!empty($oldData)){
                                $attendance = $oldData;
                            }else{
                                $attendance = new StudentAttendance;//model name
                            }  
                            $attendance->user_id = Session::get('id');
                            $attendance->session_id = Session::get('session_id');
                            $attendance->branch_id = Session::get('branch_id');
                        	$attendance->class_type_id= $item->class_type_id;
                    		$attendance->admission_id= $item->id;
                            //  $attendance->time  = date('H:i:s');
                    		$attendance->date  = $date;
                    		$attendance->attendance_status_id  = 5;
                            $attendance->save();
                        } 
                    }
                }    
            }
*/            

/*            public function autoStudentAttendance(Request $request){
                $attendance_status = $request->attendanceStatus; 
                //dd($request->dateOfEvent);
                $dateOfEvent = Carbon::createFromFormat('Y-m-d', $request->date);
                $event = Weekendcalendar::whereDate('date',$dateOfEvent)->first();
                $admissionNo = Admission::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
                if(!empty($admissionNo)){
                    $dateTime = date('l jS \of F Y h:i:s A');
                    foreach($admissionNo as $key=>  $item){
                        $oldData = StudentAttendance::where('admission_id',$item->id)->where('date',$dateOfEvent)->first();
                        if(!empty($oldData)){
                            $attendance = $oldData;
                        }else{
                            $attendance = new StudentAttendance;//model name
                        }  
                        $attendance->user_id = Session::get('id');
                        $attendance->session_id = Session::get('session_id');
                        $attendance->branch_id = Session::get('branch_id');
                    	$attendance->class_type_id= $item->class_type_id;
                		$attendance->admission_id= $item->id;
                        //$attendance->time  = date('H:i:s');
                		$attendance->date  = $dateOfEvent;
                		$attendance->attendance_status_id  = $attendance_status;
                        if(!empty($event))
                        {
                            $attendance->save();
                        }
                    } 
                    if(!empty($event)){
                        $event->is_attendance_submitted = 1;
                        $event->save();
                    }
                }    
            }
*/                public function add(Request $request)
                {
                    if ($request->isMethod('post')) {
                
                        $attendanceDate   = $request->custom_date;
                        $selectedStudents = $request->selected_students ?? []; // केवल checked students
                        $allAdmissionIds  = $request->admission_id; // सभी students (hidden input)
               
                        if (empty($allAdmissionIds)) {
                            return response()->json(['error' => 'No students found!'], 422);
                        }
                
                        foreach ($allAdmissionIds as $admissionId) {
                
                            // ✅ अगर checkbox checked है → save/update
                            if (isset($selectedStudents[$admissionId])) {
                
                                $oldData = StudentAttendance::where('admission_id', $admissionId)
                                    ->where('date', $attendanceDate)
                                    ->first();
                
                                if (!empty($oldData)) {
                                    $attendance = $oldData; // update existing
                                } else {
                                    $attendance = new StudentAttendance; // create new
                                    $attendance->admission_id = $admissionId;
                                }
                
                                $attendance->user_id        = Session::get('id');
                                $attendance->session_id     = Session::get('session_id');
                                $attendance->branch_id      = Session::get('branch_id');
                                $attendance->class_type_id  = $request->class_type_id;
                                $attendance->date           = $attendanceDate;
                                $attendance->attendance_type = 'manual';
                
                                $status = $request->attendance_status[$admissionId] ?? 1;
                
                                if ($status == 2) {
                                    $attendance->out_time = now()->format('H:i:s');
                                } else {
                                    $attendance->time = now()->format('H:i:s');
                                }
                
                                $attendance->attendance_status_id = $status;
                                $attendance->save();
                
                                // Student Info
                                $name   = $request->name[$admissionId] ?? '';
                                $mobile = $request->mobile[$admissionId] ?? '';
                
                                // Attendance Status
                                $AttendanceStatus = AttendanceStatus::find($status);
                
                                // Message Template
                                $template = MessageTemplate::select('message_templates.*', 'message_types.slug','message_types.status as message_type_status')
                                    ->leftJoin('message_types', 'message_types.id', 'message_templates.message_type_id')
                                    ->where('message_types.slug', 'attendance')
                                    ->first();
                
                                $setting = Setting::where('branch_id', Session::get('branch_id'))->first();
                
                                // Replace values
                                $arrey1 = ['{#school_name#}', '{#today_day#}', '{#attendance_status#}', '{#name#}', '{#support_no#}'];
                                $arrey2 = [
                                    $setting->name,
                                    date('d-m-Y', strtotime($attendanceDate)),
                                    $AttendanceStatus->name ?? '',
                                    $name,
                                    $setting->mobile
                                ];
                
                                
                               
                                 $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                 
                                 
                                        if ($setting->firebase_notification == 1) {
                                            Helper::sendNotification(
                                                $template->title ?? 'Attendence',
                                                $whatsapp,
                                                'student',
                                                $request->admission_id[$admissionId]
                                            ); 
                                        }
                                    if ($template->message_type_status == 1) {
                                         if ($branch->whatsapp_srvc == 1) {
                                            if (!empty($mobile)) {
                                                Helper::MessageQueue($mobile, $whatsapp);
                                            }
                                        }
                                     }
                              
                
                
                            } else {
                                // ❌ Checkbox unchecked → Delete old record
                                StudentAttendance::where('admission_id', $admissionId)
                                    ->where('date', $attendanceDate)
                                    ->delete();
                            }
                        }
                
                        return response()->json(['success' => true, 'message' => 'Attendance updated successfully!']);
                    }
                
                                    return view('students/attendance/attendance_add');
                }




            public function studentPanelAttendanceView(Request $request){
                return view('students/attendance/studentPanelAttendanceView');
            }
            


public function viewTable(Request $request)
{
    $search['name'] = $request->name;
    $search['class_type_id'] = $request->class_type_id;
    $search['admissionNo'] = $request->admissionNo;
    $search['date'] = !empty($request->date) ? $request->date : date("m");
    $search['year'] = !empty($request->year) ? $request->year : date("Y");

    $allStudents = Admission::where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'));

    if (Session::get('role_id') == 3) {
        $allStudents = $allStudents->where('id', Session::get('id'));
    }

    $allStudents = $allStudents->orderBy('first_name', 'ASC')->get();

    // ✅ Weekend dates (publish=1 and <= today)
    $weekendDates = WeekendCalendar::where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'))
        ->whereDate('date', '<=', now()->toDateString())
        ->get(['date', 'attendance_status'])
        ->map(function ($item) {
            return [
                'date' => $item->date,
                'attendance_status' => $item->attendance_status ?? 5, // अगर null तो default 5
            ];
        })
        ->toArray();

    if ($request->isMethod('post')) {
        $data = json_decode($request->data);
        $attendance = [];

        if (!empty($data)) {
            foreach ($data as $key => $item) {
                if ((intval($request->loop['from']) <= $key) && (intval($request->loop['to']) >= $key)) {

                    // 🔹 Weekend Holidays Auto Insert
                    foreach ($weekendDates as $weekend) {
                        $exists = StudentAttendance::where('admission_id', $item->id)
                            ->whereDate('date', $weekend['date'])
                            ->exists();

                        if (!$exists) {
                            $add = Admission::where('id',$item->id)->first(['class_type_id','session_id','branch_id']);
                            $attendanceRow = new StudentAttendance();
                            $attendanceRow->user_id = Session::get('id');
                            $attendanceRow->session_id = $add->session_id;
                            $attendanceRow->branch_id = $add->branch_id;
                            $attendanceRow->admission_id = $item->id; 
                            $attendanceRow->date = $weekend['date'];
                            $attendanceRow->attendance_status_id = $weekend['attendance_status']; // default 5 (Holiday)
                            $attendanceRow->time = now()->format('H:i:s');;
                            $attendanceRow->attendance_type = 'cron'; 
                            $attendanceRow->class_type_id = $add->class_type_id;
                            $attendanceRow->save();
                        }
                    }

                    // 🔹 Fetch Attendance (after holiday insert)
                    $attendance[] = StudentAttendance::where('admission_id', $item->id)
                        ->whereIn('date', $item->date)
                        ->get();
                }
            }
        }

        return response()->json(['data' => $attendance]);
    }

    return view('students/attendance/attendance_view_table_format', [
        'search' => $search,
        'allStudents' => $allStudents
    ]);
}


            public function SearchValueAtten(Request $request)
                    {

                        $query = Admission::with('ClassTypes')
                            ->where('branch_id', Session::get('branch_id'))
                            ->where('session_id', Session::get('session_id'))
                            ->where('status', 1);
                    
                        // Class filter only
                        if (!empty($request->class_type_id)) {
                            $query->where('class_type_id', $request->class_type_id);
                        }
                    
                        $allStudents = $query->orderBy('first_name', 'ASC')->get();
                    
                        return view('students.attendance.attendance_Search', [
                            'data'        => $allStudents,
                            'custom_date' => $request->custom_date,
                        ]);
                    }
  
           
         
            }
