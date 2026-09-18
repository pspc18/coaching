<?php

namespace App\Models\exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExaminationSchedule extends Model
{
        use SoftDeletes;
	protected $table = "examination_schedules"; //table name
	protected $fillable = ['exam_center'];
}