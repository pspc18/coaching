<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\StudentAttendance;
use App\Models\AttendanceStatus;
use App\Models\Admission;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon\Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class StudentAttendanceController extends BaseController
{
    public function monthWise(Request $request){
        
        $admission_id = $request->admission_id;
        $month = $request->month;
        $year = $request->year;
        
         if($request->isMethod('post')){
         	$atnrecord = StudentAttendance::where('admission_id',$admission_id)->whereMonth('student_attendance.date',$month)->whereYear('student_attendance.date',$year)
               	             ->groupby('student_attendance.date')->get();
               	    
               	    
       	 //   dd($atnrecord);
    	$data = array();
    	$data1 = array();
    	
    	$color = '';
    	
        $first_date = Carbon::now()->startOfMonth();
        $today_date = Carbon::now();
        $last_date = Carbon::now()->endOfMonth();
        
        $totalDaysTillDate = 0;
        $totalDays = 0;

        for ($date = $first_date; $date->lte($today_date); $date->addDay()) {
            if ($date->dayOfWeek != Carbon::SUNDAY) {
                $totalDaysTillDate++;
            }
        }
        
        $first_date = Carbon::now()->startOfMonth();
    

        for ($date = $first_date; $date->lte($last_date); $date->addDay()) {
            if ($date->dayOfWeek != Carbon::SUNDAY) {
                $totalDays++;
            }
        }

        
        $attendanceDetails['totalDaysTillDate'] = $totalDaysTillDate;
        $attendanceDetails['totalDays'] = $totalDays;
        
        $attendanceDetails['present'] = 0;
        $attendanceDetails['absent'] = 0;
        $attendanceDetails['halfday'] = 0;
        $attendanceDetails['holiday'] = 0;
        
            foreach ($atnrecord as $key => $item) {
                if($item->attendance_status_id == 3)
                {
                    	$color = 'blue';
                }
                elseif($item->attendance_status_id == 1)
                {
                    	$color = 'green';
                    	$attendanceDetails['present']++;
                }
                elseif($item->attendance_status_id == 2)
                {
                    	$color  = 'red';
                    	$attendanceDetails['absent']++;
                }
                elseif($item->attendance_status_id == 5)
                {
                    	$color  = 'skyblue';
                    	$attendanceDetails['holiday']++;
                }
                elseif($item->attendance_status_id == 4)
                {
                    	$color ='orange';
                    	
                    	 $attendanceDetails['halfday']++;
                }
                else
                {
                    $color='';
                }
                
                 $data1[0] = array(
                    'selected' => $color== '' ? false :true,
                    'selectedColor' =>$color
                );
                $data[] = array(
                    $item->date => $data1[0]
                );
            }
            
            if(!empty($data))
            {
                	return response()->json(['status' => true, 'message' => 'Attendance Fetched Successfully','data'=>$data,'attendanceDetails'=>$attendanceDetails], 200);
            }
    	else
    	{
    	    	return response()->json(['status' => false,'totalDaysTillDate'=>$totalDaysTillDate,'totalDays'=>$totalDays,'message' => 'No Data Found','data'=>[],'attendanceDetails'=>[]], 200);
    	}

         }
 
    }
    public function view(Request $request){
        
     
        $search['name'] = $request->name;
        $search['date'] = !empty($request->date) ? $request->date : date("m");
        $current_date = !empty($request->date) ? $request->date : date("m");
        
            $curr_yrs = date('Y');	
    		$curr_mnt = $current_date;	

    		$data['monthDate'] = date('t', mktime(0, 0, 0, $curr_mnt, 1, $curr_yrs));
    		$totel_month_day = $data['monthDate'];

    
        $all_student= Admission::where('branch_id',1)->where('status','1')->where('school',1)->where('id',1);
   
    		 if($request->isMethod('post')){
		    
    	}
    		
    		$all_admissions = $all_student->get()->toArray();
    		$atnrecord =array();
    		$work_from_home =array();
    		$present =array();
    		$holiday =array();
    		$absent =array();
    		$halfday =array();
    		if(!empty($all_admissions)){
    		    
        		foreach ($all_admissions as $key => $staff_record) {
        		    
        		    	$atnrecord[] =StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name' ,'admissions.image as photo')
                        ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                        
              	    ->where('student_attendance.admission_id',$staff_record['id'])->whereMonth('student_attendance.date',$curr_mnt)->whereYear('student_attendance.date',$curr_yrs)->groupby('student_attendance.date')->get(['date','admission_id','attendance_status_id']);
    	
    	
    	$data1=StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name','admissions.image as photo')
                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                       
                        
                        ->where('student_attendance.admission_id',$staff_record['id'])->where('student_attendance.date',$request->date1)->where('student_attendance.attendance_status_id',3)->get(['date','admission_id','attendance_status_id']);
    	       
    	       
    	       
    	          if(count($data1) > 0)
    	          {
    	              $work_from_home[] = $data1;
    	          }
    	     
    	     $data2=StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name','admissions.image as photo')
                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                    
                        
                        ->where('student_attendance.admission_id',1)->where('student_attendance.date',1)->where('student_attendance.attendance_status_id',13)->get(['date','admission_id','attendance_status_id']);
    	            
    	           if(count($data2)> 0)
    	          {
    	              $present[] = $data2;
    	          }
    	          
    	         $data3=StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name','admissions.image as photo')
                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                       
                        ->where('student_attendance.admission_id',$staff_record['id'])->where('student_attendance.date',$request->date1)->where('student_attendance.attendance_status_id',14)->get(['date','admission_id','attendance_status_id']);
    	       
    	           if(count($data3)> 0)
    	          {
    	              $absent[] = $data3;
    	          }
    	            
    	            $data4=StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name','admissions.image as photo')
                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                          
                        ->where('student_attendance.admission_id',$staff_record['id'])->where('student_attendance.date',$request->date1)->where('student_attendance.attendance_status_id',5)->get(['date','admission_id','attendance_status_id']);
    	       
    		   if(count($data4)> 0)
    	          {
    	              $holiday[] = $data4;
    	          }
        		   
        		   $data5=StudentAttendance::Select('student_attendance.*','admissions.first_name','admissions.last_name','admissions.image as photo')
                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                         
                        ->where('student_attendance.admission_id',$staff_record['id'])->where('student_attendance.date',$request->date1)->where('student_attendance.attendance_status_id',4)->get(['date','admission_id','attendance_status_id']);
    	       
    		   if(count($data5)> 0)
    	          {
    	              $halfday[] = $data5;
    	          }
        		   
        		}
    		}
    	

    		 
    		$AttStatus = AttendanceStatus::markable()->get()->keyBy('id')->toArray();
    		
		return response()->json(['status' => true, 'message' => 'Attendance Fetched Successfully','data'=>$atnrecord,'present'=>$present,'work_from_home'=>$work_from_home,'absent'=>$absent,'halfday'=>$halfday,'holiday'=>$holiday,'all_admissions'=>$all_admissions,'AttStatus'=>$AttStatus,'curr_yrs'=>$curr_yrs,'curr_mnt'=>$curr_mnt,'totel_month_day'=>$totel_month_day, 'search'=>$search], 200);

    }
    
    
       public function add(Request $request){

       if($request->isMethod('post')){
           
    
   

           
          $admission_id =$request->admission_id;
           $status =$request->status;
        //   $name =$request->first_name;
        //   $mobile =$request->mobile;
        //   $email =$request->email;
          $dateTime = date('l jS \of F Y h:i:s A');

            if(!empty($admission_id)){

                         $keys = array_keys($admission_id);
          
          	    $data = array();
          	    
          	   
          foreach ($admission_id as $key => $item) {
                           
                                
                                $last_data = StudentAttendance::where('admission_id',$item)->where('date',$request->date)->get()->first();
                               
                                  if(!empty($last_data)){
                                      $attendance = $last_data;
                                    }else{
                                      $attendance = new StudentAttendance;//model name
                                  }  
                                   
                                $attendance->user_id = 1;
                                $attendance->session_id = 3;
                                $attendance->branch_id = 1;
                        		$attendance->admission_id= $item;
                        		$attendance->date  = $request->date;
                        		$attendance->attendance_status_id  = $status[$key];
                        		$attendance->save();
                        // 		if(!empty($name)){
                        // 		$attendance->name= $name[$count];
                        // 		}
                        // 		if(!empty($mobile)){
                        // 		$attendance->mobile= $mobile[$count];
                        // 		}
                        		
                        // 		$oldData = StudentAttendance::select('student_attendance.*','teachers.mobile','teachers.first_name','teachers.email','attendance_status.name','teachers.last_name')
                        //                                     ->leftjoin('admissions','admissions.id','student_attendance.admission_id')
                        //                                     ->leftjoin('attendance_status','attendance_status.id','student_attendance.attendance_status_id')
                        //                                     ->where('student_attendance.admission_id',$item)
                        //                                     ->get()->first();
                        		
                        	     		
                        	
                          
                        }  
                        
                 	return response()->json(['status' => true, 'message' => 'Attendance Successfully'], 200);
            }
            	return response()->json(['status' => false, 'message' => 'Attendance Unsuccessfully','data'=>$admission_id, 'attendanceStatus'=>$status],200);
        }
    
     
    }
    
    
}
