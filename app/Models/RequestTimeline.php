<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestTimeline extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'metadata' => 'array', // Tự động parse JSON thành Array khi truy xuất
        ];
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }
    
    // Tùy chọn bổ sung liên kết đến User (nhân viên) thực hiện hành động
    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}