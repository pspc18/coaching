<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
class TeacherSubject extends Model
{
       use SoftDeletes;
//	protected $table = "teacher_subjects"; //table name

    protected $fillable = [
        'user_id',
        'session_id',
        'branch_id',
        'subject_id',
        'teacher_id',
        'class_type_id',
        'time_period_id',
    ];	
	
}