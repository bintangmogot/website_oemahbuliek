@extends('layouts.app')

@section('content')
<div class="container py-5">
    {{-- Header: Title, Export, Create --}}
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        @if(auth()->user()->role === 'admin')
                            Manajemen Jadwal Shift - Semua Pegawai
                        @else
                            Jadwal Shift Saya
                        @endif
                    </h3>
                    <div class="d-flex gap-3">
                    <!-- Filter Button -->
                    <button class="btn btn-theme primary me-2 py-2 px-3">
                        <i class="bi bi-funnel" style="font-size: 1.2rem" ></i>
                    </button>
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('jadwal-shift.create') }}" class="btn btn-yellow">
                            <i class="fas fa-plus"></i> Tambah Jadwal
                        </a>
                    @endif
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr style="background-color:#FFD9D9">
                                    <th>No</th>
                                    @if(auth()->user()->role === 'admin')
                                        <th>Nama Pegawai</th>
                                    @endif
                                    <th>Shift</th>
                                    <th>Tanggal</th>
                                    <th>Jam Kerja</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($jadwalShifts as $index => $jadwal)
                                @php
                                    // Pengecekan apakah hari ini adalah hari-H jadwal atau tidak
                                    $today = \Carbon\Carbon::today();
                                    $jadwalDate = \Carbon\Carbon::parse($jadwal->tanggal);
                                    $isJadwalHariIni = $today->isSameDay($jadwalDate);
                                    $isJadwalLewat = $today->greaterThan($jadwalDate);
                                @endphp
                                <tr>
                                    <td>{{ $jadwalShifts->firstItem() + $index }}</td>
                                    @if(auth()->user()->role === 'admin')
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($jadwal->user->foto_profil)
                                                    <img src="{{ asset('storage/'.$jadwal->user->foto_profil) }}" 
                                                         class="rounded-circle me-2" 
                                                         width="30" height="30" 
                                                         style="object-fit:cover">
                                                @else
                                                    <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                         style="width:30px; height:30px; font-size:12px; color:white;">
                                                        {{ substr($jadwal->user->nama_lengkap, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="fw-bold">{{ $jadwal->user->nama_lengkap }}</div>
                                                    <small class="text-muted">{{ $jadwal->user->jabatan }}</small>
                                                </div>
                                            </div>
                                        </td>
                                    @endif
                                    <td>
                                        <span class="badge bg-info">{{ $jadwal->shift->nama_shift }}</span>
                                    </td>
                                    <td>
                                        {{ $jadwal->tanggal->format('d/m/Y') }}
                                        @if(!$isJadwalHariIni && !$isJadwalLewat && auth()->user()->role === 'pegawai')
                                            <br><small class="text-warning">
                                                <i class="fas fa-clock"></i> Belum waktunya
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- PERBAIKAN: Menampilkan jam kerja dari shift yang tepat --}}
                                        <div class="text-muted">
                                            <i class="fas fa-clock me-2"></i>
                                            {{ $jadwal->shift->jam_mulai ? \Carbon\Carbon::parse($jadwal->shift->jam_mulai)->format('H:i') : '—' }}
                                            –
                                            {{ $jadwal->shift->jam_selesai ? \Carbon\Carbon::parse($jadwal->shift->jam_selesai)->format('H:i') : '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        @switch($jadwal->status)
                                            @case(0)
                                                <span class="badge bg-danger">Dibatalkan</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-success">Aktif</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-secondary">Selesai</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if(auth()->user()->role === 'pegawai')
                                                {{-- Tombol untuk pegawai ke halaman presensi --}}
                                                @if($isJadwalHariIni && $jadwal->status == 1)
                                                    {{-- Tombol aktif jika hari ini adalah hari-H dan jadwal aktif --}}
                                                    <a href="{{ route('presensi.create', ['jadwal_id' => $jadwal->id]) }}" 
                                                       class="btn btn-outline-primary" 
                                                       title="Presensi">
                                                        <i class="fas fa-clock"></i> Presensi
                                                    </a>
                                                @elseif($isJadwalLewat)
                                                    {{-- Tombol disabled jika jadwal sudah lewat --}}
                                                    <button class="btn btn-outline-secondary" 
                                                            disabled 
                                                            title="Jadwal sudah lewat">
                                                        <i class="fas fa-clock"></i> Sudah Lewat
                                                    </button>
                                                @elseif($jadwal->status != 1)
                                                    {{-- Tombol disabled jika jadwal tidak aktif --}}
                                                    <button class="btn btn-outline-secondary" 
                                                            disabled 
                                                            title="Jadwal tidak aktif">
                                                        <i class="fas fa-ban"></i> Tidak Aktif
                                                    </button>
                                                @else
                                                    {{-- Tombol disabled jika belum hari-H --}}
                                                    <button class="btn btn-outline-secondary" 
                                                            disabled 
                                                            title="Belum waktunya presensi">
                                                        <i class="fas fa-clock"></i> Belum Waktunya
                                                    </button>
                                                @endif
                                            @else
                                                {{-- Tombol untuk admin --}}
                                                <a href="{{ route('jadwal-shift.show', $jadwal->id) }}" 
                                                   class="btn btn-outline-info btn-sm" 
                                                   title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('jadwal-shift.edit', $jadwal->id) }}" 
                                                   class="btn btn-outline-warning btn-sm" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('jadwal-shift.destroy', $jadwal->id) }}" 
                                                      method="POST" 
                                                      class="d-inline" 
                                                      onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn btn-outline-danger btn-sm" 
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'admin' ? '7' : '6' }}" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">
                                                @if(auth()->user()->role === 'admin')
                                                    Belum ada jadwal shift yang dibuat
                                                @else
                                                    Anda belum memiliki jadwal shift
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($jadwalShifts->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $jadwalShifts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection