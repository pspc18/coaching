<?php

namespace App\Models;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InventorySaleDetail extends Model
{
        use SoftDeletes;
	protected $table = "inventory_sale_details"; //table name
    
    
 
}