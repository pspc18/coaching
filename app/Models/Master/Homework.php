<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class Homework extends Model
{
        use SoftDeletes;
	protected $table = "homeworks"; //table name
	
     public function Admission(){
         
        return $this->belongsTo('App\Models\Admission','subject');
    }

     public function Subject(){
         
        return $this->belongsTo('App\Models\Subject','subject');
    }
    
    public function ClassType(){
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }

     public function Section(){
        return $this->belongsTo('App\Models\Master\Section','section_id');
    }    
    
     public function Teacher(){
        return $this->belongsTo('App\Models\User','teacher_id')->withDefault();
    }

    public function UploadHomework(){
        return $this->hasMany('App\Models\Master\UploadHomework', 'homework_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    
    public static function todayHomework()
{
    $data = Homework::where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'))
        ->whereDate('homework_issue_date', date('Y-m-d'));

    // Default value
    $homework = 0;

    if (Session::get('role_id') == 2) {

        $classTeacherOf = null;

        if (!empty($classTeacherOf) && !empty($classTeacherOf->class_type_id)) {
            $homework = $data->where('class_type_id', $classTeacherOf->class_type_id)->count();
        }

    } else {
        $homework = $data->count();
    }

    return $homework;
}
    
}

// Bi-directional class aliases for full case-insensitivity on all environments (Linux/Windows)
if (!class_exists('App\Models\Master\HomeWork', false)) {
    class_alias('App\Models\Master\Homework', 'App\Models\Master\HomeWork');
}
if (!class_exists('app\Models\Master\Homework', false)) {
    class_alias('App\Models\Master\Homework', 'app\Models\Master\Homework');
}
if (!class_exists('app\Models\Master\HomeWork', false)) {
    class_alias('App\Models\Master\Homework', 'app\Models\Master\HomeWork');
}