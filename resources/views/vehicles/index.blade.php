@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Monitoring Armada Kendaraan') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Visibilitas kesiapan armada nikel, sebaran wilayah kantor & tambang, konsumsi BBM, serta jadwal perawatan unit.
                </p>
            </div>
            @if(Auth::user()->role === 'admin')
                <div>
                    <a href="{{ route('bookings.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                        + Buat Pesanan
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Ringkasan Status Kesiapan Seluruh Unit Armada Tambang -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- 1. Total Armada -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Total Armada</span>
                <div class="text-3xl font-bold text-gray-800 mt-1">{{ $vehicles->count() }}</div>
                <p class="text-xs text-gray-500 mt-1">Unit terdata di seluruh site</p>
            </div>

            <!-- 2. Tersedia -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Siap Beroperasi</span>
                <div class="text-3xl font-bold text-green-700 mt-1">{{ $vehicles->where('status', 'tersedia')->count() }}</div>
                <p class="text-xs text-gray-500 mt-1">Tersedia di pool</p>
            </div>

            <!-- 3. Sedang Dipakai -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Sedang Bertugas</span>
                <div class="text-3xl font-bold text-blue-700 mt-1">{{ $vehicles->where('status', 'dipakai')->count() }}</div>
                <p class="text-xs text-gray-500 mt-1">Dalam dinas / site tambang</p>
            </div>

            <!-- 4. Dalam Perawatan -->
            <div class="bg-white shadow-md overflow-hidden sm:rounded-lg px-6 py-5">
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Dalam Perawatan</span>
                <div class="text-3xl font-bold text-amber-700 mt-1">{{ $vehicles->where('status', 'service')->count() }}</div>
                <p class="text-xs text-gray-500 mt-1">Sedang servis berkala</p>
            </div>
        </div>

        <!-- Tabel Monitoring Lengkap (7 Informasi Utama) -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Tabel Monitoring & Status Kondisi Unit</h3>
                    <p class="text-xs text-gray-500">Data terperinci identitas, lokasi penempatan, rasio & pemakaian BBM, jadwal servis, dan riwayat pakai.</p>
                </div>
                <span class="text-xs font-medium px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md border border-gray-200">
                    {{ $vehicles->count() }} Unit Kendaraan
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-gray-200 text-gray-500 font-semibold uppercase text-[11px] bg-gray-50">
                            <th class="py-3 px-3">1. Kendaraan & Plat</th>
                            <th class="py-3 px-3">2. Lokasi Penempatan</th>
                            <th class="py-3 px-3">3. Jenis & Kepemilikan</th>
                            <th class="py-3 px-3">4. Konsumsi BBM</th>
                            <th class="py-3 px-3">5. Jadwal Service</th>
                            <th class="py-3 px-3 text-center">6. Riwayat Pakai</th>
                            <th class="py-3 px-3 text-center">7. Status Unit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($vehicles as $v)
                            @php
                                $totalBbmTerpakai = $v->bookings->sum('bbm_terpakai_liter');
                                $totalTripSelesai = $v->bookings->count();
                                $serviceDate = \Carbon\Carbon::parse($v->jadwal_service_berikutnya);
                                $isServiceNear = $serviceDate->isPast() || $serviceDate->diffInDays(now()) <= 7;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- 1. Kendaraan & Plat Nomor -->
                                <td class="py-3.5 px-3 align-top">
                                    <div class="font-bold text-gray-900 text-sm leading-tight">{{ $v->nama_kendaraan }}</div>
                                    <span class="inline-block mt-1 font-mono font-semibold px-2 py-0.5 rounded bg-gray-100 text-gray-800 text-[11px] border border-gray-200">
                                        {{ $v->plat_nomor }}
                                    </span>
                                </td>

                                <!-- 2. Lokasi Penempatan -->
                                <td class="py-3.5 px-3 align-top">
                                    <span class="font-semibold text-gray-800 block">{{ $v->region->nama_lokasi ?? '-' }}</span>
                                    @if(optional($v->region)->tipe === 'pusat')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800 mt-1">
                                            Kantor Pusat
                                        </span>
                                    @elseif(optional($v->region)->tipe === 'cabang')
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800 mt-1">
                                            Kantor Cabang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-emerald-100 text-emerald-800 mt-1">
                                            Lokasi Tambang
                                        </span>
                                    @endif
                                </td>

                                <!-- 3. Jenis & Kepemilikan -->
                                <td class="py-3.5 px-3 align-top">
                                    <div class="text-gray-800 font-medium">
                                        {{ $v->jenis === 'angkutan_orang' ? 'Angkutan Orang' : 'Angkutan Barang' }}
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        Status: <strong class="{{ $v->kepemilikan === 'perusahaan' ? 'text-gray-700' : 'text-amber-700' }}">{{ $v->kepemilikan === 'perusahaan' ? 'Milik Perusahaan' : 'Sewa Rekanan' }}</strong>
                                    </div>
                                </td>

                                <!-- 4. Konsumsi BBM -->
                                <td class="py-3.5 px-3 align-top">
                                    <div class="text-gray-800">
                                        Rasio: <strong>{{ number_format($v->konsumsi_bbm_per_km, 1) }} km/L</strong>
                                    </div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        Total Terpakai: <span class="font-semibold text-gray-700">{{ number_format($totalBbmTerpakai, 1) }} L</span>
                                    </div>
                                </td>

                                <!-- 5. Jadwal Service -->
                                <td class="py-3.5 px-3 align-top whitespace-nowrap">
                                    <div class="font-medium {{ $isServiceNear ? 'text-amber-700 font-semibold' : 'text-gray-800' }}">
                                        {{ $serviceDate->format('d M Y') }}
                                    </div>
                                    @if($serviceDate->isPast())
                                        <span class="inline-block text-[10px] text-red-600 font-semibold mt-0.5">⚠️ Terlewat jadwal</span>
                                    @elseif($serviceDate->diffInDays(now()) <= 14)
                                        <span class="inline-block text-[10px] text-amber-600 mt-0.5">{{ $serviceDate->diffForHumans() }}</span>
                                    @else
                                        <span class="inline-block text-[10px] text-gray-400 mt-0.5">{{ $serviceDate->diffForHumans() }}</span>
                                    @endif
                                </td>

                                <!-- 6. Riwayat Pakai -->
                                <td class="py-3.5 px-3 align-top text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                        {{ $totalTripSelesai }} Trip Selesai
                                    </span>
                                </td>

                                <!-- 7. Status Unit (Real-Time Badge) -->
                                <td class="py-3.5 px-3 align-top text-center">
                                    @if($v->status === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                                            Tersedia
                                        </span>
                                    @elseif($v->status === 'dipakai')
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5"></span>
                                            Sedang Dipakai
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                            Dalam Service
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-400">
                                    Belum ada data unit kendaraan yang terdaftar dalam sistem.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
