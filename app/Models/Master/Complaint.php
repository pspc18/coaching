<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Complaint extends Model
{
        use SoftDeletes;
	protected $table = "complaint"; //table name



 public function ClassType()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }	

 public function Admission()
    {
        return $this->belongsTo('App\Models\Admission','admission_id');
    }	
    
     public function Section()
    {
        return $this->belongsTo('App\Models\Master\Section','section_id');
    }
    
   public static function countComplaint(){
    return Complaint::whereNull('deleted_at')
        ->where('branch_id', Session::get('branch_id'))
        ->where('session_id', Session::get('session_id'))
        ->get();
}

}