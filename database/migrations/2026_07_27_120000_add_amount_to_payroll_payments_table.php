<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmountToPayrollPaymentsTable extends Migration
{
    public function up()
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('year');
        });
    }

    public function down()
    {
        Schema::table('payroll_payments', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
}
