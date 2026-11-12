<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'kode_booking',
        'vehicle_id',
        'driver_id',
        'created_by',
        'nama_pemesan',
        'tanggal_mulai',
        'tanggal_selesai',
        'keperluan',
        'bbm_terpakai_liter',
        'status_akhir',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(BookingApproval::class)->orderBy('level');
    }
}