<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelAssign extends Model
{
        use SoftDeletes;
	protected $table = "hostel_assign"; //table name


    
    public function Hostel(){
        return $this->belongsTo('App\Models\hostel\Hostel','hostel_id');
    }
    public function HostelBuilding(){
        return $this->belongsTo('App\Models\hostel\HostelBuilding','building_id');
    }
    public function HostelFloor(){
        return $this->belongsTo('App\Models\hostel\HostelFloor','floor_id');
    }
    public function HostelRoom(){
        return $this->belongsTo('App\Models\hostel\HostelRoom','room_id');
    }
    public function HostelBed(){
        return $this->belongsTo('App\Models\hostel\HostelBed','bed_id');
    }
    
    public static function countTotelStudent(){
        $data = HostelAssign:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }    
    public static function countTotelStudentFees(){
        $data = HostelAssign:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->sum('hostel_fees');
        return $data;
    }    
    
}