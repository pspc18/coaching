<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StoreBillingDetail extends Model
{
       use SoftDeletes;
	protected $table = "store_item_billing_details"; //table name

    
}

