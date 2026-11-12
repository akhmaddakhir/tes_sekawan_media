@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag(); @endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Buat Pesanan Kendaraan Baru') }}
                </h2>
                <p class="text-xs text-gray-500 mt-1">
                    Form pencatatan pemakaian armada nikel dan penunjukan 2 atasan penyetuju berjenjang.
                </p>
            </div>
            <a href="{{ route('bookings.index') }}" 
               class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-md transition-colors">
                &larr; Kembali ke Daftar
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">

        <!-- Error Alert -->
        @if (isset($errors) && $errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md text-sm text-red-800 shadow-xs">
                <p class="font-semibold">Mohon perbaiki data berikut sebelum menyimpan:</p>
                <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Pengajuan Pemesanan Kendaraan Operasional -->
        <div class="bg-white shadow-md overflow-hidden sm:rounded-lg p-6 sm:p-8">
            <form method="POST" action="{{ route('bookings.store') }}" class="space-y-6">
                @csrf

                <!-- Section 1: Data Pegawai Pemesan & Keperluan -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">
                        1. Informasi Pemohon & Keperluan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Nama Pemesan -->
                        <div class="md:col-span-2">
                            <label for="nama_pemesan" class="block font-medium text-xs text-gray-700 mb-1">
                                Nama Pegawai Pemesan <span class="text-red-500">*</span>
                            </label>
                            <input id="nama_pemesan" name="nama_pemesan" type="text" value="{{ old('nama_pemesan') }}" required autofocus
                                   placeholder="Contoh: Doni Darmawan (Divisi Eksplorasi)"
                                   class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm" />
                            @error('nama_pemesan')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Mulai -->
                        <div>
                            <label for="tanggal_mulai" class="block font-medium text-xs text-gray-700 mb-1">
                                Tanggal Mulai Pakai <span class="text-red-500">*</span>
                            </label>
                            <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required
                                   class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm" />
                            @error('tanggal_mulai')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label for="tanggal_selesai" class="block font-medium text-xs text-gray-700 mb-1">
                                Tanggal Selesai Pakai <span class="text-red-500">*</span>
                            </label>
                            <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai', date('Y-m-d')) }}" required
                                   class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm" />
                            @error('tanggal_selesai')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Keperluan Tugas -->
                        <div class="md:col-span-2">
                            <label for="keperluan" class="block font-medium text-xs text-gray-700 mb-1">
                                Keperluan / Tujuan Operasional <span class="text-red-500">*</span>
                            </label>
                            <textarea id="keperluan" name="keperluan" rows="3" required
                                      placeholder="Jelaskan agenda kerja, rute perjalanan, atau lokasi site yang dituju..."
                                      class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm">{{ old('keperluan') }}</textarea>
                            @error('keperluan')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Penugasan Unit Armada & Driver -->
                <div class="border-b border-gray-100 pb-5">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-4">
                        2. Penugasan Kendaraan & Driver
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Pilihan Kendaraan -->
                        <div>
                            <label for="vehicle_id" class="block font-medium text-xs text-gray-700 mb-1">
                                Pilih Unit Kendaraan (Tersedia) <span class="text-red-500">*</span>
                            </label>
                            <select id="vehicle_id" name="vehicle_id" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm">
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>
                                        {{ $v->nama_kendaraan }} ({{ $v->plat_nomor }}) - {{ $v->region->nama_lokasi ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-gray-400 mt-1 block">Hanya menampilkan kendaraan dengan status siap pakai</span>
                            @error('vehicle_id')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Pilihan Driver -->
                        <div>
                            <label for="driver_id" class="block font-medium text-xs text-gray-700 mb-1">
                                Pilih Driver (Tersedia) <span class="text-red-500">*</span>
                            </label>
                            <select id="driver_id" name="driver_id" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm">
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $d)
                                    <option value="{{ $d->id }}" {{ old('driver_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nama }} ({{ $d->no_sim }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-gray-400 mt-1 block">Hanya driver yang berstatus tersedia di pool</span>
                            @error('driver_id')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 3: Persetujuan Berjenjang (Level 1 & Level 2) -->
                <div class="pb-2">
                    <h3 class="text-sm font-semibold text-gray-800 uppercase tracking-wider mb-2">
                        3. Penetapan 2 Atasan Penyetuju (Multi-Level Approval)
                    </h3>
                    <p class="text-xs text-gray-500 mb-4">
                        Pemesanan wajib disetujui berjenjang: Atasan Level 1 harus menyetujui terlebih dahulu sebelum permohonan berlanjut ke Atasan Level 2.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Approver Level 1 -->
                        <div>
                            <label for="approver_1" class="block font-medium text-xs text-gray-700 mb-1">
                                Penyetuju Level 1 (Supervisor / Kepala Pool) <span class="text-red-500">*</span>
                            </label>
                            <select id="approver_1" name="approver_1" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm">
                                <option value="">-- Pilih Atasan Level 1 --</option>
                                @foreach($approvers as $app)
                                    <option value="{{ $app->id }}" {{ old('approver_1') == $app->id ? 'selected' : '' }}>
                                        {{ $app->name }} ({{ $app->jabatan ?? 'Approver' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('approver_1')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Approver Level 2 -->
                        <div>
                            <label for="approver_2" class="block font-medium text-xs text-gray-700 mb-1">
                                Penyetuju Level 2 (Manager Operasional / Tambang) <span class="text-red-500">*</span>
                            </label>
                            <select id="approver_2" name="approver_2" required
                                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-xs block w-full text-sm">
                                <option value="">-- Pilih Atasan Level 2 --</option>
                                @foreach($approvers as $app)
                                    <option value="{{ $app->id }}" {{ old('approver_2') == $app->id ? 'selected' : '' }}>
                                        {{ $app->name }} ({{ $app->jabatan ?? 'Approver' }})
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-gray-400 mt-1 block">Wajib berbeda dari penyetuju Level 1</span>
                            @error('approver_2')
                                <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Tombol Tindakan Simpan dan Batal -->
                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('bookings.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-xs hover:bg-gray-50 focus:outline-none transition ease-in-out duration-150">
                        Batal
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-5 py-2.5 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                        Simpan & Ajukan Pemesanan &rarr;
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-app-layout>
