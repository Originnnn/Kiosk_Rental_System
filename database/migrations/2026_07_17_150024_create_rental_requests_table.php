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
    Schema::create('rental_requests', function (Blueprint $table) {
        $table->id();
        $table->string('reference_code')->unique();
        $table->foreignId('kiosk_id')->constrained('kiosks');
        $table->foreignId('customer_id')->constrained('customers');
        $table->string('contact_name');
        $table->string('contact_email');
        $table->string('contact_phone');
        $table->date('desired_start');
        $table->date('desired_end');
        $table->string('status')->index(); // new, in_review, approved, rejected, converted
        $table->unsignedBigInteger('assigned_to')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
        
        $table->index('created_at');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_requests');
    }
};
