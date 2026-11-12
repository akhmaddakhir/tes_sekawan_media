<?php

namespace App\Exports;

use App\Models\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BookingsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Booking::with(['vehicle.region', 'driver', 'approvals.approver']);

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal_mulai', [$this->startDate, $this->endDate]);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Kode Booking',
            'Nama Pemesan',
            'Kendaraan',
            'Plat Nomor',
            'Lokasi Operasional',
            'Driver',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Keperluan',
            'BBM Terpakai (Liter)',
            'Status Akhir',
        ];
    }

    public function map($booking): array
    {
        return [
            $booking->kode_booking,
            $booking->nama_pemesan,
            $booking->vehicle->nama_kendaraan,
            $booking->vehicle->plat_nomor,
            $booking->vehicle->region->nama_lokasi,
            $booking->driver->nama,
            $booking->tanggal_mulai,
            $booking->tanggal_selesai,
            $booking->keperluan,
            $booking->bbm_terpakai_liter ?? 0,
            strtoupper($booking->status_akhir),
        ];
    }
}