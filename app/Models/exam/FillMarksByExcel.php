<?php

namespace App\Models\exam;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FillMarksByExcel extends Model
{
        use SoftDeletes;
	protected $table = "fill_marks_by_excel"; //table name

}