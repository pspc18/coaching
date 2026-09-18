<?php

namespace App\Http\Controllers;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Student;
use App\Models\Admission;
use App\Models\RollNumber;
use App\Models\StudentId;
use App\Models\StudentAction;
use App\Models\Classs;
use App\Models\BillCounter;
use App\Models\SmsSetting;
use App\Models\SchoolCalender;
use App\Models\Master\Weekendcalendar;
use Session;
use Hash;
use Helper;
use Str;
use Mail;
use DB;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SchoolCalenderController extends Controller

{
    public function schoolCalenderAdd(Request $request){
        
        if($request->isMethod('post')){
            $add = new SchoolCalender;//model name
            $add->user_id = Session::get('id');
            $add->session_id = Session::get('session_id');
            $add->branch_id = Session::get('branch_id'); 
            $add->message = $request->message;
            $add->date = $request->date;
            $add->save();
        }
    }
    public function calendarView(Request $request){
        
       $data=SchoolCalender::all();
             return response()->json([
            'data' => $data,
        ]);
    }
    public function getEvents(Request $request){
        
       $calender=WeekendCalendar::all();
       
       	    $data = array(); 
            foreach ($calender as $key => $item) {
                $data[] = array(
                    'date' => $item->date ?? '',
                    'event' => $item->event_schedule ?? '',
                    'attendance_status' => $item->attendance_status ?? '',
                   
                );
            }
	   
             return response()->json([
            'data' => $data,
        ]);
    }
    
    
    
}