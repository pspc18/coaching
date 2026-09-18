<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InventoryDetail extends Model
{
        use SoftDeletes;
	protected $table = "inventory_details"; //table name
    
    
 
}