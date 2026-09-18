<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PrintFileSubModule extends Model
{
        use SoftDeletes;
	protected $table = "print_file_sub_modules"; //table name
}