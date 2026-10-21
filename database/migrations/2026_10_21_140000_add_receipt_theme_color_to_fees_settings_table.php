<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceiptThemeColorToFeesSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('fees_settings') && !Schema::hasColumn('fees_settings', 'receipt_theme_color')) {
            Schema::table('fees_settings', function (Blueprint $table) {
                $table->string('receipt_theme_color', 30)->nullable()->default('#8b2635')->after('receipt_layout');
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
        if (Schema::hasTable('fees_settings') && Schema::hasColumn('fees_settings', 'receipt_theme_color')) {
            Schema::table('fees_settings', function (Blueprint $table) {
                $table->dropColumn('receipt_theme_color');
            });
        }
    }
}
