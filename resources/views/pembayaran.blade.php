<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info Antrian & Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <style>
        body { background-color: #0f111a; }
        .glass-card { background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px; }
        .bg-dark-glass { background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255, 255, 255, 0.1); }
        .qr-frame { background: white; padding: 15px; border-radius: 15px; display: inline-block; }
    </style>
</head>
<body class="payment-page-bg">
    
    <!-- Audio Notifikasi -->
    <audio id="notif-sound" src="https://actions.google.com/sounds/v1/alarms/beep_short.ogg" preload="auto"></audio>

    <div class="container mt-4 mb-4">
        <div class="row justify-content-center">
            <div class="col-md-7">
                
                @if(session('success'))
                    <div class="alert alert-success shadow-sm">{{ session('success') }}</div>
                @endif

                <div class="glass-card">
                    <div class="header-box mb-3">
                        <h4 class="mb-0 text-white">Instruksi Pembayaran (QRIS)</h4>
                    </div>

                    <div class="data-wrapper my-1">
                        <div class="d-flex justify-content-between border-bottom border-secondary py-1">
                            <span class="text-white-50 small">Nama Pelanggan</span>
                            <span class="text-white fw-bold">{{ $booking->nama_pelanggan ?? 'Nama Tidak Ditemukan' }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom border-secondary py-1">
                            <span class="text-white-50 small">Tanggal Main</span>
                            <span class="text-white fw-bold">{{ $booking->tanggal ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom border-secondary py-1">
                            <span class="text-white-50 small">Durasi Sewa</span>
                            <span class="text-white fw-bold">{{ $booking->durasi ?? '0' }} Jam</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom border-secondary py-1">
                            <span class="text-white-50 small">Status</span>
                            <div>
                                <span class="badge {{ $booking->status == 'menunggu_pembayaran' ? 'bg-danger' : 'bg-info' }}">
                                    {{ strtoupper(str_replace('_', ' ', $booking->status)) }}
                                </span>
                                
                                <!-- Timer Bermain (Muncul jika status bermain) -->
                                @if($booking->status == 'bermain' && $booking->jam_mulai)
                                    <span id="timer-bermain" 
                                          class="text-warning fw-bold ms-2" 
                                          data-start="{{ $booking->tanggal }} {{ $booking->jam_mulai }}" 
                                          data-durasi="{{ $booking->durasi }}">
                                        --:--:--
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-2 p-2 rounded bg-dark-glass">
                            <span class="text-light">Total Biaya</span>
                            <span class="h5 mb-0 text-warning">Rp {{ number_format($total_bayar ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @if($booking->status == 'terlambat')
                        <div class="alert alert-danger text-center fw-bold mt-3 mb-0 py-2">⚠️ Antrian Anda hangus.</div>
                    @else
                        <div class="text-center my-3">
                            <p class="text-white-50 mb-2 small">Silakan Scan QRIS Berikut:</p>
                            <div class="qr-frame">
                                <img src="{{ asset('image/qris_markas.png') }}" alt="QRIS" class="img-fluid" style="max-width: 150px;">
                            </div>
                        </div>

                        @if(!in_array($booking->status, ['menunggu_pembayaran', 'menunggu_verifikasi', 'terlambat']))
                            <div class="alert alert-success text-center fw-bold shadow-sm py-2">✅ Pembayaran Berhasil!</div>
                        @endif

                        @if($booking->status == 'menunggu_pembayaran')
                            <hr class="border-secondary my-3">
                            <form action="{{ route('booking.upload', $booking->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-2 text-start">
                                    <label class="form-label text-light small">Unggah Bukti Transfer</label>
                                    <input type="file" name="bukti_pembayaran" class="form-control form-control-sm" required accept="image/*">
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2">Kirim Bukti Pembayaran</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Panggilan -->
    <div class="modal fade" id="modalPanggilan" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-card border-0">
                <div class="modal-body text-center text-white p-4">
                    <h5 class="mb-3">🔔 GILIRAN ANDA!</h5>
                    <p>Silakan segera menuju meja biliar.</p>
                    <div class="alert alert-danger fw-bold fs-4 my-2">
                        Sisa Waktu: <span id="timer-pelanggan">15:00</span>
                    </div>
                    <button type="button" class="btn btn-primary mt-2" onclick="tutupModal()">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bookingId = {{ $booking->id }};
            const modalEl = document.getElementById('modalPanggilan');
            const audio = document.getElementById('notif-sound');
            const display = document.querySelector('#timer-pelanggan');
            let interval = null;
            let timerIsRunning = false;

            // --- FUNGSI TIMER BERMAIN ---
            function mulaiTimerBermain() {
                const el = document.getElementById('timer-bermain');
                if (!el) return;

                const startStr = el.getAttribute('data-start');
                const durasiJam = parseInt(el.getAttribute('data-durasi'));
                const startTime = new Date(startStr.replace(' ', 'T')).getTime();
                const endTime = startTime + (durasiJam * 60 * 60 * 1000);

                const intervalBermain = setInterval(function() {
                    const now = new Date().getTime();
                    const distance = endTime - now;

                    if (distance < 0) {
                        clearInterval(intervalBermain);
                        el.innerHTML = "HABIS";
                        el.classList.replace('text-warning', 'text-danger');
                    } else {
                        const h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((distance % (1000 * 60)) / 1000);
                        el.innerHTML = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
                    }
                }, 1000);
            }

            // Jalankan Timer Bermain
            mulaiTimerBermain();

            // --- FUNGSI MODAL PANGGILAN ---
            function tampilkanModal(waktuPanggil) {
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
                audio.play().catch(e => console.log("Audio diblokir browser"));
                
                if (waktuPanggil && !timerIsRunning) {
                    mulaiTimer(waktuPanggil);
                }
            }

            window.tutupModal = function() {
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                document.body.classList.remove('modal-open');
            }

            function mulaiTimer(waktuPanggilStr) {
                timerIsRunning = true;
                if (interval) clearInterval(interval);
                const start = new Date(waktuPanggilStr.replace(' ', 'T')).getTime();
                const deadline = start + (15 * 60 * 1000);

                interval = setInterval(function () {
                    const sekarang = new Date().getTime();
                    const sisa = deadline - sekarang;

                    if (sisa <= 0) {
                        clearInterval(interval);
                        timerIsRunning = false;
                        display.textContent = "00:00";
                    } else {
                        const m = Math.floor((sisa % (1000 * 60 * 60)) / (1000 * 60));
                        const s = Math.floor((sisa % (1000 * 60)) / 1000);
                        display.textContent = (m < 10 ? "0"+m : m) + ":" + (s < 10 ? "0"+s : s);
                    }
                }, 1000);
            }

            // Polling Status
            setInterval(function() {
                fetch("/booking/check-status/" + bookingId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'menunggu_konfirmasi') {
                            if (modalEl.style.display !== 'block') {
                                tampilkanModal(data.waktu_panggil);
                            }
                        }
                    })
                    .catch(error => console.error('Error polling:', error));
            }, 3000);
        });
    </script>
</body>
</html>