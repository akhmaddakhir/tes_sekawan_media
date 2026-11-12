<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'region_id',
        'plat_nomor',
        'nama_kendaraan',
        'jenis',
        'kepemilikan',
        'konsumsi_bbm_per_km',
        'jadwal_service_berikutnya',
        'status',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
