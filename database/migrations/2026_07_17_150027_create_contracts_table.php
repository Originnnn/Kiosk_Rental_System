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
    Schema::create('contracts', function (Blueprint $table) {
        $table->id();
        $table->string('reference_code')->unique();
        $table->foreignId('rental_request_id')->constrained('rental_requests');
        $table->foreignId('kiosk_id')->constrained('kiosks');
        $table->foreignId('customer_id')->constrained('customers');
        $table->date('start_date');
        $table->date('end_date');
        $table->text('terms')->nullable();
        $table->string('status')->index(); // draft, active, expired, cancelled
        $table->decimal('total_amount', 15, 2);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
