<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StaffSalaryDetail extends Model
{
       use SoftDeletes;
	protected $table = "staff_salary_details"; //table name

    public function User(){
        return $this->belongsTo('App\Models\User','staff_id');
    }
    
    public function City(){
        return $this->belongsTo('App\Models\City','name');
    }
    public function Month(){
        return $this->belongsTo('App\Models\Month','month_id');
    }
    
    public function SalaryDocument(){
        return $this->belongsTo('App\Models\SalaryDocument','staff_id');
    }
    
}