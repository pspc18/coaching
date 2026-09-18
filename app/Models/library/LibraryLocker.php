<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryLocker extends Model
{
        use SoftDeletes;
	protected $table = "library_lockers"; //table name
 
    
}