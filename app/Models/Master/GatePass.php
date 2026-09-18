<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class GatePass extends Model
{
        use SoftDeletes;
	protected $table = "gate_passes"; //table name
	
}