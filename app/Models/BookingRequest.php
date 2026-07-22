<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRequest extends Model
{
    protected $fillable = [
        'kiosk_id',
        'customer_name',
        'phone',
        'business_type',
        'duration_months',
        'status',
        'handled_by'
    ];

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class);
    }

    public function handler()
    {
        return $this->belongsTo(User::class, 'handled_by');
    }
}
