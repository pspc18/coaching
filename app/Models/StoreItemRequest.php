<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StoreItemRequest extends Model
{
       use SoftDeletes;
	protected $table = "store_item_student_requests"; //table name

    
}

