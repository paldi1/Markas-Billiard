@forelse($bookings as $b)
<tr class="align-middle">
    <!-- 1. Nomor -->
    <td class="text-center">{{ $loop->iteration }}</td>

    <!-- 2. Nama -->
    <td><strong>{{ $b->nama_pelanggan }}</strong></td>

    <!-- 3. Meja -->
    <td class="text-center">
        @if(!empty($b->meja))
            <span class="badge bg-secondary" style="font-size: 0.75rem;">
                {{ $b->meja }}
            </span>
        @else
            -
        @endif
    </td>

    <!-- 4. Status & Timer -->
    <td>
        @if($b->status == 'menunggu_konfirmasi')
            <span class="badge bg-primary mb-1">Konfirmasi (15mnt)</span>
            <div class="timer-admin fw-bold text-danger small" 
                 data-waktu="{{ $b->waktu_panggil ? \Carbon\Carbon::parse($b->waktu_panggil)->toIso8601String() : '' }}">--:--</div>
        @elseif($b->status == 'menunggu_verifikasi')
            <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
        @elseif($b->status == 'dilewati')
            <span class="badge bg-secondary mb-1">DILEWATI</span>
            <form action="{{ route('admin.panggil_kembali', $b->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-xs btn-info text-white" style="font-size: 10px;">↺ Panggil</button>
            </form>
        @elseif($b->status == 'bermain')
            <span class="badge bg-success">Bermain</span>
        @else
            <span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $b->status)) }}</span>
        @endif
    </td>

    <!-- 5. Durasi -->
    <td>
        <span class="badge bg-light text-dark border">{{ $b->durasi }} Jam</span>
        @if($b->status == 'bermain' && $b->jam_mulai)
            <div class="timer-bermain text-success fw-bold small mt-1" 
                 data-start="{{ \Carbon\Carbon::parse($b->tanggal . ' ' . $b->jam_mulai)->toIso8601String() }}" 
                 data-durasi="{{ $b->durasi }}">--:--</div>
        @endif
        <form action="{{ route('admin.booking.tambah_durasi', $b->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-outline-warning py-0 px-2" title="Tambah 1 Jam">+</button>
        </form>
    </td>

    <!-- 6. Bukti Pembayaran -->
    <td>
        @if($b->bukti_pembayaran)
            <a href="{{ asset('uploads/bukti_pembayaran/' . $b->bukti_pembayaran) }}" target="_blank">
                <img src="{{ asset('uploads/bukti_pembayaran/' . $b->bukti_pembayaran) }}" 
                     alt="Bukti" 
                     class="img-thumbnail" 
                     style="width: 60px; height: 60px; object-fit: cover;">
            </a>
            @if($b->status == 'menunggu_verifikasi')
                <div class="mt-1 d-flex gap-1">
                    <form action="{{ route('admin.verifikasi', $b->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm py-0 px-2">✅</button>
                    </form>
                    <form action="{{ route('admin.tolak', $b->id) }}" method="POST" onsubmit="return confirm('Yakin?')">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm py-0 px-2">❌</button>
                    </form>
                </div>
            @endif
        @else
            <span class="text-muted small">N/A</span>
        @endif
    </td>

    <!-- 7. Aksi -->
    <td>
        <div class="d-grid gap-1">
            @if($b->status == 'antri')
                <form action="{{ route('admin.panggil', $b->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm w-100">🔔 Panggil</button>
                </form>
            @elseif($b->status == 'menunggu_konfirmasi')
                <!-- Tombol Mulai sekarang memicu Modal -->
                <button type="button" class="btn btn-success btn-sm w-100" onclick="bukaModalMulai('{{ $b->id }}')">
                    ✅ Mulai
                </button>
                <form action="{{ route('admin.lewati', $b->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Lewati?')">❌ Lewati</button>
                </form>
            @elseif($b->status == 'bermain')
                <form action="{{ route('admin.selesai', $b->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-warning btn-sm w-100">■ Selesai</button>
                </form>
            @endif
        </div>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center py-4">Antrian kosong.</td></tr>
@endforelse