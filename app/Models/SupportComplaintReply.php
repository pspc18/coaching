<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportComplaintReply extends Model
{
    protected $guarded = [];

    public function complaint()
    {
        return $this->belongsTo(SupportComplaint::class, 'support_complaint_id');
    }
}
