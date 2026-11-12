<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        <!-- Ringkasan Statistik Status Armada & Antrean Approval -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total unit armada dengan status siap digunakan di pool -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Tersedia</span>
                <div class="text-3xl font-bold text-gray-800 mt-1">{{ $kendaraanTersedia }}</div>
                <p class="text-xs text-gray-500 mt-1">Armada siap digunakan</p>
            </div>

            <!-- Total unit armada yang sedang beroperasi di perjalanan / site tambang -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Sedang Dipakai</span>
                <div class="text-3xl font-bold text-gray-800 mt-1">{{ $sedangDipakai }}</div>
                <p class="text-xs text-gray-500 mt-1">Dalam perjalanan / tugas</p>
            </div>

            <!-- Total pesanan aktif yang masih menunggu izin atasan berjenjang -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Menunggu Approval</span>
                <div class="text-3xl font-bold text-gray-800 mt-1">{{ $menungguPersetujuan }}</div>
                <p class="text-xs text-gray-500 mt-1">Proses izin bertingkat</p>
            </div>

            <!-- Tugas approval spesifik user yang login atau total armada perusahaan -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                @if(Auth::user()->role === 'approver')
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Tugas Saya</span>
                    <div class="text-3xl font-bold text-gray-800 mt-1">{{ $tugasApprovalSaya }}</div>
                    <p class="text-xs text-gray-500 mt-1">Perlu persetujuan Anda</p>
                @else
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Armada</span>
                    <div class="text-3xl font-bold text-gray-800 mt-1">{{ $kendaraanTersedia + $sedangDipakai }}</div>
                    <p class="text-xs text-gray-500 mt-1">Unit yang terdata</p>
                @endif
            </div>
        </div>

        <!-- Visualisasi Grafik Batang: Frekuensi Riwayat Pemakaian Armada -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-1">Frekuensi Pemakaian Kendaraan</h3>
            <p class="text-xs text-gray-500 mb-4">Grafik jumlah riwayat pemakaian per unit armada.</p>
            <div class="relative w-full h-64">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

        <!-- Daftar 5 Transaksi Pemesanan Terakhir Beserta Status Approval Berjenjang -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Pemesanan Terkini</h3>
                    <p class="text-xs text-gray-500">5 pengajuan pemesanan terbaru dalam sistem.</p>
                </div>
                <a href="{{ route('bookings.index') }}" class="text-xs font-semibold text-gray-800 hover:text-black underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 font-semibold uppercase text-[11px]">
                            <th class="py-3 px-3">Kode & Pemesan</th>
                            <th class="py-3 px-3">Kendaraan</th>
                            <th class="py-3 px-3">Driver</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3 text-center">Approval</th>
                            <th class="py-3 px-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($recentBookings as $b)
                            <tr>
                                <td class="py-3 px-3">
                                    <span class="font-bold text-gray-900 block font-mono">{{ $b->kode_booking }}</span>
                                    <span class="text-gray-500 text-[11px]">{{ $b->nama_pemesan }}</span>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="font-medium text-gray-800">{{ $b->vehicle->nama_kendaraan ?? '-' }}</span>
                                    <span class="block text-[11px] text-gray-400">{{ $b->vehicle->plat_nomor ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-3">{{ $b->driver->nama ?? '-' }}</td>
                                <td class="py-3 px-3">
                                    {{ \Carbon\Carbon::parse($b->tanggal_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($b->tanggal_selesai)->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="inline-flex space-x-1">
                                        @foreach($b->approvals->sortBy('level') as $app)
                                            <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold border 
                                                {{ $app->status === 'approved' ? 'bg-green-50 text-green-700 border-green-200' : ($app->status === 'rejected' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-gray-50 text-gray-600 border-gray-200') }}">
                                                L{{ $app->level }}: {{ strtoupper($app->status) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[11px] font-medium 
                                        {{ $b->status_akhir === 'approved' ? 'bg-green-100 text-green-800' : ($b->status_akhir === 'rejected' ? 'bg-red-100 text-red-800' : ($b->status_akhir === 'completed' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ ucfirst($b->status_akhir) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-400">
                                    Belum ada data pemesanan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pustaka visualisasi grafik Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('usageChart');
            if (ctx) {
                const labels = @json($chartLabels);
                const data = @json($chartData);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Jumlah Trip',
                            data: data,
                            backgroundColor: 'rgba(31, 41, 55, 0.85)', // Warna batang grafik (slate dark)
                            borderColor: 'rgb(17, 24, 39)',
                            borderWidth: 1,
                            borderRadius: 4,
                            maxBarThickness: 36
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1, precision: 0 },
                                grid: { color: 'rgba(243, 244, 246, 1)' }
                            },
                            x: {
                                grid: { display: false }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
