<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Markas Biliar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #0f172a; color: #f8fafc; font-family: sans-serif; }
        .admin-card { 
            background-color: #1e293b; 
            border: 1px solid #334155; 
            border-radius: 8px; 
            padding: 24px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .table-admin { color: #fff; width: 100%; border-collapse: collapse; }
        .table-admin thead { background-color: #334155; }
        .table-admin th, .table-admin td { padding: 12px 16px; border-bottom: 1px solid #334155; }
        .table-admin tbody tr:hover { background-color: #2d3748; }
        
        .btn-glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #fff;
            transition: all 0.3s ease;
        }
        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: rgba(255, 255, 255, 0.3);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .btn-outline-danger.btn-glass:hover { background: rgba(220, 53, 69, 0.2); border-color: #dc3545; }
        .btn-outline-warning.btn-glass:hover { background: rgba(255, 193, 7, 0.2); border-color: #ffc107; }
        .btn-outline-success.btn-glass:hover { background: rgba(25, 135, 84, 0.2); border-color: #198754; }
        .btn-outline-light.btn-glass:hover { background: rgba(248, 249, 250, 0.2); border-color: #f8f9fa; }
        .btn-outline-info.btn-glass:hover { background: rgba(13, 202, 240, 0.2); border-color: #0dcaf0; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold d-flex align-items-center">
                <img src="{{ asset('image/markas-logo.jpg') }}" alt="Logo Markas" style="height: 45px; width: auto; margin-right: 15px; border-radius: 5px;">
                Dashboard Antrian
            </h2>
            
            <div class="d-flex gap-2">
                @if(session()->has('admin_logged_in'))
                    <button type="button" class="btn btn-outline-info btn-sm rounded-pill px-3 py-2 btn-glass" data-bs-toggle="modal" data-bs-target="#modalGantiSandi">
                        <i class="bi bi-shield-lock me-1"></i> Ganti Sandi
                    </button>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 btn-glass">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 py-2 btn-glass">
                        <i class="bi bi-key me-1"></i> Login
                    </a>
                @endif
                
                <form action="{{ route('admin.reset_antrian') }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mereset semua antrian hari ini?')">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning btn-sm rounded-pill px-3 py-2 btn-glass">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </form>
                
                <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 py-2 btn-glass" data-bs-toggle="modal" data-bs-target="#modalPesananManual">
                    <i class="bi bi-plus-circle me-1"></i> Tambah Manual
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <div class="admin-card mt-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Daftar Antrian Aktif</h4>
            </div>

            <div class="table-responsive">
                <table class="table-admin align-middle">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nomor</th>
                            <th>Meja</th>
                            <th>Status</th>
                            <th>Durasi</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-booking-body">
                        @include('partials.booking_table')
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal Ganti Sandi --}}
    <div class="modal fade" id="modalGantiSandi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <form action="{{ route('admin.update_password') }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary"><h5 class="modal-title">Ganti Password Admin</h5></div>
                    <div class="modal-body">
                        <label class="mb-1">Password Lama</label>
                        <input type="password" name="old_password" class="form-control mb-3 bg-dark text-white border-secondary" required>
                        <label class="mb-1">Password Baru</label>
                        <input type="password" name="new_password" class="form-control mb-3 bg-dark text-white border-secondary" required>
                        <label class="mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Pesanan Manual --}}
    <div class="modal fade" id="modalPesananManual" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <form action="{{ route('admin.booking.manual') }}" method="POST">
                    @csrf
                    <div class="modal-header border-secondary"><h5 class="modal-title">Tambah Pesanan Manual</h5></div>
                    <div class="modal-body">
                        <label class="mb-1">Nama Pelanggan</label>
                        <input type="text" name="nama_pelanggan" class="form-control mb-3 bg-dark text-white border-secondary" required>
                        <label class="mb-1">Durasi (Jam)</label>
                        <input type="number" name="durasi" class="form-control mb-3 bg-dark text-white border-secondary" required>
                        <label class="mb-1">Nomor Meja</label>
                        <input type="text" name="meja" class="form-control bg-dark text-white border-secondary" placeholder="Contoh: Meja 01">
                    </div>
                    <div class="modal-footer border-secondary"><button type="submit" class="btn btn-success">Simpan Data</button></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Mulai Permainan (NEW) --}}
    <div class="modal fade" id="modalMulai" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <form action="{{ route('admin.mulai_proses') }}" method="POST">
                    @csrf
                    <input type="hidden" name="booking_id" id="booking_id_input">
                    <div class="modal-header border-secondary"><h5 class="modal-title">Konfirmasi Meja</h5></div>
                    <div class="modal-body">
                        <label class="mb-1">Masukkan Nomor Meja Pelanggan</label>
                        <input type="text" name="meja" class="form-control bg-dark text-white border-secondary" placeholder="Contoh: Meja 05" required>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Mulai Permainan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi pemicu modal
        function bukaModalMulai(id) {
            document.getElementById('booking_id_input').value = id;
            new bootstrap.Modal(document.getElementById('modalMulai')).show();
        }

        setInterval(function() {
            fetch("{{ route('admin.table.refresh') }}")
                .then(response => response.text())
                .then(html => { document.getElementById('table-booking-body').innerHTML = html; })
                .catch(error => console.error('Error:', error));
        }, 5000);

        setInterval(function() {
            document.querySelectorAll('.timer-admin').forEach(el => {
                const waktuPanggilStr = el.getAttribute('data-waktu');
                if(!waktuPanggilStr) return;
                const waktuPanggil = new Date(waktuPanggilStr.replace(' ', 'T')).getTime();
                const batasWaktu = waktuPanggil + (15 * 60 * 1000); 
                const selisih = batasWaktu - new Date().getTime();
                if (selisih <= 0) { el.innerText = "EXPIRED"; el.classList.add('text-danger'); } 
                else { const menit = Math.floor(selisih / (1000 * 60)); const detik = Math.floor((selisih % (1000 * 60)) / 1000); el.innerText = `${menit}:${detik.toString().padStart(2, '0')}`; el.classList.remove('text-danger'); }
            });
            document.querySelectorAll('.timer-bermain').forEach(el => {
                const startStr = el.getAttribute('data-start');
                const durasiJam = parseInt(el.getAttribute('data-durasi'));
                if (!startStr) return;
                const waktuMulai = new Date(startStr.replace(' ', 'T'));
                const waktuSelesai = new Date(waktuMulai.getTime() + (durasiJam * 60 * 60 * 1000));
                const selisih = waktuSelesai - new Date();
                if (selisih <= 0) { el.innerText = "HABIS"; el.classList.add('text-danger'); } 
                else { const h = Math.floor(selisih / (1000 * 60 * 60)); const m = Math.floor((selisih % (1000 * 60 * 60)) / (1000 * 60)); const s = Math.floor((selisih % (1000 * 60)) / 1000); el.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`; el.classList.remove('text-danger'); }
            });
        }, 1000);
    </script>
</body>
</html>