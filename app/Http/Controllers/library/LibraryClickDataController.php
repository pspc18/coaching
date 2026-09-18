<?php

namespace App\Http\Controllers\library;
use Illuminate\Validation\Validator; 
use App\Models\User;
use App\Models\Admission;
use App\Models\ClassType;
use App\Models\library\Library;
use App\Models\library\LibraryCabin;
use App\Models\library\LibraryPlan;
use App\Models\library\LibraryAssign;
use App\Models\library\LibraryLocker;
use App\Models\library\LibraryTimeSlot;
use Session;
use Hash;
use Helper;
use Str;
use Redirect;
use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryClickDataController extends Controller

{

 
    public function dataCabin(Request $request,$id){
        $data = array();  
        if(!empty($id)){
            $data = LibraryCabin::with('Library')->where('library_id',$id)->get();
            $libraryData ="";
            foreach($data as $key=>$type){
                $alredyAssign = LibraryAssign::where('cabin_id',$type->id)->where('cabin_status',1)->get()->first();
                if(!empty($alredyAssign)){
                    if($alredyAssign->cabin_id == $type->id){ 
                        $libraryData.='<div class="text-center m-1 cabin_width hideForm studentDetail" data-id="'.$type->id.'">
                        <div class="btn btn-danger btn-xs w-100">
                        <input class="cabin" id="cabin'.$key.'" type="radio" value="'.$type->id.'"  disabled data-value="'.$type->id.'">
                            <i class="fa fa-trello"></i><br><label for="cabin'.$key.'" >Cabin '.$type->name.'</label>
                        </div></div>';
                    }                   
                }else{
                    $libraryData.='<div class="text-center m-1 cabin_width openForm" data-id="'.$type->id.'">
                    <div class="btn btn-success btn-xs w-100 mycabin_on_click">
                    <input class="cabin" id="cabin'.$key.'" type="radio" value="'.$type->id.'" data-value="'.$type->id.'">
                    <i class="fa fa-trello"></i><br><label for="cabin'.$key.'" >Cabin '.$type->name.'</label>
                    </div></div>';
                }
           }
            echo $libraryData;
       }  
    }    
    
    
    public function allotSeatInSlot(Request $request){
     
        $data = LibraryCabin::with('Library')->where('library_id',1)->get();
    // $data = LibraryCabin::get();
        $libraryCabin ="";
            foreach($data as $key=>$type){
                $alredyAssign = LibraryPlan::where('library_cabin_id',$type->id)->where('library_time_slot_id',$request->time_slot_id)->where('status',0)->get()->first();
                if(!empty($alredyAssign)){
                    if($alredyAssign->library_cabin_id == $type->id){ 
                        $libraryCabin.='<div class="seat assigned" id="seat-3876" seat-code="S" title="" data-toggle="tooltip" aria-describedby="ui-tooltip-6">S-'.$type->name.'</div>';
                    }                   
                }else{
                    $time = LibraryTimeSlot::where('id',$request->time_slot_id)->first();
                    $not_assign_time_slot = LibraryPlan::whereIn('library_time_slot_id',explode(',', $time->not_assign_time_slot_id))->where('library_cabin_id',$type->id)->where('status',0)->get()->first();
                     
                    if(!empty($not_assign_time_slot)){
                   if($not_assign_time_slot->library_cabin_id == $type->id){ 
                        $libraryCabin.='<div class="seat assigned" id="seat-3876" seat-code="S" title="" data-toggle="tooltip" aria-describedby="ui-tooltip-6">S-'.$type->name.'</div>';
                   }else{
                    $libraryCabin .= '<div class="seat seat_not_assigned" id="seat-'.$type->id.'" seat-code="S" onclick="assignedSeat(' . ($type->id ?? '') . ',' . ($type->name ?? '') .',' . ($request->time_slot_id ?? '') . ');">S-' . $type->name . '</div>';
                   }               
                }else{
                    
                     $libraryCabin .= '<div class="seat seat_not_assigned" id="seat-'.$type->id.'" seat-code="S" onclick="assignedSeat(' . ($type->id ?? '') . ',' . ($type->name ?? '') .',' . ($request->time_slot_id ?? '') . ');">S-' . $type->name . '</div>';

                }
                }
           }
           
           
        return $libraryCabin;
    }
    
    public function allotLocker(Request $request){
     
        //$data = LibraryCabin::with('Library')->where('library_id',1)->get();
     $data = LibraryLocker::get();
        $LibraryLocker ="";
            foreach($data as $key=>$type){
                $alredyAssign = LibraryAssign::where('library_locker_id',$type->id)->where('status',1)->get()->first();
                if(!empty($alredyAssign)){
                    if($alredyAssign->library_locker_id == $type->id){ 
                        $LibraryLocker.='<div class="locker assigned" id="locker-'.$type->id.'" title="Already Allotted" data-toggle="tooltip">'.$type->name.'</div>';
                    }                   
                }else{
                        $LibraryLocker .= '<div class="locker" id="locker-' . $type->id . '" onclick="assignedLocker(' . ($type->id ?? '') . ",'" .$type->name. "'". ');">' . $type->name .'</div>';
                }
           }
           
           
        return $LibraryLocker;
    }
    
    public function get_unassigned_users(Request $request){
        $data = LibraryPlan::select('library_plans.*','admissions.first_name','admissions.last_name')
                ->leftJoin('admissions','admissions.id','library_plans.admission_id')
                ->where('library_plans.library_cabin_id',null)
                ->where('library_plans.status',0)->get();
                
        return $data;        
    }
    
}
