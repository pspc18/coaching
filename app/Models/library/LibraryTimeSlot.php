<?php

namespace App\Models\library;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class LibraryTimeSlot extends Model
{
        use SoftDeletes;
	protected $table = "library_time_slots"; //table name
 
    
}