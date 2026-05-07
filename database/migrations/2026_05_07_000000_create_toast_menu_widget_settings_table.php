<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toast_menu_widget_settings', function (Blueprint $table) {
            $table->id();
            $table->json('allowed_menu_guids')->nullable();
            $table->json('allowed_category_guids')->nullable();
            $table->json('allowed_item_guids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toast_menu_widget_settings');
    }
};
