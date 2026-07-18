<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan Biliar - Markas Biliar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                
                {{-- Penanganan Error --}}
                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="glass-card">
                    <div class="header-box d-flex align-items-center justify-content-center gap-3">
                        <img src="{{ asset('image/markas-logo.jpg') }}" alt="Logo" class="brand-logo">
                        <h4 class="mb-0">Form Pemesanan</h4>
                    </div>

                    {{-- INFO BOX MINIMALIS --}}
                    <div class="info-box">
                        <strong>Sistem Antrian Otomatis (FIFO)</strong>
                        Anda akan otomatis mendapatkan slot waktu bermain tercepat setelah melakukan pembayaran.
                    </div>

                    <form action="{{ route('booking.store') }}" method="POST" onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').innerText='Memproses...';">
                        @csrf 

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="nama_pelanggan" class="form-control @error('nama_pelanggan') is-invalid @enderror" value="{{ old('nama_pelanggan') }}" required placeholder="Masukkan nama Anda">
                            @error('nama_pelanggan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Durasi Main (Jam)</label>
                            <input type="number" name="durasi" class="form-control @error('durasi') is-invalid @enderror" value="{{ old('durasi') }}" min="1" required placeholder="Contoh: 2">
                            @error('durasi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-success w-100 fw-bold py-2 shadow-sm btn-billiard">Submit Pesanan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>