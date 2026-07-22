<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFile extends Model
{
    protected $guarded = [];

    public function rentalRequest()
    {
        return $this->belongsTo(RentalRequest::class);
    }
}