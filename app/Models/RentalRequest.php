<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class RentalRequest extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'desired_start' => 'date',
            'desired_end' => 'date',
        ];
    }

    public function kiosk()
    {
        return $this->belongsTo(Kiosk::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function files()
    {
        return $this->hasMany(RequestFile::class);
    }

    public function timeline()
    {
        return $this->hasMany(RequestTimeline::class)->orderBy('created_at');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}