<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contract extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'attachments' => 'array',
        ];
    }

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function paymentSchedules()
    {
        return $this->hasMany(ContractPaymentSchedule::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}