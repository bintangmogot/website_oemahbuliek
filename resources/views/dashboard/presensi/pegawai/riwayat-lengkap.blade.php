@extends('layouts.app')
@section('title', 'Riwayat Presensi Lengkap')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 80vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4">
            <div>
                <h3 class="fw-bold mb-0">📜 Riwayat Presensi Lengkap</h3>
                <p class="text-muted mb-0">Lihat semua catatan kehadiran Anda.</p>
            </div>
            <a href="{{ route('pegawai.presensi.index') }}" class="btn theme-secondary bg-primary text-white hover-darker">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>

        {{-- Filter Section --}}
        <div class="card card-body mb-4">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="start_date" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-5">
                    <label for="end_date" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-2">
                <button class="btn btn-theme info py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                </button>
                </div>
            </form>
        </div>

        {{-- Tabel Riwayat Presensi --}}
        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body p-0 table-responsive rounded-3">
                <table class="table table-striped table-hover mb-0 rounded-3">
                    <thead style="background-color:#FFD9D9">
                        <tr>
                            <th>Tanggal</th>
                            <th>Shift</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status Kehadiran</th>
                            <th>Status Lembur</th>
                            <th>Approval</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatPresensi as $presensi)
                            <tr class="bg-white">
                                <td class="align-middle">{{ \Carbon\Carbon::parse($presensi->tgl_presensi)->format('d/m/Y') }}</td>
                                <td class="align-middle">
                                    @if($presensi->jadwalShift && $presensi->jadwalShift->shift)
                                        <div>
                                            <strong>{{ $presensi->jadwalShift->shift->nama_shift }}</strong>
                                            <div class="text-muted small">{{ $presensi->jadwalShift->shift->kode_shift }}</div>
                                            <div class="mt-1">
                                                @if($presensi->jadwalShift->shift->is_shift_lembur == 1)
                                                    <span class="badge bg-info">Shift Lembur</span>
                                                @else
                                                    <span class="badge bg-secondary">Shift Normal</span>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($presensi->jam_masuk)
                                        <strong>{{ \Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}</strong>
                                        @if($presensi->menit_terlambat > 0)
                                            <div class="text-danger small">Terlambat {{ $presensi->menit_terlambat }} menit</div>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($presensi->jam_keluar)
                                        <strong>{{ \Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}</strong>
                                    @else
                                        <span class="text-muted">Belum Check Out</span>
                                    @endif
                                </td>
                                <td class="align-middle"><span class="badge {{ match($presensi->status_kehadiran) { 'present' => 'bg-success', 'late' => 'bg-warning', 'absent' => 'bg-danger', default => 'bg-secondary' } }}">{{ $presensi->status_kehadiran_label }}</span></td>
                                <td class="align-middle"><span class="badge {{ $presensi->status_lembur_badge }}">{{ $presensi->status_lembur_label }}</span></td>
                                <td class="align-middle"><span class="badge {{ match($presensi->status_approval) { 0 => 'bg-warning', 1 => 'bg-success', 2 => 'bg-danger', default => 'bg-secondary' } }}">{{ $presensi->status_approval_label }}</span></td>
                                <td class="align-middle">
                                    <a href="{{ route('presensi.detail', $presensi->id) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>Tidak ada data riwayat presensi yang ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $riwayatPresensi->links() }}
        </div>
    </div>
</div>
@endsection