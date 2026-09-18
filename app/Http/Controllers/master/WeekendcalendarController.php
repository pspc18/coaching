<?php
namespace App\Http\Controllers\master;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Master\Weekendcalendar;
use Session;
use Hash;
use Str;
use Redirect;
use File;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

class WeekendcalendarController extends Controller 

{
           public function add(Request $request)
                {
                    if ($request->isMethod('post')) {
                        $request->validate([
                            'date' => 'required|array',
                            'date.*' => 'required|date',
                            'event_schedule' => 'required|array',
                            'event_schedule.*' => 'required|string',
                            'attendance_status' => 'required|array',
                            'attendance_status.*' => 'required|integer',
                        ]);
                
                        $userId = Session::get('id');
                        $sessionId = Session::get('session_id');
                        $branchId = Session::get('branch_id');
                
                        foreach ($request->date as $index => $dateValue) {
                            $date = new DateTime($dateValue);
                            $monthNumber = $date->format('n');
                            $weekName = $date->format('l');
                
                            $add_weeked = new Weekendcalendar;
                            $add_weeked->user_id = $userId;
                            $add_weeked->session_id = $sessionId;
                            $add_weeked->branch_id = $branchId;
                            $add_weeked->month_id = $monthNumber;
                            $add_weeked->date = $dateValue;
                            $add_weeked->day = $weekName;
                            $add_weeked->event_schedule = $request->event_schedule[$index];
                            $add_weeked->attendance_status = $request->attendance_status[$index];
                            $add_weeked->save();
                        }
                
                        return response()->json(['status' => 'success', 'message' => 'Weekend Events Added Successfully.']);
                    }
                
                    return view('master.Weekendcalendar.add');
                }
                 

                public function view(Request $request)
                {
                    $search = [
                        'month_id' => $request->month_id ?? '',
                    ];
                
                    $query = Weekendcalendar::query()
                        ->select(
                            'weekendcalendar.*',
                            'months.name as month_name',
                            'attendance_status.name as attendance_status'
                        )
                        ->leftJoin('months', 'months.id', '=', 'weekendcalendar.month_id')
                        ->leftJoin('attendance_status', 'attendance_status.id', '=', 'weekendcalendar.attendance_status')
                        ->where('weekendcalendar.session_id', Session::get('session_id'))
                        ->where('weekendcalendar.branch_id', Session::get('branch_id'));
                
                    if (!empty($search['month_id'])) {
                        $query->where('weekendcalendar.month_id', $search['month_id']);
                    }
                
                    $data = $query->latest('weekendcalendar.id')->get();
                
                    return view('master.Weekendcalendar.view', compact('data', 'search'));
                }

            public function delete(Request $request){
                $data = Weekendcalendar::find($request->delete_id)->delete();
                return redirect::to('view_weekend')->with('message', 'Weekend Calendar  Deleted Successfully.');
            }

            public function weekendPrint(Request $request, $id){
                $weekendcalendar =  Weekendcalendar::where('month_id', $id)->orderBy('id','ASC')->get();
                //dd($weekendcalendar);
                return view('master.Weekendcalendar.calendarprint', ['data' => $weekendcalendar,'id' => $id]);
            }

            public function academicCalendar(Request $request){
                $data = Weekendcalendar::where('session_id',Session::get('session_id'))
                ->where('branch_id',Session::get('branch_id'))
                ->orderBy('date','ASC')->get();
                return response()->json(['data' => $data]);
            }
            
            public function weekendSearch(Request $request){
                $search['month_type'] = $request->month_type;
                if ($request->isMethod('post')) {
                    $request->validate([]);
                    $data = Weekendcalendar::where('session_id', Session::get('session_id'))->where('branch_id', Session::get('branch_id'));
                    
                    
                    if (!empty($request->month_type)) {
                        $data = $data->where("month_type", $request->month_type);
                    }
                    $allstudents = $data->orderBy('id', 'DESC')->get();
                }
                return view('master.Weekendcalendar.calendarSearch', ['data' => $allstudents]);
            }

            public function weekendEdit(Request $request,$id){
                $data = Weekendcalendar::find($id);
                if ($request->isMethod('post')) {
                    $request->validate([
                        //'img_category' => 'required',
                        //'photo' => 'required', 
                        'date' => 'required', 
                        'event_schedule' => 'required', 
                        'attendance_status' => 'required', 
                        
                    ]);
                    $date = new DateTime($request->date); // replace with your date
                    $monthNumber = $date->format('n');
                    $weekName = $date->format('l');
                    $data->month_id =$monthNumber;
                    $data->attendance_status =$request->attendance_status;
                    $data->event_schedule =$request->event_schedule;
                    $data->date =$request->date;
                    $data->day =$weekName;
                    $data->save();
                  return response()->json(['status' => 'success', 'message' => 'Weekend Calendar  Updated Successfully','redirect' => url('view_weekend')]);
                }
                return view('master.Weekendcalendar.edit', ['data' => $data]);
            }
            
               public function status_weekend(Request $request)
                {
                  try {
                        $view = Weekendcalendar::find($request->weekendcalendar_id);
                        $view->publish = $request->status; // assuming 'message' column stores on/off
                        $view->save();
            
                        return response()->json([
                            'success' => true,
                            'status' => $view->publish
                        ]);
            
                    } catch (\Exception $e) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Failed to update status'
                        ]);
                    }
                }
    
}