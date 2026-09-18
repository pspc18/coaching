<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Stork extends Model
{
        use SoftDeletes;
	protected $table = "strok"; //table name
	
}