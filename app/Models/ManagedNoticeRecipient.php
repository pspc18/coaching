<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagedNoticeRecipient extends Model
{
    protected $table = 'managed_notice_recipients';
    protected $guarded = [];

    public function notice()
    {
        return $this->belongsTo(ManagedNotice::class, 'managed_notice_id');
    }
}
