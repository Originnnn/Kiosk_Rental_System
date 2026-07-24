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
        Schema::table('kiosks', function (Blueprint $table) {
            $table->string('power_supply')->nullable()->after('status');
            $table->string('water_supply')->nullable()->after('power_supply');
            $table->string('internet_connection')->nullable()->after('water_supply');
            $table->string('air_conditioning')->nullable()->after('internet_connection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kiosks', function (Blueprint $table) {
            $table->dropColumn(['power_supply', 'water_supply', 'internet_connection', 'air_conditioning']);
        });
    }
};
