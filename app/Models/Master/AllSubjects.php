<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
class AllSubjects extends Model
{
       use SoftDeletes;
	protected $table = "all_subjects"; //table name
	
        public static function countAllSubject(){
        $data = AllSubjects::where('branch_id',Session::get('branch_id'))->where('deleted_at')->count();
        if($data){
            return $data;
        }
        return 0;
    }	
	
}