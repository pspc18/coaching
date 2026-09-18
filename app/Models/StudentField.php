<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StudentField extends Model
{
        use SoftDeletes;
	protected $table = "student_fields"; //table name
	
    
}