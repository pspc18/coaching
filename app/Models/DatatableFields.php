<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class DatatableFields extends Model
{
       // use SoftDeletes;
	protected $table = "datatable_fields"; //table name

	protected $fillable = [
		'user_id',
		'fields',
	];
	
}
