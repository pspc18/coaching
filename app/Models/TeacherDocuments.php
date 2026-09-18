<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TeacherDocuments extends Model
{
       use SoftDeletes;
	protected $table = "teacher_documents"; //table name
	
	
}