<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Salary;
use App\Models\TeacherCategory;
use App\Models\Teacher;
use App\Models\SmsSetting;
use App\Models\WhatsappSetting;
use App\Models\Master\MessageTemplate;
use App\Models\Master\Weekendcalendar;
use App\Models\Master\Branch;
use App\Models\Setting;
use App\Models\AttendanceStatus; 
use App\Models\TeacherAttendance;
use Session;
use Hash;
use Helper;
use Str;
use Response ;
use Redirect;
use Carbon\Carbon;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffAttendanceController extends Controller

{
            public function autoStaffAttendance(Request $request){
                $attendance_status = $request->attendance_status; 
                //dd($request->dateOfEvent);
                // $dateOfEvent = Carbon::createFromFormat('Y-m-d', $request->date);
                $validatedData = $request->validate([
                    'date' => 'required|date_format:Y-m-d',
                ]);   
                if ($request->has('date') && !empty($request->date)) {
                    $dateOfEvent = Carbon::createFromFormat('Y-m-d', $request->date);
                }
                else {
                    // Handle missing date case
                    return response()->json(['error' => 'Date is missing'], 400);
                }
                $event = Weekendcalendar::whereDate('date',$dateOfEvent)->first();
                $staff_id = Teacher::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->get();
                if(!empty($staff_id)){
                    $dateTime = date('l jS \of F Y h:i:s A');
                    foreach($admissionNo as $key=>  $item){
                        $oldData = TeacherAttendance::where('staff_id',$item->id)->where('date',$dateOfEvent)->first();
                        if(!empty($oldData)){
                            $attendance = $oldData;
                        }
                        else{
                            $attendance = new TeacherAttendance;//model name
                        }  
                        $attendance->user_id = Session::get('id');
                        $attendance->session_id = Session::get('session_id');
                        $attendance->branch_id = Session::get('branch_id');
                    	$attendance->class_type_id= $item->class_type_id;
                		$attendance->staff_id= $item->id;
                        //$attendance->time  = date('H:i:s');
                		$attendance->date  = $dateOfEvent;
                		$attendance->attendance_status_id  = $attendance_status;
                        if(!empty($event)){
                            $attendance->save();
                        }
                    	
                    } 
                    if(!empty($event)){
                        $event->is_attendance_submitted = 1;
                        $event->save();
                    }
                }    
            }

            public function add(Request $request){
                if($request->isMethod('post')){
                   $teacher_id =$request->staff_id;
                   $attendance_status = $request->attendance_status;
                   $name =$request->first_name;
                   $mobile =$request->mobile;
                   $email =$request->email;
                   //$dateTime = date('l jS \of F Y h:i:s A');
                    if(!empty($teacher_id)){
                        for($count = 0; $count <= count($teacher_id); $count++){
                            if(isset($teacher_id[$count])){
                                if($attendance_status[$count] == 3 || $attendance_status[$count] == 5){
                                    TeacherAttendance::where('staff_id',$teacher_id[$count])->where('date',$request->date)->where('branch_id', Session::get('branch_id'))->delete();
                                }
                                 $last_data = TeacherAttendance::where('staff_id',$teacher_id[$count])->where('date',$request->date)->get()->first();
                                if(!empty($last_data)){
                                    $attendance = $last_data;
                                }
                                else{
                                                                  $attendance = new TeacherAttendance;//model name
                                }
                                $attendance->user_id = Session::get('id');
                                $attendance->session_id = Session::get('session_id');
                                $attendance->branch_id = Session::get('branch_id');
                        		$attendance->staff_id= $teacher_id[$count];
                        		$attendance->date  = $request->date;
                        		$attendance->time  = $request->time;
                        		$attendance->attendance_status_id  = $attendance_status[$count];
                        		if($attendance_status[$count] == 3 || $attendance_status[$count] == 5){
                        		    $attendance->current_attendance_status_id  = $attendance_status[$count];
                        		}
                        		$attendance->save();
                        		if(!empty($name)){
                        		    $attendance->name= $name[$count];
                        		}
                        		if(!empty($mobile)){
                        		    $attendance->mobile= $mobile[$count];
                        		}
                        		$oldData = TeacherAttendance::select('teacher_attendance.*','users.mobile','users.first_name','users.email','attendance_status.name as attendance_status_name','users.last_name')
                                ->leftjoin('users','users.id','teacher_attendance.staff_id')
                                ->leftjoin('attendance_status','attendance_status.id','teacher_attendance.attendance_status_id')
                                ->where('teacher_attendance.staff_id',$teacher_id[$count])
                                ->where('teacher_attendance.date',$request->date)
                                ->whereNull('teacher_attendance.deleted_at')
                                ->get()->first();
                              
                        		$template =  MessageTemplate::Select('message_templates.*','message_types.slug','message_types.status as message_type_status')
                                ->leftjoin('message_types','message_types.id','message_templates.message_type_id')
                                ->where('message_types.slug','teacher-attendance')->first();
                                $branch = Branch::find(Session::get('branch_id'));
                                $setting = Setting::where('branch_id',Session::get('branch_id'))->first();
                                $arrey1 = array(
                               '{#teacher_name#}',
                               '{#today_day#}',
                               '{#attendance_status#}',
                               '{#school_name#}',
                               '{#school_query_no#}');
                                $arrey2 = array(
                                $oldData->first_name." ".$oldData->last_name,
                                date('d-m-Y',strtotime($request->date)),
                                $oldData->attendance_status_name,
                                $setting->name,
                                $setting->mobile,);
                                
                                 $whatsapp = str_replace($arrey1, $arrey2, $template->whatsapp_content ?? '');
                                 
                                 
                                        if ($setting->firebase_notification == 1) {
                                            Helper::sendNotification(
                                                $template->title ?? 'Attendence',
                                                $whatsapp,
                                                'user',
                                                $oldData->staff_id
                                            ); 
                                        }
                                    if ($template->message_type_status == 1) {
                                         if ($branch->whatsapp_srvc == 1) {
                                            if (!empty($mobile)) {
                                                Helper::MessageQueue($mobile, $whatsapp);
                                            }
                                        }
                                     }
                                
                            }    		
                                
                        }  
                         return response()->json(['status' => 'success','message' => 'Attendance Added Successfully.',]);          
                    }
                    return redirect::to('staff_attendance_add')->with('error', 'Please Select Teacher !');  
                }
                $allteachers = User::select('users.*')
                ->where('users.branch_id',Session::get('branch_id'))
                ->where('users.status',1)
                ->orderBy('users.id', 'DESC')->get();
                return view('staff/staff_attendance/add',['data'=>$allteachers]);
            }
    
            public function searchValueStaffAtten(Request $request){
                $search['name'] = $request->name;
                $data =   User::select('users.*')->where('branch_id',Session::get('branch_id'))->where('status',1);
                if($request->isMethod('post')){
                    $request->validate([]);
                    if(!empty($request->name)){
                        $staff = $data ->where("first_name", 'like', '%' .$request->name. '%')
                        ->orWhere("last_name", 'like', '%' .$request->name. '%')
                        ->orWhere("father_name", 'like', '%' .$request->name. '%')
                        ->orWhere("mobile", 'like', '%' .$request->name. '%')
                        ->orWhere("email", 'like', '%' .$request->name. '%')
                        ->orWhere("aadhaar", 'like', '%' .$request->name. '%');
                    }
                }
                $allstaff = $staff->orderBy('id','DESC')->get();
                return  view('staff/staff_attendance/attendance_Search',['data'=>$allstaff]);
            }     
    
            public function view(Request $request){
                $search['name'] = $request->name;
                $search['date'] = !empty($request->date) ? $request->date : date("m");
                $search['year'] = !empty($request->year) ? $request->year : date("Y");
                $current_date = !empty($request->date) ? $request->date : date("m");
                $curr_yrs = !empty($request->year) ? $request->year : date("Y");	
        		$curr_mnt = $current_date;	
        		$data['monthDate'] = date('t', mktime(0, 0, 0, $curr_mnt, 1, $curr_yrs));
        		$totel_month_day = $data['monthDate'];
            	if(Session::get('role_id') == 2){
    		        $all_staff=   User::select('users.*')
                    ->where('users.branch_id',Session::get('branch_id'))->where('users.id',Session::get('id'))->where('users.status','1');
                }
                else{
                    $all_staff= 
                     User::select('users.*')
                    ->where('users.branch_id',Session::get('branch_id'))
                    ->where('users.branch_id',Session::get('branch_id'))->where('users.status','1');
                }
    		    if($request->isMethod('post')){
		            if(!empty($request->name)){
		                $value = $request->name;
    		    	    $all_staff =  $all_staff->where(function($query) use ($value){
		    	            $query->where("users.first_name", 'like', '%' .$value. '%');
                            $query->orWhere("users.last_name", 'like', '%' .$value. '%');
                            $query->orWhere("users.father_name", 'like', '%' .$value. '%');
                            $query->orWhere("users.mobile", 'like', '%' .$value. '%');
                            $query->orWhere("users.email", 'like', '%' .$value. '%');
    		    	    }); 
    		        }
    	        }
        		$all_teachers = $all_staff->orderBy('first_name')->get()->toArray();
        		$atnrecord =array();
    		    if(!empty($all_teachers)){
        		    foreach ($all_teachers as $key => $staff_record) {
    			        $atnrecord[$staff_record['id']] = TeacherAttendance::where('teacher_attendance.staff_id',$staff_record['id'])->whereMonth('teacher_attendance.date',$curr_mnt)->whereYear('teacher_attendance.date',$curr_yrs)->groupby('teacher_attendance.date')->get(['date','staff_id','attendance_status_id'])->keyBy('date')->toArray();
    		        }
    		    }
    		    $AttStatus = AttendanceStatus::markable()->get()->keyBy('id')->toArray();
    		    if(Session::get('role_id') == 2 ){
                    return view('staff/staff_attendance/singleView',['data'=>$atnrecord,'all_teachers'=>$all_teachers,'AttStatus'=>$AttStatus,'curr_yrs'=>$curr_yrs,'curr_mnt'=>$curr_mnt,'totel_month_day'=>$totel_month_day, 'search'=>$search]);
    		    }
    		    else{
    		      return view('staff/staff_attendance/index',['data'=>$atnrecord,'all_teachers'=>$all_teachers,'AttStatus'=>$AttStatus,'curr_yrs'=>$curr_yrs,'curr_mnt'=>$curr_mnt,'totel_month_day'=>$totel_month_day, 'search'=>$search]);
    		    }
    		}
    
            public function StaffCategiryData(Request $request,$id){
                $data['data'] = Teacher::where('teacher_categorie_id',$id)->get();
                $data['AttendanceStatus'] = AttendanceStatus::markable()->where('branch_id',Session::get('branch_id'))->orderBy('id', 'ASC')->get();
               return view('staff/staff_attendance/pagination_list',['data'=>$data]);
            }
   
            public function staff_attendance_save(Request $request){
                $Update = '';
                if($request->teacher_id){
                    $attendance = TeacherAttendance::where('teacher_id',$request->teacher_id)->where('date',$request->date)->where('branch_id', Session::get('branch_id'))->get()->first();
                    if(!empty($attendance) && $attendance != []){
                        $attendance = TeacherAttendance::where('teacher_id',$request->teacher_id)->where('date',$request->date)->where('branch_id', Session::get('branch_id'))->get()->first();
                       
                    }
                    else{
                        $attendance = new TeacherAttendance;//model name
                        $Update = 2;
                    }
                    //$attendance = new TeacherAttendance;//model name
        	        $attendance->user_id = Session::get('id');
        	        $attendance->session_id = Session::get('session_id');
                    $attendance->branch_id = Session::get('branch_id');
        			$attendance->teacher_id =$request->teacher_id;
        			$attendance->attendance_status_id =$request->attendance_id;
        			$attendance->date =date('Y-m-d');
        		    $attendance->save();
        		    if($Update == 2){
        		        return Response::json(array('stutes' => 2)); 
        		    }
        		    else{
        		        return Response::json(array('stutes' => 1)); 
        		    }
                }else{
                return Response::json(array( 'stutes' => 0)); 
            }
               
        }
        
        
   
}
