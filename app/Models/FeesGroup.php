<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesGroup extends Model
{
        use SoftDeletes;
	protected $table = "fees_group"; //table name

    
}