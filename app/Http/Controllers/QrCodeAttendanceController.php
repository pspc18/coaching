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
use Session;
use Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Helper;
use Str;
use Response;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class QrCodeAttendanceController extends Controller
{


        
            
            public function qrcode_Dashboard(Request $request){
                
                return view('students/qrcode_attendance/qrcode_Dashboard');
            }
            
              public function qrcode_user(Request $request){
                 $allteachers = User::select('users.*')
                
                ->where('users.branch_id',Session::get('branch_id'))
                ->where('users.status',1)
                ->orderBy('users.id', 'DESC')->get();
                return view('students/qrcode_attendance/qrcode_user',['data'=>$allteachers]);
            }
           public function qrcode_student(Request $request)
{
    // Initialize $search array with default values
    $search = [
        'admissionNo' => null,
        'class_type_id' => null,
    ];

    // Populate $search array with request data if available
    if ($request->has('admissionNo')) {
        $search['admissionNo'] = $request->admissionNo;
    }
    if ($request->has('class_type_id')) {
        $search['class_type_id'] = $request->class_type_id;
    }

    // Build the query
    $query = Admission::select('admissions.*', 'class.name as class_name')
        ->leftJoin('class_types as class', 'class.id', 'admissions.class_type_id')
        ->where('admissions.session_id', Session::get('session_id'))
        ->where('admissions.branch_id', Session::get('branch_id'))
        ->where('admissions.status', 1);

    // Apply filters
    if (!empty($search['admissionNo'])) {
        $query->where('admissions.admissionNo', $search['admissionNo']);
    }
    if (!empty($search['class_type_id'])) {
        $query->where('admissions.class_type_id', $search['class_type_id']);
    }

    // Fetch students
    $allstudents = $query->orderBy('admissions.id', 'DESC')->get();

    // Apply additional filter for roles > 1
    if (Session::get('role_id') > 1) {
        $allstudents = $allstudents->where('branch_id', Session::get('branch_id'));
    }

    // Return view with data and search criteria
    return view('students/qrcode_attendance/qrcode_student', [
        'data' => $allstudents,
        'search' => $search,
    ]);
}

            
            
            public function qrcode_attendance(Request $request){
                
                return view('students/qrcode_attendance/qrcode_attendance');
            }
            
            
            
        public function qrcode_attendance_save(Request $request){
           $userType = explode("/", $request->admission_id);
         
               $date = date("Y-m-d");
               if($userType[0] == "students"){
                $admission = Admission::find($userType[1]);
                 
                    if(!empty($admission)){
                    
                            $oldData = StudentAttendance::where('admission_id',$admission->id)->where('date',$date)->where('attendance_status_id',$request->attendance_mode)->first();
                             
                            if(!empty($oldData)){
                              
                            return Response::json(array('status' => 'duplicatePunch'),200); 
                                
                            }else{
                                $attendance = new StudentAttendance;//model name
                            }  
                            $attendance->user_id = Session::get('id');
                            $attendance->session_id = Session::get('session_id');
                            $attendance->branch_id = Session::get('branch_id');
                        	$attendance->class_type_id= $admission->class_type_id;
                    		$attendance->admission_id= $admission->id;
                            $attendance->time  = date('H:i:s');
                    		$attendance->date  = $date;
                    		$attendance->attendance_status_id  = $request->attendance_mode;
                            $attendance->save();
                        
                        if($request->attendance_mode == 1){
                            return Response::json(array('status' => 'in'),200); 

                        }else{
                            return Response::json(array('status' => 'out'),200); 

                        }
                    
                    

                }    
               }else{
                   
                   $last_data = TeacherAttendance::where('staff_id',$userType[1])->where('date',$date)->where('attendance_status_id',$request->attendance_mode)->first();
                                if(!empty($last_data)){
                            return Response::json(array('status' => 'duplicatePunch'),200); 

                                }
                                else{
                                    $attendance = new TeacherAttendance;//model name
                                }
                                $attendance->user_id = Session::get('id');
                                $attendance->session_id = Session::get('session_id');
                                $attendance->branch_id = Session::get('branch_id');
                        		$attendance->staff_id= $userType[1];
                        		$attendance->date  = $date;
                        		$attendance->time  = date('H:i:s');
                        		$attendance->attendance_status_id  = $request->attendance_mode;
                        		if($request->attendance_mode == 3 || $request->attendance_mode == 5){
                        		    $attendance->current_attendance_status_id  = $request->attendance_mode;
                        		}
                        		$attendance->save(); 
                        		
                        if($request->attendance_mode == 1){
                            return Response::json(array('status' => 'in'),200); 

                        }else{
                            return Response::json(array('status' => 'out'),200); 

                        }
               }
                 
            }
 
        
        public function user_attendence_qr_download(Request $request) {
                $selectedIdsdata = $request->input('ids');
            
                // Validate input
                if (empty($selectedIdsdata)) {
                    return response()->json(['error' => 'No IDs provided'], 400);
                }
            
                // Fetch user data
                $userData = User::whereIn('id', $selectedIdsdata)->get();
            
                $user = ''; 
                foreach ($userData as $val) {
                    // Generate QR code as raw SVG
                    $qrCode = QrCode::size(150)->generate('user/' . $val->id);
            
                    // Construct the HTML with div elements
                    $user .= '<div class="col-md-2 mb-3">';
                    $user .= '<div class="user-qr-code text-center">' . $qrCode . 
                    '<br>'.$val->first_name . ' ' . $val->last_name.
                    '</div>'; // Insert the raw SVG directly
                    $user .= '</div>';
                } 
            
                // Return as JSON response
                return response()->json(['html' => $user]);
            }
            public function student_attendence_qr_download(Request $request) {
                
                    $selectedIdsdata = $request->input('ids');
                
                    if (empty($selectedIdsdata)) {
                        return response()->json(['error' => 'No IDs provided'], 400);
                    }
                
                    $studentData = Admission::whereIn('id', $selectedIdsdata)->get();
                
                    $student = ''; 
                    foreach ($studentData as $val) {
                        $qrCode = QrCode::size(160)->generate('student/' . $val->id);
                        
                        $student .= '<div class="col-md-4 mb-3">';
                        $student .= '<div class="student-qr-code text-center">'.'<span>' . $qrCode . '</span>'.
                        '<br>'.$val->first_name . ' ' . $val->last_name.
                        '</div>'; 
                        $student .= '</div>';
                    }
                
                    return response()->json(['html' => $student]);
                }



            }
