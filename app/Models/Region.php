<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['nama_lokasi', 'tipe'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}   
