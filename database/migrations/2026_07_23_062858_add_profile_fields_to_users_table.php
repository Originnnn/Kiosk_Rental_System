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
        Schema::table('users', function (Blueprint $table) {
            $table->date('dob')->nullable();
            $table->string('gender')->nullable(); // Nam, Nữ, Khác
            $table->string('id_card')->nullable();
            $table->string('phone')->nullable();
            $table->string('personal_email')->nullable();
            $table->text('address')->nullable();
            $table->string('employee_code')->nullable();
            $table->string('department')->nullable();
            $table->date('join_date')->nullable();
            $table->string('avatar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dob', 'gender', 'id_card', 'phone', 'personal_email',
                'address', 'employee_code', 'department', 'join_date', 'avatar'
            ]);
        });
    }
};
