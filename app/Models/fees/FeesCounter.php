<?php

namespace App\Models\fees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesCounter extends Model
{
        use SoftDeletes;
	protected $table = "fees_counters"; //table name

    
}