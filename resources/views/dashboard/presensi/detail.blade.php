@extends('layouts.app')

@section('title', 'Detail Presensi')

@section('content')
<div class="container p-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0 fw-bold">Detail Presensi</h1>
                    <p class="mb-0 text-black fw-bold">{{ $presensi->user->nama_lengkap }} - {{ $presensi->tgl_presensi->format('d F Y') }}</p>
                </div>
                <div>
                    <a href="{{ Auth::user()->role === 'admin' ? route('admin.presensi.index') : route('pegawai.presensi.index') }}" 
                       class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Umum -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h3 class="m-0 font-weight-bold text-primary">Informasi Presensi</h3>
                    <div class="dropdown no-arrow">
                        <span class="badge bg-{{ $presensi->status_approval_color }} badge-lg">
                            {{ $presensi->status_approval_label }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Data Pegawai -->
                        <div class="col-md-6 mb-3">
                            <h3 class="text-primary mb-3">Data Pegawai</h3>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Nama</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->user->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->tgl_presensi->format('d F Y') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Data Shift -->
                        <div class="col-md-6 mb-3">
                            <h3 class="text-primary mb-3">Data Shift</h3>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Nama Shift</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->jadwalShift->shift->nama_shift ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jam Kerja</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($presensi->jadwalShift && $presensi->jadwalShift->shift)
                                            {{ Carbon\Carbon::parse($presensi->jadwalShift->shift->jam_mulai)->format('H:i') }} - 
                                            {{ Carbon\Carbon::parse($presensi->jadwalShift->shift->jam_selesai)->format('H:i') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Toleransi</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->jadwalShift->shift->toleransi_terlambat ?? 0 }} menit</td>
                                </tr>
                                <tr>
                                    <td><strong>Min. Lembur</strong></td>
                                    <td>:</td>
                                    <td>{{ $presensi->jadwalShift->shift->batas_lembur_min ?? 0 }} menit</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <!-- Check In -->
                        <div class="col-md-6 mb-3">
                            <h3 class="text-success mb-3">
                                <i class="fas fa-sign-in-alt"></i> Check In
                            </h3>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Waktu Masuk</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($presensi->jam_masuk)
                                            {{ Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}
                                            @if($presensi->menit_terlambat > 0)
                                                <span class="badge badge bg-info ml-2">
                                                    Terlambat {{ $presensi->menit_terlambat }} menit
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum check in</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Kehadiran</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $presensi->status_kehadiran == 1 ? 'success' : 
                                            ($presensi->status_kehadiran == 2 ? 'warning' : 
                                            ($presensi->status_kehadiran == 3 ? 'info' : 'secondary'))
                                        }}">
                                            {{ $presensi->status_kehadiran_label }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Check Out -->
                        <div class="col-md-6 mb-3">
                            <h3 class="text-danger mb-3">
                                <i class="fas fa-sign-out-alt"></i> Check Out
                            </h3>
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td width="40%"><strong>Waktu Keluar</strong></td>
                                    <td>:</td>
                                    <td>
                                        @if($presensi->jam_keluar)
                                            {{ Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') }}
                                            @php
                                                $overtime = $presensi->calculateOvertime();
                                            @endphp
                                            @if($overtime > 0)
                                                <span class="badge bg-info ml-2">
                                                    Lembur {{ $overtime }} menit
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum check out</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status Lembur</strong></td>
                                    <td>:</td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $presensi->status_lembur == 2 ? 'success' : 
                                            ($presensi->status_lembur == 1 ? 'warning' : 'secondary')
                                        }}">
                                            {{ $presensi->status_lembur_label }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    <!-- Durasi Kerja -->
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-clock"></i> Durasi Kerja
                            </h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h4 class="text-primary mb-1">
                                            @php
                                                $durasi = $presensi->calculateWorkDuration();
                                                $jam = floor($durasi / 60);
                                                $menit = $durasi % 60;
                                            @endphp
                                            {{ $jam }}j {{ $menit }}m
                                        </h4>
                                        <small class="text-muted">Total Kerja</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h4 class="text-warning mb-1">{{ $presensi->menit_terlambat }}m</h4>
                                        <small class="text-muted">Keterlambatan</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3 bg-light rounded">
                                        <h4 class="text-info mb-1">
                                            @php
                                                $overtime = $presensi->calculateOvertime();
                                                $jamLembur = floor($overtime / 60);
                                                $menitLembur = $overtime % 60;
                                            @endphp
                                            {{ $jamLembur }}j {{ $menitLembur }}m
                                        </h4>
                                        <small class="text-muted">Lembur</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($presensi->catatan_admin)
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <h4 class="text-primary mb-3">
                                <i class="fas fa-comment"></i> Catatan Admin
                            </h4>
                            <div class="alert alert-info">
                                {{ $presensi->catatan_admin }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Form Approval untuk Admin -->
            @if(Auth::user()->role === 'admin' && $presensi->status_approval === App\Models\Presensi::STATUS_APPROVAL_PENDING)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h4 class="m-0 font-weight-bold text-primary">Approval Presensi</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form action="{{ route('admin.presensi.approve', $presensi->id) }}" method="POST" class="mb-3">
                                @csrf
                                <div class="form-group">
                                    <label for="catatan_admin_approve">Catatan (Opsional)</label>
                                    <textarea name="catatan_admin" id="catatan_admin_approve" class="form-control" rows="3" placeholder="Catatan persetujuan..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> Setujui Presensi
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form action="{{ route('admin.presensi.reject', $presensi->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="catatan_admin_reject">Alasan Penolakan *</label>
                                    <textarea name="catatan_admin" id="catatan_admin_reject" class="form-control" rows="3" placeholder="Alasan penolakan..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-times"></i> Tolak Presensi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Foto Presensi -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-primary">Foto Presensi</h3>
                </div>
                <div class="card-body">
                    <!-- Foto Check In -->
                    <div class="mb-4">
                        <h4 class="text-success mb-3">
                            <i class="fas fa-camera"></i> Foto Check In
                        </h4>
                        @if($presensi->foto_masuk)
                            <div class="text-center">
                                <img src="{{ Storage::url($presensi->foto_masuk) }}" 
                                     alt="Foto Check In" 
                                     class="img-fluid rounded border shadow-sm"
                                     style="max-height: 200px; cursor: pointer;"
                                     data-toggle="modal" 
                                     data-target="#fotoMasukModal">
                            </div>
                        @else
                            <div class="text-center p-4 bg-light rounded">
                                <i class="fas fa-camera text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada foto check in</p>
                            </div>
                        @endif
                    </div>

                    <hr>

                    <!-- Foto Check Out -->
                    <div>
                        <h4 class="text-danger mb-3">
                            <i class="fas fa-camera"></i> Foto Check Out
                        </h4>
                        @if($presensi->foto_keluar)
                            <div class="text-center">
                                <img src="{{ Storage::url($presensi->foto_keluar) }}" 
                                     alt="Foto Check Out" 
                                     class="img-fluid rounded border shadow-sm"
                                     style="max-height: 200px; cursor: pointer;"
                                     data-toggle="modal" 
                                     data-target="#fotoKeluarModal">
                            </div>
                        @else
                            <div class="text-center p-4 bg-light rounded">
                                <i class="fas fa-camera text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">Belum ada foto check out</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timeline Aktivitas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h3 class="m-0 font-weight-bold text-primary">Timeline Aktivitas</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @if($presensi->jam_masuk)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <h4 class="mb-1">Check In</h4>
                                <p class="mb-1">{{ Carbon\Carbon::parse($presensi->jam_masuk)->format('H:i:s') }}</p>
                                <small class="text-muted">
                                    {{ Carbon\Carbon::parse($presensi->jam_masuk)->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endif

                        @if($presensi->jam_keluar)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <h4 class="mb-1">Check Out</h4>
                                <p class="mb-1">{{ Carbon\Carbon::parse($presensi->jam_keluar)->format('H:i:s') }}</p>
                                <small class="text-muted">
                                    {{ Carbon\Carbon::parse($presensi->jam_keluar)->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endif

                        @if($presensi->status_approval !== App\Models\Presensi::STATUS_APPROVAL_PENDING)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-{{ $presensi->status_approval_color }}"></div>
                            <div class="timeline-content">
                                <h4 class="mb-1">{{ $presensi->status_approval_label }}</h4>
                                <p class="mb-1">Admin</p>
                                <small class="text-muted">
                                    {{ $presensi->updated_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Foto Check In -->
@if($presensi->foto_masuk)
<div class="modal fade" id="fotoMasukModal" tabindex="-1" role="dialog" aria-labelledby="fotoMasukModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="fotoMasukModalLabel">Foto Check In</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Storage::url($presensi->foto_masuk) }}" alt="Foto Check In" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endif

<!-- Modal Foto Check Out -->
@if($presensi->foto_keluar)
<div class="modal fade" id="fotoKeluarModal" tabindex="-1" role="dialog" aria-labelledby="fotoKeluarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="fotoKeluarModalLabel">Foto Check Out</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ Storage::url($presensi->foto_keluar) }}" alt="Foto Check Out" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endif

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 12px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e3e6f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -23px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e3e6f0;
}

.timeline-content {
    padding-left: 20px;
}

.badge-lg {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
}
</style>
@endsection