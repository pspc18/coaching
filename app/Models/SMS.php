<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SMS extends Model
{
        use SoftDeletes;
	protected $table = "sms_list"; //table name
	
}