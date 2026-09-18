<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class SecurityDeposit extends Model
{
        use SoftDeletes;
	protected $table = "security_deposit"; //table name



    
}