<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use SoftDeletes;

    protected $table = 'batches';

    protected $fillable = [
        'course_id',
        'branch_id',
        'session_id',
        'financial_year',
        'user_id',
        'name',
        'shift',
        'start_time',
        'end_time',
        'capacity',
        'room_no',
        'class_type_id',
        'orderBy',
        'status',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function session()
    {
        return $this->belongsTo(Sessions::class, 'session_id');
    }

    public function classType()
    {
        return $this->belongsTo(ClassType::class, 'class_type_id');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class, 'batch_id');
    }

    /**
     * Formatted Financial Year Label: e.g. "2024-25" or "2026-27"
     */
    public function getFinancialYearLabelAttribute()
    {
        if (!empty($this->financial_year)) {
            return $this->financial_year;
        }
        if ($this->session) {
            return $this->session->from_year . '-' . $this->session->to_year;
        }
        return 'N/A';
    }

    /**
     * Formatted display label: "Batch 1 (Morning • 08:00 AM - 12:00 PM) [2024-25]"
     */
    public function getFormattedTitleAttribute()
    {
        $timing = '';
        if (!empty($this->start_time) && !empty($this->end_time)) {
            $timing = ' • ' . $this->start_time . ' - ' . $this->end_time;
        } elseif (!empty($this->start_time)) {
            $timing = ' • from ' . $this->start_time;
        }

        $fy = !empty($this->financial_year) ? ' [' . $this->financial_year . ']' : '';
        $shift = !empty($this->shift) ? $this->shift : 'General';
        return $this->name . ' (' . $shift . $timing . ')' . $fy;
    }
}
