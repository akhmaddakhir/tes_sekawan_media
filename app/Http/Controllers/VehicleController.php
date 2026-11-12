<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;

class VehicleController extends Controller
{
    /**
     * Menampilkan daftar monitoring armada kendaraan, lokasi penempatan, rasio & konsumsi BBM, serta jadwal servis.
     */
    public function index()
    {
        // Memuat unit kendaraan beserta relasi wilayah dan seluruh riwayat pemesanan yang telah selesai
        $vehicles = Vehicle::with([
            'region',
            'bookings' => function ($q) {
                $q->where('status_akhir', 'completed');
            }
        ])->get();

        return view('vehicles.index', compact('vehicles'));
    }
}
