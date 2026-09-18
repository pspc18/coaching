<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class StaffSalary extends Model
{
       use SoftDeletes;
	protected $table = "staff_salarys"; //table name

    public function User(){
        return $this->belongsTo('App\Models\User','staff_id');
    }  

    public function Month(){
        return $this->belongsTo('App\Models\Month','month_id');
    } 
    
}