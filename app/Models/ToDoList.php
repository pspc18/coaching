<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ToDoList extends Model
{
        use SoftDeletes;
	protected $table = "to_do_list"; //table name
	
}