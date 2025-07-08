@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="container p-4 my-5 bg-white rounded-4">
<x-session-status/>
    {{-- JUDUL --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Selamat Datang, {{ auth()->user()->nama_lengkap ?? 'Pengguna' }}!</h3>
        <p class="text-muted">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- ============================================= --}}
    {{-- TAMPILAN KHUSUS UNTUK ADMIN --}}
    {{-- ============================================= --}}
    @if(auth()->user()->role === 'admin')
        {{-- Kartu Ringkasan Atas --}}
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary text-white p-3 rounded-3 me-3"><i class="fas fa-boxes fa-2x"></i></div>
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Bahan Baku</h6>
                            <h4 class="card-title fw-bold">{{ $jumlahBahanBaku }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success text-white p-3 rounded-3 me-3"><i class="fas fa-users fa-2x"></i></div>
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Pegawai Aktif</h6>
                            <h4 class="card-title fw-bold">{{ $jumlahPegawai }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100 bg-warning text-dark">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-white text-warning p-3 rounded-3 me-3"><i class="fas fa-user-clock fa-2x"></i></div>
                        <div>
                            <h6 class="card-subtitle mb-2">Presensi Pending</h6>
                            <h4 class="card-title fw-bold">{{ $pendingApprovalCount }}</h4>
                            <a href="{{ route('admin.presensi.index', ['status_approval' => 0]) }}" class="stretched-link text-dark">Lihat Detail</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-danger text-white p-3 rounded-3 me-3"><i class="fas fa-exclamation-triangle fa-2x"></i></div>
                        <div>
                            <h6 class="card-subtitle mb-2 text-muted">Kerugian Bulan Ini</h6>
                            <h4 class="card-title fw-bold">Rp {{ number_format($totalKerugianBulanIni, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ringkasan Inventaris dan SDM --}}
        <div class="row g-4">
            {{-- Kolom Kiri: Peringatan & Aktivitas Inventaris --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Peringatan Stok Kritis</div>
                    <div class="card-body">
                        @if($stokKritis->isEmpty() && $stokHabis->isEmpty())
                            <p class="text-muted text-center ">👍 Semua stok dalam kondisi aman.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($stokKritis as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('bahan-baku.show', $item->id) }}" class="stretched-link text-dark">{{ $item->nama }}</a>
                                        <span class="badge bg-warning rounded-pill">Sisa {{ $item->stok_terkini }} {{ $item->satuan_label }}</span>
                                    </li>
                                @endforeach
                                 @foreach($stokHabis as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('bahan-baku.show', $item->id) }}" class="stretched-link text-dark">{{ $item->nama }}</a>
                                        <span class="badge bg-danger rounded-pill">Stok Habis</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                <div class="card shadow-sm border-0">
                    <div class="card-header fw-bold"><i class="fas fa-history me-2"></i>Aktivitas Inventaris Hari Ini</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4"><h5 class="fw-bold text-success">{{ $aktivitasHariIni->get('masuk', 0) }}</h5><p class="text-muted">Masuk</p></div>
                            <div class="col-4"><h5 class="fw-bold text-primary">{{ $aktivitasHariIni->get('produksi', 0) }}</h5><p class="text-muted">Produksi</p></div>
                            <div class="col-4"><h5 class="fw-bold text-danger">{{ $aktivitasHariIni->get('rusak', 0) }}</h5><p class="text-muted">Rusak</p></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan SDM & Penggajian --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header fw-bold"><i class="fas fa-calendar-day me-2"></i>Ringkasan SDM Hari Ini</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4 border-end"><h4 class="fw-bold text-primary">{{ $pegawaiDijadwalkan }}</h4><p class="text-muted mb-0">Dijadwalkan</p></div>
                            <div class="col-4 border-end"><h4 class="fw-bold text-success">{{ $hadirHariIni }}</h4><p class="text-muted mb-0">Hadir</p></div>
                            <div class="col-4"><h4 class="fw-bold text-danger">{{ $terlambatHariIni }}</h4><p class="text-muted mb-0">Terlambat</p></div>
                        </div>
                    </div>
                </div>
                 <div class="card shadow-sm border-0">
                    <div class="card-header fw-bold"><i class="fas fa-money-check-alt me-2"></i>Ringkasan Gaji Bulan Ini</div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Gaji Pokok Belum Dibayar <span class="badge bg-danger rounded-pill">Rp {{ number_format($totalGajiPokokBelumDibayar, 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Gaji Lembur Belum Dibayar <span class="badge bg-danger rounded-pill">Rp {{ number_format($totalLemburBelumDibayar, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{route('admin.gaji-pokok.index') }}" class="text-dark">Lihat Laporan Gaji Pokok</a> | 
                        <a href="{{ route('gaji-lembur.index', ['status_pembayaran' => 0]) }}" class="text-dark">Lihat Laporan Gaji Lembur</a>
                    </div>
                </div>
            </div>
        </div>

    {{-- ============================================= --}}
    {{-- TAMPILAN KHUSUS UNTUK PEGAWAI --}}
    {{-- ============================================= --}}
    @else
    <div class="row g-4">
        {{-- Kolom Kiri: Jadwal Aktif --}}
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header fw-bold"><i class="fas fa-calendar-check me-2"></i>Jadwal Aktif Hari Ini</div>
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    @if($jadwalAktifHariIni)
                        <h4 class="fw-bold">{{ $jadwalAktifHariIni->shift->nama_shift }}</h4>
                        <p class="text-muted fs-5">{{ \Carbon\Carbon::parse($jadwalAktifHariIni->shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwalAktifHariIni->shift->jam_selesai)->format('H:i') }}</p>
                        @if($jadwalAktifHariIni->shift->is_shift_lembur)<p><span class="badge bg-info fs-6">Shift Lembur</span></p>@endif
                        <a href="{{ route('pegawai.presensi.show', $jadwalAktifHariIni->id) }}" class="btn btn-theme info mt-3"><i class="fas fa-fingerprint"></i> Lakukan Presensi</a>
                    @else
                        <p class="text-muted fs-5">Tidak ada jadwal kerja aktif untuk hari ini.</p><p>Selamat beristirahat! 😊</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Jadwal Berikutnya & Gaji --}}
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header fw-bold"><i class="fas fa-calendar-alt me-2"></i>Jadwal Berikutnya</div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        @forelse($semuaJadwalBerikutnya as $jadwal)
                            <li class="list-group-item">
                                <p class="mb-1"><strong>{{ $jadwal->tanggal->translatedFormat('l, d F Y') }}</strong></p>
                                <p class="text-muted mb-0">{{ $jadwal->shift->nama_shift }} ({{ \Carbon\Carbon::parse($jadwal->shift->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->shift->jam_selesai)->format('H:i') }})</p>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Belum ada jadwal berikutnya yang diatur.</li>
                        @endforelse
                    </ul>
                </div>
                 <div class="card-footer">
                     <a href="{{ route('pegawai.presensi.index') }}" class="text-dark">Lihat Semua Jadwal</a>
                 </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-header fw-bold"><i class="fas fa-wallet me-2"></i>Ringkasan Gaji Anda</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Gaji Pokok Belum Dibayar
                        <span class="badge bg-danger rounded-pill">Rp {{ number_format($gajiPokokBelumDibayar, 0, ',', '.') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        Gaji Lembur Belum Dibayar
                        <span class="badge bg-danger rounded-pill">Rp {{ number_format($gajiLemburBelumDibayar, 0, ',', '.') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

        <div style="height: 20px;"></div>

        {{-- Ringkasan Inventaris --}}
        <div class="row g-4">
            {{-- Kolom Kiri: Peringatan & Aktivitas Inventaris --}}
            <div class="col-lg-6">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark fw-bold"><i class="fas fa-exclamation-circle me-2"></i>Peringatan Stok Kritis</div>
                    <div class="card-body">
                        @if($stokKritis->isEmpty() && $stokHabis->isEmpty())
                            <p class="text-muted text-center ">👍 Semua stok dalam kondisi aman.</p>
                        @else
                            <ul class="list-group list-group-flush">
                                @foreach($stokKritis as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('bahan-baku.show', $item->id) }}" class="stretched-link text-dark">{{ $item->nama }}</a>
                                        <span class="badge bg-warning rounded-pill">Sisa {{ $item->stok_terkini }} {{ $item->satuan_label }}</span>
                                    </li>
                                @endforeach
                                 @foreach($stokHabis as $item)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <a href="{{ route('bahan-baku.show', $item->id) }}" class="stretched-link text-dark">{{ $item->nama }}</a>
                                        <span class="badge bg-danger rounded-pill">Stok Habis</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm border-0">
                    <div class="card-header fw-bold"><i class="fas fa-history me-2"></i>Aktivitas Inventaris Hari Ini</div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4"><h5 class="fw-bold text-success">{{ $aktivitasHariIni->get('masuk', 0) }}</h5><p class="text-muted">Masuk</p></div>
                            <div class="col-4"><h5 class="fw-bold text-primary">{{ $aktivitasHariIni->get('produksi', 0) }}</h5><p class="text-muted">Produksi</p></div>
                            <div class="col-4"><h5 class="fw-bold text-danger">{{ $aktivitasHariIni->get('rusak', 0) }}</h5><p class="text-muted">Rusak</p></div>
                        </div>
                    </div>
                </div>
            </div>
    @endif
</div>
@endsection