<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationRoleIdsToWeekendcalendarTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('weekendcalendar') && !Schema::hasColumn('weekendcalendar', 'notification_role_ids')) {
            Schema::table('weekendcalendar', function (Blueprint $table) {
                $table->text('notification_role_ids')->nullable()->after('message_services');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('weekendcalendar') && Schema::hasColumn('weekendcalendar', 'notification_role_ids')) {
            Schema::table('weekendcalendar', function (Blueprint $table) {
                $table->dropColumn('notification_role_ids');
            });
        }
    }
}
