<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class UploadHomework extends Model
{
        use SoftDeletes;
	protected $table = "upload_homeworks"; //table name

    public function Admission(){
       return $this->belongsTo('App\Models\Admission','admission_id');
    }	

    public function ClassType(){
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }

     public function Section(){
        return $this->belongsTo('App\Models\Master\Section','section_id');
    } 

    public static function countTodayAssignment()
{
    $homework = 0;

    $data = UploadHomework::where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'))
        ->where('submission_date', date('Y-m-d'))
        ->groupBy('admission_id');

    if (Session::get('role_id') == 2) {

        $classTeacherOf = DB::table('teachers')
            ->where('id', Session::get('teacher_id'))
            ->whereNull('deleted_at')
            ->first();

        if (!empty($classTeacherOf)) {
            $homework = $data->where('class_type_id', $classTeacherOf->class_type_id)->count();
        }

    } else {
        $homework = $data->count();
    }

    return $homework;
}  









  
}