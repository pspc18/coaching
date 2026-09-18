<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AllowMultiplePayrollPayments extends Migration
{
    public function up()
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->dropUnique('payroll_payment_unique');
            $table->index(
                ['branch_id', 'session_id', 'unique_id', 'month', 'year'],
                'payroll_payment_period_index'
            );
        });
    }

    public function down()
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->dropIndex('payroll_payment_period_index');
            $table->unique(
                ['branch_id', 'session_id', 'unique_id', 'month', 'year'],
                'payroll_payment_unique'
            );
        });
    }
}
