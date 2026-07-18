<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Carbon\Carbon;

class BookingController extends Controller
{
    // --- RUTE PELANGGAN ---

    public function index()
    {
        return view('booking_form');
    }

    public function store(Request $request)
    {
        $request->validate(['nama_pelanggan' => 'required|string|max:255', 'durasi' => 'required|integer|min:1']);
        $booking = Booking::create([
            'nama_pelanggan' => $request->nama_pelanggan,
            'tanggal'        => now()->toDateString(),
            'status'         => 'menunggu_pembayaran',
            'durasi'         => $request->durasi,
        ]);
        return redirect()->route('booking.pembayaran', $booking->id);
    }

    public function pembayaran($id)
    {
        $booking = Booking::findOrFail($id);
        $tarif_per_jam = 33000;
        $total_bayar = $booking->durasi * $tarif_per_jam;
        
        $nomor_antrian = Booking::where('tanggal', $booking->tanggal)
                                ->whereIn('status', ['antri', 'menunggu_verifikasi'])
                                ->where('created_at', '<', $booking->created_at)
                                ->count() + 1;

        if ($booking->status == 'dilewati') {
            $nomor_antrian = '-'; 
        }
        
        $estimasi_tunggu = ($nomor_antrian !== '-' && $nomor_antrian > 0) ? ($nomor_antrian - 1) * 10 : 0;
        
        return view('pembayaran', compact('booking', 'total_bayar', 'nomor_antrian', 'estimasi_tunggu'));
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate(['bukti_pembayaran' => 'required|image|max:2048']);
        $booking = Booking::findOrFail($id);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/bukti_pembayaran'), $filename);
        
            $booking->update([
                'bukti_pembayaran' => $filename,
                'status' => 'menunggu_verifikasi',
            ]);
        }
        return redirect()->back()->with('success', 'Bukti berhasil diupload!');
    }

    // --- BAGIAN INI DIPERBAIKI ---
    public function checkStatus($id)
    {
        $booking = Booking::findOrFail($id);
        return response()->json([
            'status' => $booking->status,
            'waktu_panggil' => $booking->waktu_panggil // <-- Ditambahkan agar sinkron dengan Frontend
        ])
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    // --- RUTE ADMIN ---

    public function adminDashboard()
    {
        $bookings = Booking::where('tanggal', now()->toDateString())
            ->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi', 'antri', 'menunggu_konfirmasi', 'bermain', 'dilewati'])
            ->orderBy('created_at', 'asc')
            ->get();
        return view('admin_dashboard', compact('bookings'));
    }

    public function getTablePartial()
    {
        $bookings = Booking::where('tanggal', now()->toDateString())
            ->whereIn('status', ['menunggu_pembayaran', 'menunggu_verifikasi', 'antri', 'menunggu_konfirmasi', 'bermain', 'dilewati'])
            ->orderBy('created_at', 'asc')
            ->get();
        return view('partials.booking_table', compact('bookings'))->render();
    }

    public function panggilPelanggan($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['status' => 'menunggu_konfirmasi', 'waktu_panggil' => now()]);
        return redirect()->back()->with('success', 'Notifikasi dikirim!');
    }

    public function adminMulaiBermain($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['jam_mulai' => now()->format('H:i:s'), 'status' => 'bermain']);
        return redirect()->back()->with('success', 'Sesi permainan dimulai!');
    }

    public function lewatiAntrian($id)
    {
        $booking = Booking::findOrFail($id);
        $nextBooking = Booking::where('tanggal', now()->toDateString())
            ->whereIn('status', ['antri', 'menunggu_verifikasi'])
            ->where('created_at', '>', $booking->created_at)
            ->orderBy('created_at', 'asc')
            ->first();

        if ($nextBooking) {
            $booking->created_at = $nextBooking->created_at->addSecond();
        }
        $booking->status = 'dilewati';
        $booking->save(); 
        return redirect()->back()->with('success', 'Antrian diturunkan satu posisi.');
    }

    public function verifikasiPembayaran($id)
    {
        Booking::findOrFail($id)->update(['status' => 'antri']);
        return redirect()->back()->with('success', 'Pembayaran diverifikasi!');
    }

    public function panggilKembali($id)
    {
        Booking::findOrFail($id)->update(['status' => 'antri']);
        return redirect()->back()->with('success', 'Antrian dipanggil kembali!');
    }

    public function resetAntrian()
    {
        Booking::where('tanggal', now()->toDateString())
            ->whereIn('status', ['antri', 'menunggu_verifikasi', 'menunggu_pembayaran', 'menunggu_konfirmasi', 'bermain', 'dilewati'])
            ->update(['status' => 'selesai']);

        return redirect()->back()->with('success', 'Antrian telah di-reset.');
    }

    public function storeManual(Request $request)
    {
       // Tambahkan validasi
    $request->validate([
        'nama_pelanggan' => 'required',
        'durasi' => 'required|numeric',
        'meja' => 'nullable|string', // Kolom baru
    ]);

    $booking = new Booking();
    $booking->nama_pelanggan = $request->nama_pelanggan;
    $booking->durasi = $request->durasi;
    $booking->meja = $request->meja; // Simpan nomor meja
    $booking->status = 'antri'; 
    $booking->tanggal = now()->toDateString();
    $booking->save();

    return redirect()->back()->with('success', 'Pesanan manual berhasil!');
    }

    public function adminSelesai($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'selesai',
            'jam_selesai' => now()->format('H:i:s'),
        ]);
        return redirect()->back()->with('success', 'Sesi selesai!');
    }

    public function tambahDurasi($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->increment('durasi');
        return redirect()->back()->with('success', 'Durasi berhasil ditambah 1 jam!');
    }

    public function tolakPembayaran($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update([
            'status' => 'menunggu_pembayaran',
            'bukti_pembayaran' => null
        ]);
        return back()->with('success', 'Pembayaran telah ditolak.');
    }

    public function mulaiProses(Request $request)
{
    $request->validate([
        'booking_id' => 'required',
        'meja' => 'required|string',
    ]);

    $booking = Booking::findOrFail($request->booking_id);
    
    // Update data
    $booking->meja = $request->meja;
    $booking->status = 'bermain';
    $booking->jam_mulai = now()->format('H:i:s'); // Pastikan jam mulai terisi
    $booking->save();

    return redirect()->back()->with('success', 'Permainan dimulai di ' . $request->meja);
}
}