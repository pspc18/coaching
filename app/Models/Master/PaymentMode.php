<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class PaymentMode extends Model
{
        use SoftDeletes;
	protected $table = "payment_modes"; //table name
	
}