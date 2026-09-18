<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelBed extends Model
{
        use SoftDeletes;
	protected $table = "hostel_bed"; //table name


	public function HostelRoom(){
        return $this->belongsTo('App\Models\hostel\HostelRoom','room_id');
    }
    
    public static function countTotelBed(){
        $data = HostelBed:: where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
          $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }    
    
}