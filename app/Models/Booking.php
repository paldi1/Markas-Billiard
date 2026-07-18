<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'nama_pelanggan', 'tanggal', 'jam_mulai', 'durasi', 
        'total_bayar', 'status', 'bukti_pembayaran','waktu_panggil','meja' // Pastikan ini ada
    ];
}