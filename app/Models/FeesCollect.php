<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesCollect extends Model
{
        use SoftDeletes;
	protected $table = "fees_collect"; //table name

    /*public function PayMode(){
        return $this->belongsTo('App\Models\Master\PaymentMode','pay_mode_id');
    }*/  
    
	public function Student()
    {
        return $this->belongsTo('App\Models\Admission','admission_id');
    }
    public function Admission()
    {
        return $this->belongsTo('App\Models\Admission','admission_id');
    }
	/*public function StudentOnlineFee()
    {
        return $this->belongsTo('App\Models\Admission','admission_id');
    } */   
    public function ClassTypes()
    {
        return $this->belongsTo('App\Models\ClassType','class_type_id');
    }
     public function Section()
    {
        return $this->belongsTo('App\Models\Master\Section','section_id');
    }    
     public function PaymentMode()
    {
        return $this->belongsTo('App\Models\Master\PaymentMode','payment_mode_id');
    }    
    
    protected static function booted()
    {
        static::saved(function ($fee) {
            \App\Helpers\helper::clearFeesCache($fee->branch_id ?? null, $fee->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($fee->branch_id ?? null, $fee->session_id ?? null);
        });
        static::deleted(function ($fee) {
            \App\Helpers\helper::clearFeesCache($fee->branch_id ?? null, $fee->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($fee->branch_id ?? null, $fee->session_id ?? null);
        });
    }
}