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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_code', 50)->nullable()->unique()->after('id');
            $table->string('id_card_number', 50)->nullable()->unique()->after('phone');
            $table->string('id_card_front')->nullable()->after('id_card_number');
            $table->string('id_card_back')->nullable()->after('id_card_front');
            $table->string('status')->default('active')->after('address'); // active, inactive, pending
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'customer_code',
                'id_card_number',
                'id_card_front',
                'id_card_back',
                'status'
            ]);
        });
    }
};
