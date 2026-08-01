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
            $table->string('floor')->nullable()->after('area');
            $table->string('kiosk_type')->nullable()->after('floor');
            $table->string('min_term')->nullable()->after('kiosk_type');
            $table->json('features')->nullable()->after('min_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kiosks', function (Blueprint $table) {
            $table->dropColumn(['floor', 'kiosk_type', 'min_term', 'features']);
        });
    }
};
