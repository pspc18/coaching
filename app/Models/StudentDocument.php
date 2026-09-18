<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Session;
class StudentDocument extends Model
{
       use SoftDeletes;
	protected $table = "student_documents"; //table name
	
}