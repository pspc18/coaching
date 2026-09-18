<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gallery extends Model
{
        use SoftDeletes;
	protected $table = "gallery"; //table name
	
}