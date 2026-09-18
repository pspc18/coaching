<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollLoanPayment extends Model
{
    protected $table = 'payroll_loan_payments';

    protected $fillable = [
        'branch_id',
        'session_id',
        'loan_id',
        'payment_date',
        'amount',
        'remark',
        'created_by',
    ];
}

