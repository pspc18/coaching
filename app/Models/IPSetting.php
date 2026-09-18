<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class IPSetting extends Model
{
        use SoftDeletes;
	protected $table = "ip_settings"; //table name

    
}

