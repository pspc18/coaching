<?php

namespace App\Models\exam;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExamTerm extends Model
{
        use SoftDeletes;
	protected $table = "exam_terms"; //table name
}