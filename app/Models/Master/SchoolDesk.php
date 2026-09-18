<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SchoolDesk extends Model
{
        use SoftDeletes;
	protected $table = "school_desk"; //table name

  
	
}