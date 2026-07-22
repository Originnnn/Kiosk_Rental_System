<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kiosk extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function position()
    {
        return $this->hasOne(KioskPosition::class);
    }

    public function images()
    {
        return $this->hasMany(KioskImage::class)->orderBy('sort_order');
    }

    public function rentalRequests()
    {
        return $this->hasMany(RentalRequest::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}