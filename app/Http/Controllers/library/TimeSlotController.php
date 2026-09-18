<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\library\LibraryTimeSlot;
use Session;
use Hash;
use Str;
use Redirect;
use Helper;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimeSlotController extends Controller

{
    
    public function time_slot(Request $request){
        
        
        if($request->isMethod('post')){
                $request->validate([
            'start_time' => 'required',
            'end_time' => 'required',
            'study_time' => 'required',
            'study_hour' => 'required',
            'amount' => 'required',     
         ]);
        
       
        $time = new LibraryTimeSlot;//model name
        $time->user_id = Session::get('id');
        $time->session_id = Session::get('session_id');
        $time->branch_id = Session::get('branch_id');
		$time->start_time =$request->start_time;
		$time->end_time= $request->end_time;
		$time->study_time= $request->study_time;
		$time->study_hour= $request->study_hour;
		$time->amount= $request->amount;
	    $time->save();
	    
        return redirect::to('library/time_slot')->with('message', 'Time Slot add Successfully.');
        }
       $data = LibraryTimeSlot::orderBy('id','ASC')->get();           
       return view('library.time_slot.add',['data'=>$data]);
    }
    
    public function notAssignTimeSlots(Request $request){
        if($request->isMethod('post')){
            $notAssigned = [];
            foreach($request->slot_id as $slot){
                foreach($request->not_assign_time_slot_id as $not_slot){
                    $parts = explode("/", $not_slot);
                    if($slot == $parts[1]){
                        $notAssigned[] = $parts[2];
                    }
                }
                
                $notAssignTimeSlots = implode(',', $notAssigned);
                LibraryTimeSlot::where('id',$slot)->update(['not_assign_time_slot_id' => $notAssignTimeSlots]);
                $notAssigned = [];
            }

            return redirect::to('library/time_slot')->with('message', 'Time Slot Update Successfully.');
        }
        
    }
    

    
    
}