<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            // Data Form Pelanggan
            $table->string('nama_pelanggan');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->integer('durasi');
            
            // Kolom Tambahan agar Controller tidak error
            $table->integer('total_bayar')->default(0); 
            $table->timestamp('waktu_mulai')->nullable();
            $table->timestamp('waktu_selesai')->nullable();
            
            // Data Sistem & Antrian
            $table->integer('no_antrian')->nullable();
            $table->time('estimasi_waktu')->nullable();
            // Pastikan tabel 'tables' sudah ada sebelum menjalankan ini
            $table->foreignId('table_id')->nullable(); 
            
            // Data Pembayaran & Status
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', [
                'menunggu_pembayaran', 'menunggu_verifikasi', 'antri', 'bermain', 'selesai', 'dibatalkan'
            ])->default('menunggu_pembayaran');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};