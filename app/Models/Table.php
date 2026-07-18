<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    // Menentukan nama tabel di database
    protected $table = 'tables';

    // Kolom-kolom yang boleh diisi datanya
    protected $fillable = [
        'nomor_meja',
        'status', 
    ];

    // Relasi: Satu Meja bisa memiliki banyak Booking (Antrian)
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}