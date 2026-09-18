<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Penalty extends Model
{
        use SoftDeletes;
	protected $table = "pelantys"; //table name
	
}