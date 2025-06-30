@extends('layouts.app')

@section('title', 'Detail Gaji Pokok - ' . $gajiPokok->user->nama_lengkap)

@section('content')
<x-session-status/>

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Detail Gaji Pokok</h3>
                <p class="text-muted mb-0">{{ $gajiPokok->user->nama_lengkap }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-edit"></i> Update Status
                </button>
            </div>
        </div>

        {{-- Status Badge --}}
        <div class="mb-4">
            @if($gajiPokok->status_pembayaran == 0)
                <span class="badge bg-danger fs-6 px-3 py-2">
                    <i class="fas fa-clock"></i> Belum Dibayar
                </span>
            @elseif($gajiPokok->status_pembayaran == 1)
                <span class="badge bg-success fs-6 px-3 py-2">
                    <i class="fas fa-check-circle"></i> Sudah Dibayar
                </span>
            @else
                <span class="badge bg-warning fs-6 px-3 py-2">
                    <i class="fas fa-minus-circle"></i> Dibayar Sebagian
                </span>
            @endif
        </div>

        <div class="row">
            {{-- Informasi Karyawan --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Informasi Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="avatar-lg rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-user fa-2x"></i>
                            </div>
                            <h5 class="fw-bold">{{ $gajiPokok->user->nama_lengkap }}</h5>
                            <p class="text-muted">{{ $gajiPokok->user->email }}</p>
                        </div>
                        
                        <hr>
                        
                        <div class="row text-center">
                            <div class="col-12 mb-2">
                                <small class="text-muted">Role</small>
                                <div class="fw-semibold">{{ ucfirst($gajiPokok->user->role) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Detail Gaji --}}
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Detail Gaji</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Periode Awal</label>
                                <div class="fw-semibold">{{ $gajiPokok->periode_awal->format('d F Y') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Periode Akhir</label>
                                <div class="fw-semibold">{{ $gajiPokok->periode_akhir->format('d F Y') }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Jumlah Jam Kerja</label>
                                <div class="fw-semibold">{{ $gajiPokok->jumlah_jam_kerja }} jam</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Rate per Jam</label>
                                <div class="fw-semibold">Rp {{ number_format($gajiPokok->rate_kerja_per_jam) }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Rate Potongan per Menit</label>
                                <div class="fw-semibold">Rp {{ number_format($gajiPokok->rate_potongan_per_menit) }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Dibayar</label>
                                <div class="fw-semibold">
                                    {{ $gajiPokok->tgl_bayar ? $gajiPokok->tgl_bayar->format('d F Y') : 'Belum dibayar' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Perhitungan Gaji --}}
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calculator"></i> Perhitungan Gaji</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-semibold">Total Gaji Kotor</td>
                                    <td class="text-end">Rp {{ number_format($gajiPokok->total_gaji) }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold text-danger">Potongan Keterlambatan</td>
                                    <td class="text-end text-danger">- Rp {{ number_format($gajiPokok->total_potongan_terlambat) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold fs-5">Total Gaji Bersih</td>
                                    <td class="text-end fw-bold fs-5 text-success">
                                        Rp {{ number_format($gajiPokok->total_gaji - $gajiPokok->total_potongan_terlambat) }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline/History --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-marker bg-primary"></div>
                                <div class="timeline-content">
                                    <small class="text-muted">{{ $gajiPokok->created_at->format('d M Y H:i') }}</small>
                                    <div class="fw-semibold">Gaji Dibuat</div>
                                </div>
                            </div>
                            @if($gajiPokok->updated_at != $gajiPokok->created_at)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-info"></div>
                                    <div class="timeline-content">
                                        <small class="text-muted">{{ $gajiPokok->updated_at->format('d M Y H:i') }}</small>
                                        <div class="fw-semibold">Terakhir Diupdate</div>
                                    </div>
                                </div>
                            @endif
                            @if($gajiPokok->tgl_bayar)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <small class="text-muted">{{ $gajiPokok->tgl_bayar->format('d M Y') }}</small>
                                        <div class="fw-semibold">Gaji Dibayar</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Presensi Terkait --}}
        @if($gajiPokok->presensi && $gajiPokok->presensi->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock"></i> Data Presensi Terkait</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th style="background-color: #ca414e; color: white;">Tanggal</th>
                                        <th style="background-color: #ca414e; color: white;">Jam Masuk</th>
                                        <th style="background-color: #ca414e; color: white;">Jam Keluar</th>
                                        <th style="background-color: #ca414e; color: white;">Jam Kerja</th>
                                        <th style="background-color: #ca414e; color: white;">Status</th>
                                        <th style="background-color: #ca414e; color: white;">Terlambat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gajiPokok->presensi as $presensi)
                                    <tr>
                                        <td>{{ $presensi->tanggal->format('d M Y') }}</td>
                                        <td>{{ $presensi->jam_masuk->format('H:i') }}</td>
                                        <td>{{ $presensi->jam_keluar ? $presensi->jam_keluar->format('H:i') : '-' }}</td>
                                        <td>{{ $presensi->jam_kerja_efektif ?? 0 }} jam</td>
                                        <td>
                                            <span class="badge 
                                                @if($presensi->status == 'hadir') bg-success
                                                @elseif($presensi->status == 'late') bg-warning
                                                @else bg-danger
                                                @endif">
                                                {{ ucfirst($presensi->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($presensi->menit_terlambat > 0)
                                                <span class="text-danger">{{ $presensi->menit_terlambat }} menit</span>
                                            @else
                                                <span class="text-success">Tepat waktu</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Update Status --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">Update Status Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.gaji-pokok.update-pembayaran', $gajiPokok->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" id="status_pembayaran" class="form-select" required>
                            <option value="0" {{ $gajiPokok->status_pembayaran == 0 ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="1" {{ $gajiPokok->status_pembayaran == 1 ? 'selected' : '' }}>Sudah Dibayar</option>
                            <option value="2" {{ $gajiPokok->status_pembayaran == 2 ? 'selected' : '' }}>Dibayar Sebagian</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-lg {
    width: 80px;
    height: 80px;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -1.5rem;
    top: 1.5rem;
    width: 2px;
    height: calc(100% - 0.5rem);
    background: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -1.75rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    padding-left: 0.5rem;
}
</style>
@endpush