<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StudentExpense extends Model
{
        use SoftDeletes;
	protected $table = "student_expenses"; //table name

    public static function countStudentExpense(){
        $data = StudentExpense:: where('session_id',Session::get('session_id'))
		 ->where('branch_id',Session::get('branch_id'))->count();
        if($data){
            return $data;
        }
        return 0;
    }

    
}