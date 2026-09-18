<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RetrunBook extends Model
{
        use SoftDeletes;
	protected $table = "retrun_book"; //table name
 
    
}