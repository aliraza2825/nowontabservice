<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('toast_menus', function (Blueprint $table) {
            $table->id();
            $table->longText('raw_json')->nullable();
            $table->longText('formatted_json')->nullable();
            $table->string('metadata_hash')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('toast_menus');
    }
};


