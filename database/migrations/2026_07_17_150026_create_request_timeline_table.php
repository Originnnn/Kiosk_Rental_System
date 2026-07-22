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
    Schema::create('request_timeline', function (Blueprint $table) {
        $table->id();
        $table->foreignId('rental_request_id')->constrained('rental_requests')->cascadeOnDelete();
        $table->unsignedBigInteger('actor_id')->nullable(); // Có thể là User nội bộ hoặc null nếu từ system
        $table->string('action');
        $table->string('from_status')->nullable();
        $table->string('to_status')->nullable();
        $table->text('note')->nullable();
        $table->json('metadata')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_timeline');
    }
};
