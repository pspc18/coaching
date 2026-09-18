<?php

namespace App\Models\Master;
use Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class NoticeBoard extends Model
{
        use SoftDeletes;
	protected $table = "notice_board"; //table name

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