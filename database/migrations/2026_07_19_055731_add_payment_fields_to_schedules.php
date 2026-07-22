<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_payment_schedules', function (Blueprint $table) {
            $table->decimal('actual_amount', 15, 2)->nullable()->after('amount');
            $table->string('payment_method')->nullable()->after('paid_at');
            $table->string('receipt_file')->nullable()->after('payment_method');
            $table->text('notes')->nullable()->after('receipt_file');
        });
    }

    public function down(): void
    {
        Schema::table('contract_payment_schedules', function (Blueprint $table) {
            $table->dropColumn(['actual_amount', 'payment_method', 'receipt_file', 'notes']);
        });
    }
};
