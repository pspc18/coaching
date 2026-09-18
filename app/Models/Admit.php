<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Admit extends Model
{
        use SoftDeletes;
	protected $table = "admit_cards"; //table name
	
}