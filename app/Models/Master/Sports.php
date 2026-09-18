<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sports extends Model
{
        use SoftDeletes;
	protected $table = "sports_certificates"; //table name
	
}