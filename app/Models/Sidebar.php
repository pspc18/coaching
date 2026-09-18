<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Sidebar extends Model
{
        use SoftDeletes;
	protected $table = "sidebars"; //table name
	
}