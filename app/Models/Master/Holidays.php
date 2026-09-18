<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Holidays extends Model
{
        use SoftDeletes;
	protected $table = "holidays"; //table name
	
}