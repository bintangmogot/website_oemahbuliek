{{-- resources/views/dashboard/presensi/pegawai/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Presensi Saya')

@section('content')
<x-session-status/>

<div class="container py-5">
    {{-- Header --}}
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <h3 class="fw-bold mb-0">Presensi Saya</h3>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted">{{ Carbon\Carbon::now()->format('d F Y') }}</span>
            </div>
        </div>

        {{-- Jadwal Shift Section --}}
        <div class="mb-5">
            <h5 class="fw-bold mb-3">Jadwal Shift Mendatang</h5>
            <div class="card rounded-2xl border-0 shadow-sm rounded-3">
                <div class="card-body p-0 table-responsive rounded-3">
                    <table class="table table-striped table-borderless mb-0 rounded-3">
                        <thead style="background-color:#FFD9D9">
                            <tr>
                                <th>Tanggal</th>
                                <th>Shift</th>
                                <th>Jam Kerja</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jadwalShifts as $jadwal)
                                @php
                                    $today = Carbon\Carbon::today();
                                    $jadwalDate = Carbon\Carbon::parse($jadwal->tanggal);
                                    $isToday = $today->isSameDay($jadwalDate);
                                    $isPast = $today->greaterThan($jadwalDate);
                                @endphp
                                <tr class="bg-white">
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            {{ Carbon\Carbon::parse($jadwal->tanggal)->format('d/m/Y') }}
                                            @if($isToday)
                                                <span class="badge bg-primary ms-2">Hari Ini</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div>
                                            <strong>{{ $jadwal->shift->nama_shift }}</strong>
                                            <div class="text-muted small">{{ $jadwal->shift->kode_shift }}</div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        {{ Carbon\Carbon::parse($jadwal->shift->jam_mulai)->format('H:i') }} - 
                                        {{ Carbon\Carbon::parse($jadwal->shift->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td class="align-middle">
                                        @if($isPast)
                                            <span class="badge bg-secondary">Sudah Berlalu</span>
                                        @elseif($isToday)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-info">Mendatang</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($isToday || $isPast)
                                            <a href="{{ route('pegawai.presensi.show', $jadwal->id) }}" 
                                               class="btn btn-theme primary btn-sm">
                                                <i class="fas fa-clock"></i> 
                                                @if($isToday) Presensi @else Lihat @endif
                                            </a>
                                        @else
                                            <span class="text-muted">Belum Waktunya</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-calendar-times fa-2x mb-2"></i>
                                            <p>Belum ada jadwal shift.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Presensi Section --}}
        <div class="mb-4">
            <h5 class="fw-bold mb-3">Riwayat Presensi (7 Hari Terakhir)</h5>
            <div class="card rounded-2xl border-0 shadow-sm rounded-3">
                <div class="card-body p-0 table-responsive rounded-3">
                    <table class="table table-striped table-borderless mb-0 rounded-3">
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
                                    <td class="align-middle">
                                        {{ Carbon\Carbon::parse($presensi->tgl_presensi)->format('d/m/Y') }}
                                    </td>
                                    <td class="align-middle">
                                        @if($presensi->jadwalShift && $presensi->jadwalShift->shift)
                                            <div>
                                                <strong>{{ $presensi->jadwalShift->shift->nama_shift }}</strong>
                                                <div class="text-muted small">{{ $presensi->jadwalShift->shift->kode_shift }}</div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($presensi->jam_masuk)
                                            <div>
                                                <strong>{{ Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i') }}</strong>
                                                @if($presensi->menit_terlambat > 0)
                                                    <div class="text-danger small">
                                                        Terlambat {{ $presensi->menit_terlambat }} menit
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($presensi->jam_keluar)
                                            <strong>{{ Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i') }}</strong>
                                        @else
                                            <span class="text-muted">Belum Check Out</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $statusClass = match($presensi->status_kehadiran) {
                                                0 => 'bg-danger',      // Absent
                                                1 => 'bg-success',     // Present
                                                2 => 'bg-warning',     // Late
                                                3 => 'bg-info',        // Half Day
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $presensi->status_kehadiran_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $lemburClass = match($presensi->status_lembur) {
                                                0 => 'bg-secondary',   // No Overtime
                                                1 => 'bg-warning',     // Overtime Pending
                                                2 => 'bg-success',     // Overtime Approved
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $lemburClass }}">
                                            {{ $presensi->status_lembur_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $approvalClass = match($presensi->status_approval) {
                                                0 => 'bg-warning',     // Pending
                                                1 => 'bg-success',     // Approved
                                                2 => 'bg-danger',      // Rejected
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $approvalClass }}">
                                            {{ $presensi->status_approval_label }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('presensi.detail', $presensi->id) }}" 
                                           class="btn btn-theme info btn-sm">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-history fa-2x mb-2"></i>
                                            <p>Belum ada riwayat presensi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-info-circle text-primary fa-2x mb-2"></i>
                        <h6 class="card-title">Informasi</h6>
                        <p class="card-text small">
                            Pastikan Anda melakukan presensi sesuai dengan jadwal shift yang telah ditentukan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-camera text-success fa-2x mb-2"></i>
                        <h6 class="card-title">Foto Presensi</h6>
                        <p class="card-text small">
                            Foto diperlukan untuk setiap check in dan check out sebagai bukti kehadiran.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body text-center">
                        <i class="fas fa-clock text-warning fa-2x mb-2"></i>
                        <h6 class="card-title">Tepat Waktu</h6>
                        <p class="card-text small">
                            Keterlambatan akan mempengaruhi status approval presensi Anda.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection