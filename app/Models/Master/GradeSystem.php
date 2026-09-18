<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GradeSystem extends Model
{
        use SoftDeletes;
	protected $table = "grade_system"; //table name
	
}