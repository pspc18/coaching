<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveManagement extends Model
{
    use SoftDeletes;

    protected $table = "leave_management";

    public function studentId()
    {
        return $this->belongsTo('App\Models\Admission', 'admission_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public static function countLeave()
    {
        $data = LeaveManagement::where('status', 1)->count();
        if ($data) {
            return $data;
        }
        return 0;
    }
}
