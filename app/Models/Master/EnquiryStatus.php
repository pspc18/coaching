<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryStatus extends Model
{
    use SoftDeletes;

    protected $table = "enquiry_status";

    protected $fillable = [
        'user_id','branch_id','session_id','name','type'
    ];

    public static function countStatus($type){
        return EnquiryStatus::where('type',$type)->whereNull('deleted_at')->count();
    }

       public function callLogs()
    {
        return $this->hasMany(\App\Models\CallLog::class, 'calling_purpose_id');
    }
}