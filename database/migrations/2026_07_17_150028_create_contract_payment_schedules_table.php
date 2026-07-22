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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('contract_id')->constrained('contracts');
        $table->decimal('amount', 15, 2)->unsigned(); // unsigned đảm bảo số > 0
        $table->date('payment_date');
        $table->string('method');
        $table->string('receipt_url')->nullable();
        $table->text('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_payment_schedules');
    }
};
