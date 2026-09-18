<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Classs extends Model
{
       use SoftDeletes;
	protected $table = "class"; //table name

     public function Section()
    {
        return $this->belongsTo('App\Models\Master\Section','section_id');
    }
	
	public function ClassType()
    {
        return $this->belongsTo('App\Models\ClassType','class_id');
    }
}

