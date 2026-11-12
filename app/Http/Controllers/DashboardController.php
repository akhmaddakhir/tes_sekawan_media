<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Menghitung ringkasan status kesiapan armada kendaraan
        $kendaraanTersedia = Vehicle::where('status', 'tersedia')->count();
        $sedangDipakai = Vehicle::where('status', 'dipakai')->count();
        $menungguPersetujuan = Booking::where('status_akhir', 'pending')->count();

        // Menghitung jumlah permohonan yang sedang menunggu tindakan persetujuan user saat ini
        $tugasApprovalSaya = 0;
        if ($user->role === 'approver') {
            $pendingApprovals = BookingApproval::where('approver_id', $user->id)
                ->where('status', 'pending')
                ->get();

            foreach ($pendingApprovals as $item) {
                if ($item->canApprove()) {
                    $tugasApprovalSaya++;
                }
            }
        }

        // Data agregasi frekuensi riwayat pemakaian per unit kendaraan untuk grafik Chart.js
        $vehicles = Vehicle::withCount('bookings')->get();
        $chartLabels = $vehicles->pluck('nama_kendaraan');
        $chartData = $vehicles->pluck('bookings_count');

        // Mengambil 5 data transaksi pemesanan terbaru untuk tabel ringkasan dashboard
        $recentBookings = Booking::with(['vehicle', 'driver', 'approvals.approver'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'kendaraanTersedia',
            'sedangDipakai',
            'menungguPersetujuan',
            'tugasApprovalSaya',
            'chartLabels',
            'chartData',
            'recentBookings'
        ));
    }
}
