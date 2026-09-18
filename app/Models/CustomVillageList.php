<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class CustomVillageList extends Model
{
        use SoftDeletes;
	protected $table = "custom_villages_list"; //table name

	
}