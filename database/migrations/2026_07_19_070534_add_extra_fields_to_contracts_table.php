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
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_request_id')->nullable()->change();
            
            $table->string('payment_cycle')->nullable()->after('total_amount'); // Ví dụ: 1, 3, 6, 12
            $table->decimal('deposit_amount', 15, 2)->nullable()->after('payment_cycle');
            $table->string('manager_name')->nullable()->after('deposit_amount');
            $table->string('contact_name')->nullable()->after('manager_name');
            $table->string('contact_phone')->nullable()->after('contact_name');
            $table->text('notes')->nullable()->after('contact_phone');
            $table->json('attachments')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_request_id')->nullable(false)->change();
            
            $table->dropColumn([
                'payment_cycle',
                'deposit_amount',
                'manager_name',
                'contact_name',
                'contact_phone',
                'notes',
                'attachments'
            ]);
        });
    }
};
