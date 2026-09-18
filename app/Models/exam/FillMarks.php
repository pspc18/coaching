<?php

namespace App\Models\exam;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FillMarks extends Model
{
        use SoftDeletes;
	protected $table = "fill_marks"; //table name

    // public static function countFillMark(){
    //     $countFillMark = FillMarks::where('branch_id',Session::get('branch_id'))->where('session_id',Session::get('session_id'))->count();
    //     return $countFillMark; 
    // }
}