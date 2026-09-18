<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AssignBook extends Model
{
        use SoftDeletes;
	protected $table = "assign_books"; //table name
 
    
     public static function countLibraryAssign(){
        $data=AssignBook::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('status',1)->count();
        if($data){
            return $data;
        }
        return 0;
    }
     public static function countLibraryAssignBook(){
        $data=AssignBook::where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->where('status',0)->count();
        if($data){
            return $data;
        }
        return 0;
    }
    
}