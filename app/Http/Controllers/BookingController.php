<?php

namespace App\Http\Controllers;

use App\Exports\BookingsExport;
use App\Models\Booking;
use App\Models\BookingApproval;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    /**
     * Menampilkan daftar pemesanan kendaraan dengan filter rentang tanggal.
     */
    public function index(Request $request)
    {
        $query = Booking::with(['vehicle.region', 'driver', 'approvals.approver']);

        // Filter berdasarkan rentang tanggal mulai
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('tanggal_mulai', [$request->start_date, $request->end_date]);
        }

        $bookings = $query->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Menampilkan form pembuatan pesanan baru (khusus Admin Pool).
     */
    public function create()
    {
        abort_if(Auth::user()->role !== 'admin', 403, 'Hanya Admin Pool yang dapat membuat pesanan.');

        // Hanya mengambil unit armada dan driver yang berstatus tersedia
        $vehicles = Vehicle::with('region')->where('status', 'tersedia')->get();
        $drivers = Driver::where('status', 'tersedia')->get();
        $approvers = User::where('role', 'approver')->get();

        return view('bookings.create', compact('vehicles', 'drivers', 'approvers'));
    }

    /**
     * Menyimpan data pemesanan baru dan membuat 2 level persetujuan berjenjang.
     */
    public function store(Request $request)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'nama_pemesan' => 'required|string|max:255',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keperluan' => 'required|string',
            'approver_1' => 'required|exists:users,id|different:approver_2',
            'approver_2' => 'required|exists:users,id',
        ]);

        DB::transaction(function () use ($request) {
            $booking = Booking::create([
                'kode_booking' => 'BKG-' . now()->format('Ymd') . '-' . rand(100, 999),
                'vehicle_id' => $request->vehicle_id,
                'driver_id' => $request->driver_id,
                'created_by' => Auth::id(),
                'nama_pemesan' => $request->nama_pemesan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'keperluan' => $request->keperluan,
                'status_akhir' => 'pending',
            ]);

            // Persetujuan Berjenjang: Level 1 (Supervisor/Kepala Pool)
            BookingApproval::create([
                'booking_id' => $booking->id,
                'approver_id' => $request->approver_1,
                'level' => 1,
                'status' => 'pending',
            ]);

            // Persetujuan Berjenjang: Level 2 (Manager Operasional/Tambang)
            BookingApproval::create([
                'booking_id' => $booking->id,
                'approver_id' => $request->approver_2,
                'level' => 2,
                'status' => 'pending',
            ]);

            Log::info("Pemesanan baru dibuat [{$booking->kode_booking}] oleh User: " . Auth::user()->name);
        });

        return redirect()->route('bookings.index')->with('success', 'Pesanan berhasil dibuat dan dikirim ke Atasan Level 1.');
    }

    /**
     * Memproses persetujuan permohonan oleh atasan berwenang.
     */
    public function approve(Request $request, BookingApproval $approval)
    {
        abort_if($approval->approver_id !== Auth::id(), 403, 'Anda tidak berwenang menyetujui pesanan ini.');
        abort_if(!$approval->canApprove(), 403, 'Menunggu persetujuan level sebelumnya.');

        DB::transaction(function () use ($request, $approval) {
            $approval->update([
                'status' => 'approved',
                'catatan' => $request->catatan,
                'approved_at' => now(),
            ]);

            // Periksa apakah semua level persetujuan sudah disahkan
            $masihPending = BookingApproval::where('booking_id', $approval->booking_id)
                ->where('status', '!=', 'approved')
                ->exists();

            if (!$masihPending) {
                $booking = $approval->booking;
                $booking->update(['status_akhir' => 'approved']);
                $booking->vehicle->update(['status' => 'dipakai']);
                $booking->driver->update(['status' => 'bertugas']);

                Log::info("Pemesanan [{$booking->kode_booking}] disetujui penuh. Status unit & driver berubah menjadi bertugas.");
            } else {
                Log::info("Persetujuan Level {$approval->level} untuk pesanan [{$approval->booking->kode_booking}] disetujui.");
            }
        });

        return back()->with('success', 'Pemesanan kendaraan berhasil disetujui.');
    }

    /**
     * Memproses penolakan permohonan oleh atasan.
     */
    public function reject(Request $request, BookingApproval $approval)
    {
        abort_if($approval->approver_id !== Auth::id(), 403);
        abort_if(!$approval->canApprove(), 403);

        DB::transaction(function () use ($request, $approval) {
            $approval->update([
                'status' => 'rejected',
                'catatan' => $request->catatan ?? 'Ditolak oleh atasan',
                'approved_at' => now(),
            ]);

            // Jika satu level menolak, status akhir pemesanan otomatis ditolak
            $approval->booking->update(['status_akhir' => 'rejected']);

            Log::warning("Pemesanan [{$approval->booking->kode_booking}] ditolak pada Level {$approval->level}. Alasan: {$approval->catatan}");
        });

        return back()->with('success', 'Pemesanan kendaraan telah ditolak.');
    }

    /**
     * Menyelesaikan perjalanan operasional, mencatat konsumsi BBM, dan memulihkan status armada & driver.
     */
    public function complete(Request $request, Booking $booking)
    {
        abort_if(Auth::user()->role !== 'admin', 403);

        $request->validate([
            'bbm_terpakai_liter' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, $booking) {
            $booking->update([
                'bbm_terpakai_liter' => $request->bbm_terpakai_liter,
                'status_akhir' => 'completed',
            ]);

            // Mengembalikan unit kendaraan dan driver menjadi siap pakai (tersedia)
            $booking->vehicle->update(['status' => 'tersedia']);
            $booking->driver->update(['status' => 'tersedia']);

            Log::info("Pemesanan [{$booking->kode_booking}] telah selesai. BBM terpakai: {$booking->bbm_terpakai_liter} L. Unit kembali tersedia.");
        });

        return back()->with('success', 'Perjalanan selesai! Kendaraan & driver kembali tersedia.');
    }

    /**
     * Mengekspor laporan pemesanan ke format file Microsoft Excel (.xlsx).
     */
    public function export(Request $request)
    {
        Log::info("Laporan pemesanan diekspor ke Excel oleh: " . Auth::user()->name);

        return Excel::download(
            new BookingsExport($request->start_date, $request->end_date),
            'laporan-pemesanan-armadanikel-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
