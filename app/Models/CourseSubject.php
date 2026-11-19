<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\AllSubjects;

class CourseSubject extends Model
{
    use SoftDeletes;

    protected $table = 'course_subjects';

    protected $appends = ['name'];

    protected $fillable = [
        'course_id',
        'subject_id',
        'subject_name',
        'sort_by',
        'branch_id',
        'session_id',
        'user_id',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function allSubject()
    {
        return $this->belongsTo(AllSubjects::class, 'subject_id');
    }

    public function getNameAttribute()
    {
        return $this->attributes['subject_name'] ?? '';
    }
}
