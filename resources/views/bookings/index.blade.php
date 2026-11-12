@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Pemesanan Kendaraan') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Kelola permohonan kendaraan, persetujuan bertingkat atasan, dan pengembalian unit armada.
                </p>
            </div>
            @if(Auth::user()->role === 'admin')
                <div>
                    <a href="{{ route('bookings.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Pesanan Baru
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-md text-sm text-green-800 shadow-xs flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md text-sm text-red-800 shadow-xs">
                <p class="font-semibold">Terjadi kesalahan pada aksi sebelumnya:</p>
                <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Filter Laporan Berdasarkan Rentang Periode Tanggal -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">Filter Laporan Periodik</h3>
            <form method="GET" action="{{ route('bookings.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                <div>
                    <label for="start_date" class="block font-medium text-xs text-gray-700 mb-1">Dari Tanggal</label>
                    <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                           class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-xs" />
                </div>

                <div>
                    <label for="end_date" class="block font-medium text-xs text-gray-700 mb-1">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                           class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-xs" />
                </div>

                <div class="flex items-center space-x-2">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150">
                        Filter
                    </button>
                    @if(request()->filled('start_date') || request()->filled('end_date'))
                        <a href="{{ route('bookings.index') }}" 
                           class="inline-flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md transition-colors">
                            Reset
                        </a>
                    @endif
                </div>

                <div class="sm:text-right">
                    <a href="{{ route('bookings.export', request()->only(['start_date', 'end_date'])) }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-xs hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-1.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Ekspor Excel (.xlsx)
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Riwayat Pemesanan, Persetujuan Berjenjang, dan Penyelesaian Armada -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-semibold text-gray-800">Daftar Pengajuan & Riwayat Pemesanan</h3>
                <span class="text-xs text-gray-500">Total: <strong>{{ $bookings->count() }}</strong> data</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 font-semibold uppercase text-[11px] bg-gray-50">
                            <th class="py-3 px-3">Kode & Pemesan</th>
                            <th class="py-3 px-3">Kendaraan & Wilayah</th>
                            <th class="py-3 px-3">Driver</th>
                            <th class="py-3 px-3">Jadwal Pakai</th>
                            <th class="py-3 px-3 text-center">Persetujuan Berjenjang</th>
                            <th class="py-3 px-3 text-center">Status Akhir & Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($bookings as $b)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Kode & Pemesan -->
                                <td class="py-3.5 px-3 align-top">
                                    <span class="font-bold text-gray-900 block font-mono text-[11px]">{{ $b->kode_booking }}</span>
                                    <div class="font-medium text-gray-800 mt-0.5">{{ $b->nama_pemesan }}</div>
                                    <p class="text-[11px] text-gray-500 mt-1 max-w-xs break-words italic">"{{ $b->keperluan }}"</p>
                                </td>

                                <!-- Kendaraan & Wilayah -->
                                <td class="py-3.5 px-3 align-top">
                                    <span class="font-semibold text-gray-800 block">{{ $b->vehicle->nama_kendaraan ?? '-' }}</span>
                                    <span class="text-gray-500 font-mono text-[11px]">{{ $b->vehicle->plat_nomor ?? '-' }}</span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        {{ $b->vehicle->region->nama_lokasi ?? '-' }} ({{ ucfirst($b->vehicle->kepemilikan ?? '-') }})
                                    </div>
                                </td>

                                <!-- Driver -->
                                <td class="py-3.5 px-3 align-top">
                                    <span class="font-medium text-gray-800 block">{{ $b->driver->nama ?? '-' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $b->driver->no_sim ?? '' }}</span>
                                </td>

                                <!-- Jadwal -->
                                <td class="py-3.5 px-3 align-top whitespace-nowrap">
                                    <div><strong>Mulai:</strong> {{ \Carbon\Carbon::parse($b->tanggal_mulai)->format('d M Y') }}</div>
                                    <div><strong>Selesai:</strong> {{ \Carbon\Carbon::parse($b->tanggal_selesai)->format('d M Y') }}</div>
                                    <div class="text-[10px] text-gray-400 mt-0.5">
                                        Durasi: {{ \Carbon\Carbon::parse($b->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($b->tanggal_selesai)) + 1 }} hari
                                    </div>
                                </td>

                                <!-- Persetujuan Berjenjang 2 Level -->
                                <td class="py-3.5 px-3 align-top">
                                    <div class="space-y-2 min-w-[210px]">
                                        @foreach($b->approvals->sortBy('level') as $app)
                                            <div class="p-2 rounded-md border text-[11px] {{ $app->status === 'approved' ? 'bg-green-50/60 border-green-200' : ($app->status === 'rejected' ? 'bg-red-50/60 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-semibold text-gray-700">Level {{ $app->level }}: {{ $app->approver->name ?? '-' }}</span>
                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold uppercase 
                                                        {{ $app->status === 'approved' ? 'bg-green-200 text-green-900' : ($app->status === 'rejected' ? 'bg-red-200 text-red-900' : 'bg-amber-100 text-amber-900') }}">
                                                        {{ $app->status }}
                                                    </span>
                                                </div>

                                                @if($app->catatan)
                                                    <div class="text-[10px] text-gray-500 mt-1 italic">Catatan: {{ $app->catatan }}</div>
                                                @endif

                                                <!-- Form Aksi Approver (Hanya jika user login adalah approver yang ditunjuk & status masih pending) -->
                                                @if(Auth::id() === $app->approver_id && $app->status === 'pending')
                                                    <div class="mt-2 pt-2 border-t border-gray-200">
                                                        @if($app->canApprove())
                                                            <div x-data="{ openReject: false }">
                                                                <div class="flex items-center space-x-1.5">
                                                                    <!-- Tombol Setujui Langsung -->
                                                                    <form method="POST" action="{{ route('approvals.approve', $app) }}" class="inline">
                                                                        @csrf
                                                                        <button type="submit" 
                                                                                class="px-2.5 py-1 bg-green-600 hover:bg-green-700 text-white font-semibold text-[11px] rounded transition-colors shadow-xs"
                                                                                onclick="return confirm('Apakah Anda yakin menyetujui pemesanan ini?')">
                                                                            ✓ Setujui
                                                                        </button>
                                                                    </form>

                                                                    <!-- Tombol Buka Input Tolak -->
                                                                    <button type="button" @click="openReject = !openReject" 
                                                                            class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white font-semibold text-[11px] rounded transition-colors shadow-xs">
                                                                        ✕ Tolak
                                                                    </button>
                                                                </div>

                                                                <!-- Form Alasan Penolakan -->
                                                                <div x-show="openReject" class="mt-2 p-2 bg-white rounded border border-red-200" style="display: none;">
                                                                    <form method="POST" action="{{ route('approvals.reject', $app) }}">
                                                                        @csrf
                                                                        <label class="block text-[10px] font-semibold text-gray-600 mb-1">Alasan Penolakan:</label>
                                                                        <input type="text" name="catatan" required placeholder="Contoh: Jadwal bertabrakan"
                                                                               class="w-full text-xs border-gray-300 rounded px-2 py-1 mb-1.5 focus:ring-red-500 focus:border-red-500" />
                                                                        <div class="flex justify-end space-x-1">
                                                                            <button type="button" @click="openReject = false" class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px]">Batal</button>
                                                                            <button type="submit" class="px-2 py-0.5 bg-red-600 text-white rounded text-[10px] font-semibold">Kirim Penolakan</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-[10px] text-amber-700 font-medium flex items-center">
                                                                <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                                </svg>
                                                                Menunggu persetujuan Level 1
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Status Akhir & Input BBM (Admin) -->
                                <td class="py-3.5 px-3 align-top text-center">
                                    <div class="space-y-2">
                                        <!-- Badge Status Akhir -->
                                        @if($b->status_akhir === 'approved')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-100 text-green-800">
                                                Disetujui / Jalan
                                            </span>
                                        @elseif($b->status_akhir === 'rejected')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                        @elseif($b->status_akhir === 'completed')
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                                Selesai
                                            </span>
                                            @if($b->bbm_terpakai_liter)
                                                <div class="text-[11px] text-gray-500 font-medium mt-1">
                                                    BBM: <strong>{{ number_format($b->bbm_terpakai_liter, 1) }} L</strong>
                                                </div>
                                            @endif
                                        @else
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                                Pending Izin
                                            </span>
                                        @endif

                                        <!-- Form Selesai Pakai & Input BBM (Hanya untuk Admin saat status approved) -->
                                        @if(Auth::user()->role === 'admin' && $b->status_akhir === 'approved')
                                            <div x-data="{ openComplete: false }" class="mt-2 text-left">
                                                <button type="button" @click="openComplete = !openComplete"
                                                        class="w-full px-2.5 py-1.5 bg-gray-800 hover:bg-gray-700 text-white font-semibold text-[10px] rounded transition-colors text-center block">
                                                    Input BBM & Selesai &rarr;
                                                </button>

                                                <div x-show="openComplete" class="mt-2 p-2.5 bg-gray-50 rounded-md border border-gray-200 shadow-xs" style="display: none;">
                                                    <form method="POST" action="{{ route('bookings.complete', $b) }}">
                                                        @csrf
                                                        <label class="block text-[10px] font-semibold text-gray-700 mb-1">
                                                            BBM Terpakai (Liter):
                                                        </label>
                                                        <input type="number" step="0.1" min="0" name="bbm_terpakai_liter" required placeholder="Contoh: 35.5"
                                                               class="w-full text-xs border-gray-300 rounded px-2 py-1 mb-2 focus:ring-gray-800 focus:border-gray-800" />
                                                        
                                                        <div class="flex justify-end space-x-1">
                                                            <button type="button" @click="openComplete = false" class="px-2 py-0.5 bg-white border border-gray-300 text-gray-600 rounded text-[10px]">
                                                                Batal
                                                            </button>
                                                            <button type="submit" class="px-2 py-0.5 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded text-[10px]">
                                                                Selesai Pakai
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada data pemesanan yang sesuai dengan filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
