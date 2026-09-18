<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StudentsSidebar extends Model
{
       use SoftDeletes;
	protected $table = "students_sidebar"; //table name

}