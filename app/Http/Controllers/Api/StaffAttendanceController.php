<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
use App\Models\AttendanceStatus;
use Validator;
use Hash;
use File;
use App;
use URL;
use Image;
use Carbon;
use Str;
use App\Helpers\helpers;
use Mail;


class StaffAttendanceController extends BaseController
{
    public function monthWise(Request $request){
        
        $teacher_id = $request->teacher_id;
        $month = $request->month;
        $year = $request->year;
        
         if($request->isMethod('post')){
             	$atnrecord =TeacherAttendance::where('teacher_id',$teacher_id)->whereMonth('teacher_attendance.date',$month)->whereYear('teacher_attendance.date',$year)
               	    ->groupby('teacher_attendance.date')->get();
               	    
               	    
               	 //   dd($atnrecord);
    	$data = array();
    	$data1 = array();
    	
    	$color = '';
            foreach ($atnrecord as $key => $item) {
                if($item->attendance_status_id == 3)
                {
                    	$color = 'blue';
                }
                elseif($item->attendance_status_id == 13)
                {
                    	$color = 'green';
                }
                elseif($item->attendance_status_id == 14)
                {
                    	$color  = 'red';
                }
                elseif($item->attendance_status_id == 5)
                {
                    	$color  = 'skyblue';
                }
                elseif($item->attendance_status_id == 4)
                {
                    	$color ='orange';
                }
                else
                {
                    $color='';
                }
                
                 $data1[0] = array(
                    'selected' => true,
                    'selectedColor' =>$color
                );
                $data[] = array(
                    $item->date => $data1[0]
                    
                );
            }
    		return response()->json(['status' => true, 'message' => 'Attendance Fetched Successfully','data'=>$data], 200);

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

    // 	if(Session::get('role_id') == 2){
    // 		$all_staff= Teacher::where('branch_id',1)->where('id',28)->where('staff_status','1');
    //     }else{
    //         $all_staff= Teacher::where('branch_id',1)->where('staff_status','1')->where('drop_status','0');
    //     }
        
        // $all_staff= Teacher::where('branch_id',1)->where('staff_status','1')->where('drop_status','0');
       $all_staff=  Teacher::Select('teachers.*','teacher_documents.photo')
                       
                         ->leftjoin('teacher_documents','teacher_documents.teacher_id','teachers.id')
               	   ->where('teachers.branch_id',1)->where('teachers.staff_status','1')->where('teachers.drop_status','0');
     
    		 if($request->isMethod('post')){
		     if(!empty($request->name)){
		          $value = $request->name;
		          
    		    	$all_staff =  $all_staff->where(function($query) use ($value){
    		    	             $query->where("teachers.first_name", 'like', '%' .$value. '%');
                                $query->orWhere("teachers.last_name", 'like', '%' .$value. '%');
                                $query->orWhere("teachers.father_name", 'like', '%' .$value. '%');
                                $query->orWhere("teachers.mobile", 'like', '%' .$value. '%');
                                $query->orWhere("teachers.email", 'like', '%' .$value. '%');
    		    	}); 
    		}
    	}
    		
    		$all_teachers = $all_staff->get()->toArray();
    		$atnrecord =array();
    		$work_from_home =array();
    		$present =array();
    		$holiday =array();
    		$absent =array();
    		$halfday =array();
    		if(!empty($all_teachers)){
    		    
        		foreach ($all_teachers as $key => $staff_record) {
        		    
        		    	$atnrecord[] =TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name','teacher_documents.photo')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                         ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
               	    ->where('teacher_attendance.teacher_id',$staff_record['id'])->whereMonth('teacher_attendance.date',$curr_mnt)->whereYear('teacher_attendance.date',$curr_yrs)->groupby('teacher_attendance.date')->get(['date','teacher_id','attendance_status_id']);
    	
    		//	$atnrecord[$staff_record['id']] = TeacherAttendance::where('teacher_attendance.teacher_id',$staff_record['id'])->whereMonth('teacher_attendance.date',$curr_mnt)->whereYear('teacher_attendance.date',$curr_yrs)->groupby('teacher_attendance.date')->get(['date','teacher_id','attendance_status_id'])->keyBy('date')->toArray();
    	
    	
    	$data1=TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name','teacher_documents.photo')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                         ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
                        
                        ->where('teacher_attendance.teacher_id',$staff_record['id'])->where('teacher_attendance.date',$request->date1)->where('teacher_attendance.attendance_status_id',3)->get(['date','teacher_id','attendance_status_id']);
    	       
    	       
    	       
    	          if(count($data1) > 0)
    	          {
    	              $work_from_home[] = $data1;
    	          }
    	     
    	     $data2=TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name','teacher_documents.photo')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                        ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
                        
                        ->where('teacher_attendance.teacher_id',$staff_record['id'])->where('teacher_attendance.date',$request->date1)->where('teacher_attendance.attendance_status_id',13)->get(['date','teacher_id','attendance_status_id']);
    	            
    	           if(count($data2)> 0)
    	          {
    	              $present[] = $data2;
    	          }
    	          
    	         $data3=TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name' ,'teachers.mobile','teacher_documents.photo')
                           ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')->where('teacher_attendance.teacher_id',$staff_record['id'])->where('teacher_attendance.date',$request->date1)->where('teacher_attendance.attendance_status_id',14)->get(['date','teacher_id','attendance_status_id']);
    	       
    	           if(count($data3)> 0)
    	          {
    	              $absent[] = $data3;
    	          }
    	            
    	            $data4=TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name','teacher_documents.photo')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                             ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
                        ->where('teacher_attendance.teacher_id',$staff_record['id'])->where('teacher_attendance.date',$request->date1)->where('teacher_attendance.attendance_status_id',5)->get(['date','teacher_id','attendance_status_id']);
    	       
    		   if(count($data4)> 0)
    	          {
    	              $holiday[] = $data4;
    	          }
        		   
        		   $data5=TeacherAttendance::Select('teacher_attendance.*','teachers.first_name','teachers.last_name','teacher_documents.photo')
                        ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                          ->leftjoin('teacher_documents','teacher_documents.teacher_id','teacher_attendance.teacher_id')
                        ->where('teacher_attendance.teacher_id',$staff_record['id'])->where('teacher_attendance.date',$request->date1)->where('teacher_attendance.attendance_status_id',4)->get(['date','teacher_id','attendance_status_id']);
    	       
    		   if(count($data5)> 0)
    	          {
    	              $halfday[] = $data5;
    	          }
        		   
        		}
    		}
    	

    		 
    		$AttStatus = AttendanceStatus::markable()->get()->keyBy('id')->toArray();
    		
		return response()->json(['status' => true, 'message' => 'Attendance Fetched Successfully','data'=>$atnrecord,'present'=>$present,'work_from_home'=>$work_from_home,'absent'=>$absent,'halfday'=>$halfday,'holiday'=>$holiday,'all_teachers'=>$all_teachers,'AttStatus'=>$AttStatus,'curr_yrs'=>$curr_yrs,'curr_mnt'=>$curr_mnt,'totel_month_day'=>$totel_month_day, 'search'=>$search], 200);

    }
    
    
       public function add(Request $request){

       if($request->isMethod('post')){
           
           
        //   $array = ['1'=> 1,'4'=>5];
        //   $keys = array_keys($array);
          
        //   	    $data = array();
          	    
        //   	    $count =0;
        //   foreach ($array as $key => $item) {
            
        //      $data[] = array(
        //             'teacher_id' => $keys[$count],
        //             'status' => $item,
                  
        //         );
                
        //         $count++;
               
               
        //   }
           
           
   

           
          $teacher_id =$request->teacher_id;
           $status =$request->status;
          $name =$request->first_name;
          $mobile =$request->mobile;
          $email =$request->email;
          $dateTime = date('l jS \of F Y h:i:s A');

            if(!empty($teacher_id)){

                         $keys = array_keys($teacher_id);
          
          	    $data = array();
          	    
          	   
          foreach ($teacher_id as $key => $item) {
                           
                                
                                $last_data = TeacherAttendance::where('teacher_id',$item)->where('date',$request->date)->get()->first();
                               
                                  if(!empty($last_data)){
                                      $attendance = $last_data;
                                    }else{
                                      $attendance = new TeacherAttendance;//model name
                                  }  
                                   
                                $attendance->user_id = 1;
                                $attendance->session_id = 1;
                                $attendance->branch_id = 1;
                        		$attendance->teacher_id= $item;
                        		$attendance->date  = $request->date;
                        		$attendance->attendance_status_id  = $status[$key];
                        		$attendance->save();
                        // 		if(!empty($name)){
                        // 		$attendance->name= $name[$count];
                        // 		}
                        // 		if(!empty($mobile)){
                        // 		$attendance->mobile= $mobile[$count];
                        // 		}
                        		
                        		$oldData = TeacherAttendance::select('teacher_attendance.*','teachers.mobile','teachers.first_name','teachers.email','attendance_status.name','teachers.last_name')
                                                            ->leftjoin('teachers','teachers.id','teacher_attendance.teacher_id')
                                                            ->leftjoin('attendance_status','attendance_status.id','teacher_attendance.attendance_status_id')
                                                            ->where('teacher_attendance.teacher_id',$item)
                                                            ->get()->first();
                        		
                        	     		
                        	
                          
                        }  
                        
                 	return response()->json(['status' => true, 'message' => 'Attendance Successfully'], 200);
            }
            	return response()->json(['status' => false, 'message' => 'Attendance Unsuccessfully','data'=>$teacher_id, 'attendanceStatus'=>$status],200);
        }
    
     
    }
}
