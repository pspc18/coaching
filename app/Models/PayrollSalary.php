<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollSalary extends Model
{
    protected $table = 'payroll_salaries';

    protected $fillable = [
        'branch_id',
        'session_id',
        'unique_id',
        'month',
        'year',
        'gross_amount',
        'deduction_amount',
        'net_amount',
        'generated_at',
        'generated_by',
    ];
}
