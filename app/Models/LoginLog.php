<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LoginLog extends Model
{
        use SoftDeletes;
	protected $table = "login_logs"; //table name
	
}