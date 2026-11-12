<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->string('plat_nomor')->unique();
            $table->string('nama_kendaraan');
            $table->enum('jenis', ['angkutan_orang', 'angkutan_barang']);
            $table->enum('kepemilikan', ['perusahaan', 'sewa']);
            $table->decimal('konsumsi_bbm_per_km', 5, 2)->default(10.00);
            $table->date('jadwal_service_berikutnya');
            $table->enum('status', ['tersedia', 'dipakai', 'service'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
