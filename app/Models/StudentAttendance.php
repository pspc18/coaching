<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
class StudentAttendance extends Model
{
       use SoftDeletes;
	protected $table = "student_attendance"; //table name
	
	public function AttendanceStatus()
    {
        return $this->belongsTo('App\Models\AttendanceStatus','attendance_status_id');
    }

    public function ClassTypes()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }

    
    
    public static function countPresentStudents()
{
    $data = StudentAttendance::where('date', date('Y-m-d'))
        ->where('session_id', Session::get('session_id'))
        ->where('branch_id', Session::get('branch_id'));

    return $data;
}
    
    public static function studentTotalPresent(){
        $first_date = StudentAttendance::where('class_type_id', Session::get('class_type_id'))
            ->where('attendance_status_id', 1)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->orderBy('id', 'ASC')
            ->value('date');
        
        $last_date = StudentAttendance::where('class_type_id', Session::get('class_type_id'))
            ->where('attendance_status_id', 1)
            ->where('session_id', Session::get('session_id'))
            ->where('branch_id', Session::get('branch_id'))
            ->orderBy('id', 'DESC')
            ->value('date');
            
        $data = 0;
        
        if(!empty($first_date)){
            $data = StudentAttendance::where('attendance_status_id', 1)
                ->where('admission_id', Session::get('id'))
                ->where('session_id', Session::get('session_id'))
                ->where('branch_id', Session::get('branch_id'))
                ->whereBetween('date', [$first_date, $last_date])
                ->count(); 
        }
    
        return $data;
    
    }
    
    protected static function booted()
    {
        static::saved(function ($att) {
            \App\Helpers\helper::clearAttendanceCache($att->branch_id ?? null, $att->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($att->branch_id ?? null, $att->session_id ?? null);
        });
        static::deleted(function ($att) {
            \App\Helpers\helper::clearAttendanceCache($att->branch_id ?? null, $att->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($att->branch_id ?? null, $att->session_id ?? null);
        });
    }
}