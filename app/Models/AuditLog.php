<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDescriptionAttribute()
    {
        $type = class_basename($this->target_type);
        $targetName = $this->metadata['target_name'] ?? "#" . $this->target_id;
        
        $actionText = 'Thao tác';
        if ($this->action === 'create') $actionText = 'Tạo mới';
        if ($this->action === 'update') $actionText = 'Cập nhật';
        if ($this->action === 'delete') $actionText = 'Xóa';
        if ($this->action === 'login') return 'Đăng nhập hệ thống';

        $typeText = '';
        if ($type === 'Contract') $typeText = 'hợp đồng';
        elseif ($type === 'Kiosk') $typeText = 'kiosk';
        elseif ($type === 'Customer') $typeText = 'khách hàng';
        elseif ($type === 'User') $typeText = 'người dùng';
        elseif ($type === 'Payment') $typeText = 'thanh toán';
        elseif ($type === 'RentalRequest') $typeText = 'yêu cầu thuê';
        elseif ($type === 'BookingRequest') $typeText = 'đăng ký thuê';
        
        return "{$actionText} {$typeText} {$targetName}";
    }

    public function getIconAttribute()
    {
        $type = class_basename($this->target_type);
        if ($this->action === 'login') return 'fa-right-to-bracket';
        if ($type === 'Contract') return 'fa-file-contract';
        if ($type === 'Kiosk') return 'fa-store';
        if ($type === 'Customer') return 'fa-user-tie';
        if ($type === 'User') return 'fa-user';
        if ($type === 'Payment') return 'fa-money-bill';
        return 'fa-circle-check';
    }

    public function getColorAttribute()
    {
        $type = class_basename($this->target_type);
        if ($this->action === 'login') return 'text-blue-600 bg-blue-50';
        if ($type === 'Contract') return 'text-orange-500 bg-orange-50';
        if ($type === 'Kiosk') return 'text-purple-600 bg-purple-50';
        if ($type === 'Customer') return 'text-green-600 bg-green-50';
        if ($type === 'Payment') return 'text-emerald-600 bg-emerald-50';
        return 'text-gray-600 bg-gray-100';
    }
}