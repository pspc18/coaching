<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelBuilding extends Model
{
        use SoftDeletes;
	protected $table = "hostel_building"; //table name

	public function Hostel(){
        return $this->belongsTo('App\Models\hostel\Hostel','hostel_id');
    }

    public static function countTotelBuilding(){
        $data = HostelBuilding:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
    
}