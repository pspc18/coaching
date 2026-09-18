<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelRoom extends Model
{
        use SoftDeletes;
	protected $table = "hostel_room"; //table name

	public function Hostel(){
        return $this->belongsTo('App\Models\hostel\Hostel','hostel_id');
    }
    
	public function HostelBuilding(){
        return $this->belongsTo('App\Models\hostel\HostelBuilding','building_id');
    } 
    
	public function HostelFloor(){
        return $this->belongsTo('App\Models\hostel\HostelFloor','floor_id');
    }

    public static function countTotelRoom(){
        $data = HostelRoom:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
    
}