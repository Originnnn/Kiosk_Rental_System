<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KioskImage extends Model
{
    protected $guarded = [];

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class);
    }
}