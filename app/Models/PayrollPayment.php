<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    protected $table = 'payroll_payments';

    protected $fillable = [
        'branch_id',
        'session_id',
        'unique_id',
        'month',
        'year',
        'amount',
        'paid_at',
        'paid_by',
    ];
}
