<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ExaminationAdmitCard extends Model
{
        use SoftDeletes;
	protected $table = "examination_admit_cards"; //table name
	
}