<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('online_payments')) {
            Schema::create('online_payments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('admission_id')->index();
                $table->unsignedBigInteger('branch_id')->default(1)->index();
                $table->unsignedBigInteger('session_id')->default(1)->index();
                $table->string('merchant_order_id', 64)->unique();
                $table->string('phonepe_order_id', 64)->nullable()->index();
                $table->decimal('amount', 10, 2);
                $table->unsignedBigInteger('amount_paise');
                $table->string('payment_status', 32)->default('PENDING')->index(); // PENDING, COMPLETED, FAILED, CANCELLED
                $table->string('payment_mode', 32)->default('PhonePe');
                $table->string('phonepe_transaction_id', 64)->nullable()->index();
                $table->unsignedBigInteger('invoice_id')->nullable()->index(); // links to fees_details_invoices.id
                $table->text('request_payload')->nullable();
                $table->text('response_payload')->nullable();
                $table->text('failure_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_payments');
    }
};
