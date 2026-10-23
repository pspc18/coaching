<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillCounter extends Model
{
    protected $table = 'bill_counters';
    protected $fillable = [
        'id', 'type', 'counter', 'session_id', 'branch_id', 'user_id',
    ];
}
