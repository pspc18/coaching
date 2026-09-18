<?php

namespace App\Models\exam;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FillMinMaxMarks extends Model
{
        use SoftDeletes;
	protected $table = "fill_min_max_marks"; //table name

    
}