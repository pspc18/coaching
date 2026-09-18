<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollDeduction extends Model
{
    protected $table = 'payroll_deductions';

    protected $fillable = [
        'loan_id',
        'branch_id',
        'session_id',
        'unique_id',
        'month',
        'year',
        'amount',
        'original_amount',
        'type',
        'title',
        'remark',
        'is_applied',
        'created_by',
    ];
}
