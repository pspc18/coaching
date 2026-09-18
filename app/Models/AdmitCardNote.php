<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AdmitCardNote extends Model
{
        use SoftDeletes;
	protected $table = "admit_card_note"; //table name
	
}