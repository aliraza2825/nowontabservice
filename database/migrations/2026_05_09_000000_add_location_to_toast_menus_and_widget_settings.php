<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('toast_menus', function (Blueprint $table) {
            $table->string('location_guid')->nullable()->after('id');
            $table->string('location_name')->nullable()->after('location_guid');
        });

        Schema::table('toast_menu_widget_settings', function (Blueprint $table) {
            $table->string('location_guid')->nullable()->after('id');
            $table->string('location_name')->nullable()->after('location_guid');
        });
    }

    public function down(): void
    {
        Schema::table('toast_menus', function (Blueprint $table) {
            $table->dropColumn(['location_guid', 'location_name']);
        });

        Schema::table('toast_menu_widget_settings', function (Blueprint $table) {
            $table->dropColumn(['location_guid', 'location_name']);
        });
    }
};
