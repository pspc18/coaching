<?php

namespace App\Models\fees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AdvanceFees extends Model
{
        use SoftDeletes;
	protected $table = "fees_advances"; //table name
}