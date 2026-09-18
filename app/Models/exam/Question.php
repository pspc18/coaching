<?php

namespace App\Models\exam;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Question extends Model
{
        use SoftDeletes;
	protected $table = "questions"; //table name

    public function Subject(){
        return $this->belongsTo('App\Models\Subject','subject_id');
    } 

    public function ClassType(){
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    } 
    
    public function Section(){
        return $this->belongsTo('App\Models\Master\Section','section_id');
    } 

    public static function countQuestion(){
        $data = Question::where('session_id',Session::get('session_id'));
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
		 $data = $data->count();
        return $data;
    }
	
    
}