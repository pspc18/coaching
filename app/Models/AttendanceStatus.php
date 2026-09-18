<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class AttendanceStatus extends Model
{
    use SoftDeletes;

	protected $table = "attendance_status"; //table name

    /**
     * Statuses that can be selected while marking attendance.
     * Other master statuses remain available to the academic calendar.
     */
    public function scopeMarkable($query)
    {
        return $query->whereIn('id', [1, 2, 3, 4, 5]);
    }
}
