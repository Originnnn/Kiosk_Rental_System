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
    Schema::create('kiosk_positions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kiosk_id')->constrained('kiosks')->cascadeOnDelete();
        $table->decimal('x', 8, 2);
        $table->decimal('y', 8, 2);
        $table->decimal('width', 8, 2);
        $table->decimal('height', 8, 2);
        $table->string('zone')->index();
        $table->string('map_version')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kiosk_positions');
    }
};
