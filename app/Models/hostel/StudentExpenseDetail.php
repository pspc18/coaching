<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StudentExpenseDetail extends Model
{
        use SoftDeletes;
	protected $table = "student_expense_details"; //table name
 
}