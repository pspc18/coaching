<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManagedNotice extends Model
{
    use SoftDeletes;

    protected $table = 'managed_notices';
    protected $guarded = [];
    protected $dates = ['from_date', 'to_date', 'reviewed_at', 'published_at'];

    public function recipients()
    {
        return $this->hasMany(ManagedNoticeRecipient::class, 'managed_notice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    protected static function booted()
    {
        static::saved(function ($notice) {
            \App\Helpers\helper::clearNoticeCache($notice->branch_id ?? null, $notice->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($notice->branch_id ?? null, $notice->session_id ?? null);
        });
        static::deleted(function ($notice) {
            \App\Helpers\helper::clearNoticeCache($notice->branch_id ?? null, $notice->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($notice->branch_id ?? null, $notice->session_id ?? null);
        });
    }
}
