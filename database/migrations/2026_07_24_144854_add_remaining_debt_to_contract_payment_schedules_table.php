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
            $table->decimal('remaining_debt', 15, 2)->default(0)->after('actual_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_payment_schedules', function (Blueprint $table) {
            $table->dropColumn('remaining_debt');
        });
    }
};
