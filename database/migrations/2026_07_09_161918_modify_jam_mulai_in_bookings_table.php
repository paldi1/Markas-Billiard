<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Mengubah kolom jam_mulai menjadi nullable
            $table->time('jam_mulai')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Mengembalikan ke not null jika diperlukan
            $table->time('jam_mulai')->nullable(false)->change();
        });
    }
};
