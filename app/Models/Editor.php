<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Editor extends Model
{
        use SoftDeletes;
	protected $table = "editors"; //table name

}


