<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StoreItem extends Model
{
       use SoftDeletes;
	protected $table = "store_items"; //table name

    
}

