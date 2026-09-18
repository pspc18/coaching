<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SidebarSub extends Model
{
        use SoftDeletes;
	protected $table = "sidebar_sub"; //table name
	
}