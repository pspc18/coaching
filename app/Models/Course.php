<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $table = 'courses';

    protected $fillable = [
        'branch_id',
        'session_id',
        'user_id',
        'name',
        'code',
        'description',
        'class_type_id',
        'orderBy',
        'status',
    ];

    public function batches()
    {
        return $this->hasMany(Batch::class, 'course_id')->orderBy('orderBy', 'ASC')->orderBy('id', 'ASC');
    }

    public function activeBatches()
    {
        return $this->hasMany(Batch::class, 'course_id')->where('status', 1)->orderBy('orderBy', 'ASC')->orderBy('id', 'ASC');
    }

    public function courseSubjects()
    {
        return $this->hasMany(CourseSubject::class, 'course_id')->orderBy('sort_by', 'ASC')->orderBy('id', 'ASC');
    }

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'course_id');
    }
}
