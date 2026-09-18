<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class Admission extends Model
{
        use SoftDeletes;
//	protected $table = "olympiad_results"; //table name
protected $table = "admissions"; //table name
	protected $fillable = ['password','body'];
	
	public function Student()
    {
        return $this->belongsTo('App\Models\Enquiry','student_id');
    }
    public function ClassTypes()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }
     public function PreviousClass()
    {
        return $this->belongsTo('App\Models\ClassType','previous_class_type_id');
    }
     public function PreviousCity()
    {
        return $this->belongsTo('App\Models\City','previous_city_id');
    }
     public function PreviousState()
    {
        return $this->belongsTo('App\Models\State','previous_state_id');
    }
     public function City()
    {
        return $this->belongsTo('App\Models\City','city_id');
    }
    
    public function State()
    {
        return $this->belongsTo('App\Models\State','state_id');
    }

     public function Gender()
    {
        return $this->belongsTo('App\Models\Gender','gender_id');
    }
    
      
    
    public function FeesAssign()
    {
        return $this->belongsTo('App\Models\fees\FeesAssign','admissionNo');
    }
    
    public static function countActiveAdmission()
{
    $data = Admission::where('session_id', Session::get('session_id'))
        ->where('status', 1)
        ->where('school', 1);

    if (Session::get('role_id') > 1) {

        if (Session::get('role_id') == 2) {

            $classTeacherOf = DB::table('users')
                ->where('id', Session::get('id'))
                ->whereNull('deleted_at')
                ->first();

            $classes = [];

            if (!empty($classTeacherOf->class_type_id)) {
              if (is_array($classTeacherOf->class_type_id)) {
                    $classes = $classTeacherOf->class_type_id;
                }
            }

            if (!empty($classes)) {
                $data->whereIn('class_type_id', $classes);
            }
        }
    }

    return $data->where('branch_id', Session::get('branch_id'));
}

    
    public static function countAdmissionHostel(){
        $data = Admission::where('session_id',Session::get('session_id'))->where('status',1)->where('hostel',1);
        
        if(Session::get('role_id') > 1){
           $data = $data->where('branch_id',Session::get('branch_id'));
        }
            $data = $data->count();
        return $data;
    }
    
    public static function countAdmissionLibrary(){
        $data=Admission::where('session_id',Session::get('session_id'))->where('status',1)->where('library',1);
        
        if(Session::get('role_id') > 1){
            $data = $data->where('branch_id',Session::get('branch_id'));
        }
        
        $data = $data->count();
        return $data;
    }

    public static function countTodaysBirthday(){
            $data=Admission::where('session_id',Session::get('session_id'))->where('dob',date('Y-m-d'));
            
            if(Session::get('role_id') > 1){
                $data = $data->where('branch_id',Session::get('branch_id'));
            }
            
            $data = $data->count();
            return $data;
    }
    
    protected static function booted()
    {
        static::saved(function ($student) {
            \App\Helpers\helper::clearStudentCache($student->branch_id ?? null, $student->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($student->branch_id ?? null, $student->session_id ?? null);
        });
        static::deleted(function ($student) {
            \App\Helpers\helper::clearStudentCache($student->branch_id ?? null, $student->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($student->branch_id ?? null, $student->session_id ?? null);
        });
    }
    
}
