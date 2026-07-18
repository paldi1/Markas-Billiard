<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Monitor Antrian & Timer Meja</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                
                <h3 class="mb-4">🎱 Live Status Meja Biliar</h3>

                @if($booking->status == 'antri')
                <div class="card shadow border-0 py-4 mb-3">
                    <div class="card-body">
                        <div class="spinner-border text-primary mb-3" role="status"></div>
                        <h4 class="fw-bold text-primary">Status: Sedang Mengantri</h4>
                        <p class="text-muted">Mohon tunggu sebentar, meja sedang disiapkan oleh admin.</p>
                        <hr>
                        <h5>Jumlah Antrian di Depan Anda: <span class="badge bg-danger">{{ $antrian_di_depan }} Orang</span></h5>
                    </div>
                </div>

                @elseif($booking->status == 'bermain')
                <div class="card bg-dark text-white border-0 shadow mb-3">
                    <div class="card-body text-center p-5">
                        <span class="badge bg-success mb-3 fw-bold p-2">MEJA AKTIF</span>
                        <h1 class="display-1 fw-bold text-warning" id="countdown-timer" data-waktu-selesai="{{ $waktu_selesai }}">
                            00:00:00
                        </h1>      
                        
                        <p class="mt-3 fs-5 text-muted">Selamat Bermain, <strong>{{ $booking->nama_pelanggan }}</strong>!</p>
                        
                        <div id="alert-tambahan" class="alert alert-warning d-none mt-3" role="alert">
                            ⚠️ <strong>Sisa waktu &lt; 35 Menit!</strong> <br> Silakan hubungi admin jika ingin menambah durasi bermain.
                        </div>

                        <div class="mt-4">
                            <a href="/" class="btn btn-outline-light btn-sm px-4 rounded-pill">Kembali ke Form Utama</a>
                        </div>
                    </div>
                </div>

                @else
                <div class="card bg-dark text-white border-0 shadow mb-3">
                    <div class="card-body text-center p-5">
                        <span class="badge bg-danger mb-3 fw-bold p-2">SESI BERAKHIR</span>
                        <h1 class="display-1 fw-bold text-danger">00:00:00</h1>
                        <h3 class="mt-3">Waktu bermain Anda telah habis.</h3>
                        <p class="text-muted">Terima kasih telah bermain di Markas Biliar! Silakan kembalikan stik ke kasir.</p>
                        
                        <div class="mt-4">
                            <a href="/" class="btn btn-outline-light btn-sm px-4 rounded-pill">Kembali ke Form Utama</a>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
    
    @if($booking->status == 'bermain')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const timerElement = document.getElementById('countdown-timer');
            const alertTambahan = document.getElementById('alert-tambahan');
            
            if (!timerElement) return;

            // 1. Ambil data string waktu selesai dari attribute HTML
            const targetWaktuStr = timerElement.getAttribute('data-waktu-selesai');
            if (!targetWaktuStr) {
                console.error("Waktu selesai tidak terbaca dari database!");
                return;
            }

            // 2. Ubah format waktu menjadi timestamp JavaScript
            const countDownDate = new Date(targetWaktuStr).getTime();

            // 3. Jalankan hitung mundur setiap 1 detik
            const x = setInterval(function() {
                const now = new Date().getTime();
                const distance = countDownDate - now;

                // Perhitungan Jam, Menit, dan Detik
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Format angka agar selalu dua digit (misal: 02:05:09)
                const formatZero = (num) => String(num).padStart(2, '0');

                if (distance > 0) {
                    // Tampilkan hasil ke layar HTML
                    timerElement.innerHTML = `${formatZero(hours)}:${formatZero(minutes)}:${formatZero(seconds)}`;

                    // Pemicu Alert Kuning jika sisa waktu kurang dari 35 menit (35 * 60 * 1000 ms)
                    if (alertTambahan && distance < (35 * 60 * 1000)) {
                        alertTambahan.classList.remove('d-none');
                    }
                } else {
                    // Jika waktu sudah habis total
                    clearInterval(x);
                    timerElement.innerHTML = "00:00:00";
                
                    // Refresh halaman otomatis HANYA SEKALI saat waktu habis, agar kartu berubah ke layar "SESI BERAKHIR"
                    window.location.reload();
                }
            }, 1000);
        });
    </script>
    @endif
</body>
</html>