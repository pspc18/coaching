<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
class Subject extends Model
{
       use SoftDeletes;
	protected $table = "subject"; //table name
	
       public static function countSubject(){
        $data=Subject::whereNull('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }	
      public function ClassTypes()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }
	
}