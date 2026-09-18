<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class BusAssign extends Model
{
        use SoftDeletes;
	protected $table = "bus_assign_students"; //table name

    public function busId(){
       return $this->belongsTo('App\Models\Master\Bus','bus_id');
    }
    public function studentId(){
       return $this->belongsTo('App\Models\Admission','admission_id');
    }
  public function Section()
    {
        return $this->belongsTo('App\Models\Master\Section','section_id');
    }
        public function ClassTypes()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }
    public function busRoute(){
       return $this->belongsTo('App\Models\Master\BusRoute','route_id');
    }
    
    public static function countBusAssign(){
        $data = BusAssign::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->count();
        
        return $data;
    }
	
	
}