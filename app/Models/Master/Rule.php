<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Rule extends Model
{
        use SoftDeletes;
	protected $table = "rules"; //table name
	
	
}