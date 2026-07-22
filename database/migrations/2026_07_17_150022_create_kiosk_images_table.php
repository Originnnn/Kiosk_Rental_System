<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('kiosk_images', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kiosk_id')->constrained('kiosks')->cascadeOnDelete();
        $table->string('file_path');
        $table->string('alt_text')->nullable();
        $table->integer('sort_order')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_images');
    }
};
