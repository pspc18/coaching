<?php

namespace App\Models\hostel;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class HostelMeterUnit extends Model
{
        use SoftDeletes;
	protected $table = "hostel_meter_units"; //table name

 
    
}