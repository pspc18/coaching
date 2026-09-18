<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HomeworkReview extends Model
{
        use SoftDeletes;
	protected $table = "homework_review"; //table name
	
    
}