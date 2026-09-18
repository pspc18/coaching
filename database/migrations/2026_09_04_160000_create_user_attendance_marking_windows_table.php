<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAttendanceMarkingWindowsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('user_attendance_marking_windows')) {
            Schema::create('user_attendance_marking_windows', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('branch_id');
                $table->unsignedBigInteger('session_id');
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('configured_by')->nullable();
                $table->time('from_time');
                $table->time('to_time');
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['branch_id', 'session_id', 'user_id'], 'user_attendance_window_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_attendance_marking_windows');
    }
}
