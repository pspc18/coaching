<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Enquiry extends Model
{
    use SoftDeletes;

    protected $table = "enquirys";

    protected $fillable = [
        'user_id','session_id','branch_id',
        'first_name','id_proof','id_number','email','mobile',
        'class_type_id','father_name','mother_name',
        'dob','gender_id','registration_date','previous_school',
        'note','no_of_child','assigned_by','reference_id','response_id','response',
        'follow_up_date','next_follow_up_date','status'
    ];

    protected $dates = [
        'dob','registration_date','follow_up_date',
        'next_follow_up_date','created_at','updated_at','deleted_at'
    ];


    public function classTypes()
    {
        return $this->belongsTo(ClassType::class,'class_type_id');
    }

    public function gender()
    {
        return $this->belongsTo('App\Models\Gender','gender_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo('App\Models\User','assigned_by');
    }

    public function latestRemark()
    {
        return $this->hasOne(Remark::class, 'student_id')->latestOfMany();
    }

    public function getLatestStatusAttribute()
    {
        if ($this->latestRemark) {
            $lines = explode("\n", $this->latestRemark->remark);
            return isset($lines[1]) ? str_replace('Status: ', '', $lines[1]) : $this->status;
        }
        return $this->status;
    }

    public function getLatestFollowupDateAttribute()
    {
        if ($this->latestRemark) {
            $lines = explode("\n", $this->latestRemark->remark);
            return isset($lines[2]) ? str_replace('Next Follow Up: ', '', $lines[2]) : $this->next_follow_up_date;
        }
        return $this->next_follow_up_date;
    }
    
    public static function countTotalRegistration()
    {
        return self::count();
    }
}
