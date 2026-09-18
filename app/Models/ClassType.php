<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ClassType extends Model
{
       use SoftDeletes;
	protected $table = "class_types"; //table name
	
 public static function countClasss(){
        $data = ClassType::where('session_id',Session::get('session_id'))->whereNull('deleted_at');
        
        if(Session('role_id') > 1){
            $data = $data->where('branch_id',Session('branch_id'));
        }
         
        $data = $data->count();  
        return $data;
    }	
	 
    protected static function booted()
    {
        static::saved(function ($class) {
            \App\Helpers\helper::clearDashboardCache($class->branch_id ?? null, $class->session_id ?? null);
        });
        static::deleted(function ($class) {
            \App\Helpers\helper::clearDashboardCache($class->branch_id ?? null, $class->session_id ?? null);
        });
    }
}