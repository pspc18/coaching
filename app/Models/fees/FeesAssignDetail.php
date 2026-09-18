<?php

namespace App\Models\fees;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class FeesAssignDetail extends Model
{
        use SoftDeletes;
	protected $table = "fees_assign_details"; //table name

    
     public static function Collection(){
        $data = FeesAssignDetail::where('session_id',Session::get('session_id'))->where('branch_id',Session::get('branch_id'))->selectRaw('SUM(fees_group_amount) as fees_group_amount')
        ->value('fees_group_amount');
        return $data;
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