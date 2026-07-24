<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends Model
{
    use HasFactory;
    use Auditable;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $year = date('Y');
                // Lấy ID lớn nhất hiện tại hoặc đếm tổng số
                $maxId = static::max('id') ?? 0;
                $nextId = $maxId + 1;
                $customer->customer_code = 'KH-' . $year . '-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }
}