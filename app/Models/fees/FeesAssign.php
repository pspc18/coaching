<?php

namespace App\Models\fees;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesAssign extends Model
{
        use SoftDeletes;
	protected $table = "fees_assigns"; //table name
}