<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PrintFileModule extends Model
{
        use SoftDeletes;
	protected $table = "print_file_modules"; //table name
	
	
	
}