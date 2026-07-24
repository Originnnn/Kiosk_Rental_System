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
        Schema::table('contract_payment_schedules', function (Blueprint $table) {
            $table->text('receipt_file')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_payment_schedules', function (Blueprint $table) {
            $table->string('receipt_file', 255)->nullable()->change();
        });
    }
};
