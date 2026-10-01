<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('session_id');
            $table->string('unique_id');
            $table->string('month');
            $table->integer('year');
            $table->decimal('amount', 12, 2)->default(0);
            $table->dateTime('paid_at')->nullable();
            $table->unsignedBigInteger('paid_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_payments');
    }
}
