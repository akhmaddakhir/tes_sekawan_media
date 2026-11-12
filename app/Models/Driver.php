<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = ['nama', 'no_sim', 'status'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}   