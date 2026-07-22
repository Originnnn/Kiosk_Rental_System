<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('code', 'admin')->first();
        $staffRole = Role::where('code', 'staff')->first();
        $leaderRole = Role::where('code', 'leader')->first();

        // Tạo Admin
        User::firstOrCreate(
            ['email' => 'admin@local.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );

        // Tạo Lãnh đạo
        User::firstOrCreate(
            ['email' => 'leader@local.com'],
            [
                'name' => 'Giám đốc Bến xe',
                'password' => Hash::make('password123'),
                'role_id' => $leaderRole->id,
                'status' => 'active',
            ]
        );

        // Tạo 3 Nhân viên
        for ($i = 1; $i <= 3; $i++) {
            User::firstOrCreate(
                ['email' => "staff0{$i}@local.com"],
                [
                    'name' => "Nhân viên {$i}",
                    'password' => Hash::make('password123'),
                    'role_id' => $staffRole->id,
                    'status' => 'active',
                ]
            );
        }
    }
}
