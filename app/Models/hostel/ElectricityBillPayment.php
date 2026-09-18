<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ElectricityBillPayment extends Model
{
        use SoftDeletes;
	protected $table = "electricity_bill_payments"; //table name

  
    
}