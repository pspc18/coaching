<?php

namespace App\Models\exam;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExaminationScheduleDetail extends Model
{
        use SoftDeletes;
	protected $table = "examination_schedule_details"; //table name
	
	protected $fillable = ['from_time','to_time','date'];
}