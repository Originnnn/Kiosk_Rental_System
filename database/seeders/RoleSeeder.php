<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'code' => 'admin',
                'name' => 'Quản trị viên',
                'description' => 'Quản lý tài khoản, phân quyền, xem audit log'
            ],
            [
                'code' => 'staff',
                'name' => 'Nhân viên vận hành',
                'description' => 'Xử lý yêu cầu, tạo hợp đồng, ghi nhận thanh toán'
            ],
            [
                'code' => 'leader',
                'name' => 'Lãnh đạo',
                'description' => 'Xem dashboard báo cáo và export dữ liệu (Read-only)'
            ],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['code' => $role['code']], $role);
        }
    }
}