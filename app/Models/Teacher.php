<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Teacher extends Model
{
       use SoftDeletes;
	protected $table = "teachers"; //table name
    
    public function TeacherDocuments()
    {
        return $this->hasMany('App\Models\TeacherDocuments','teacher_id');
    }
    
    public static function countTeacher(){
        $data=Teacher::where('drop_status',0);
        
        if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $data->count();
        return $data;
        
    }
    
    public static function countDropTeacher(){
        $data=Teacher::where('drop_status',1);
        
        if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $data->count();
        return $data;
        
    }
  
	
}