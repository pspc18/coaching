<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallLog extends Model
{
    use SoftDeletes;

    protected $table = 'call_logs';

    protected $fillable = [
        'call_type',
        'calling_purpose_id',
        'name',
        'mobile_no',
        'date',
        'start_time',
        'end_time',
        'follow_up_date',
        'note',
        'created_by'
    ];

    protected $casts = [
        'date' => 'date',
        'follow_up_date' => 'date',
    ];

    public function callingPurpose()
    {
        return $this->belongsTo(\App\Models\Master\EnquiryStatus::class, 'calling_purpose_id');
    }
}
