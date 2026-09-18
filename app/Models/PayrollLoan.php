<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoan extends Model
{
    protected $table = 'payroll_loans';

    protected $fillable = [
        'branch_id',
        'session_id',
        'unique_id',
        'type',
        'principal_amount',
        'monthly_deduction',
        'start_month',
        'start_year',
        'is_active',
        'title',
        'remark',
        'created_by',
    ];
}

