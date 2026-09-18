<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportComplaint extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $dates = ['last_replied_at', 'resolved_at', 'closed_at'];

    public const STATUSES = [
        'open' => 'Open',
        'acknowledged' => 'Acknowledged',
        'in_progress' => 'In progress',
        'awaiting_user' => 'Awaiting user response',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'reopened' => 'Reopened',
    ];

    public const CATEGORIES = [
        'academic' => 'Academic', 'fees' => 'Fees', 'attendance' => 'Attendance',
        'transport' => 'Transport', 'facility' => 'Facility', 'staff' => 'Staff',
        'technical' => 'Technical', 'other' => 'Other',
    ];

    public function replies()
    {
        return $this->hasMany(SupportComplaintReply::class)->orderBy('id');
    }

    public function student()
    {
        return $this->belongsTo(Admission::class, 'admission_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    protected static function booted()
    {
        static::saved(function ($complaint) {
            \App\Helpers\helper::clearComplaintCache($complaint->branch_id ?? null, $complaint->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($complaint->branch_id ?? null, $complaint->session_id ?? null);
        });
        static::deleted(function ($complaint) {
            \App\Helpers\helper::clearComplaintCache($complaint->branch_id ?? null, $complaint->session_id ?? null);
            \App\Helpers\helper::clearDashboardCache($complaint->branch_id ?? null, $complaint->session_id ?? null);
        });
    }
}
