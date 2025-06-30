@extends('layouts.app')

@section('title', 'Gaji Pokok Harian - Admin')

@section('content')
<x-session-status/>

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <h3 class="fw-bold mb-0">Gaji Pokok Harian</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Filter Tanggal --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <form method="GET" action="{{ route('admin.gaji-pokok.harian') }}">
                    <div class="input-group">
                        <input type="date" name="tanggal" class="form-control" value="{{ $tanggal }}" required>
                        <button type="submit" class="btn btn-theme primary">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Data Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Karyawan</th>
                        <th>Jam Kerja</th>
                        <th>Rate/Jam</th>
                        <th>Total Gaji</th>
                        <th>Potongan</th>
                        <th>Gaji Bersih</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($gajiHarian as $index => $gaji)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong>{{ $gaji->user->nama_lengkap }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $gaji->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ number_format($gaji->jumlah_jam_kerja, 2) }} jam</td>
                            <td>Rp {{ number_format($gaji->rate_kerja_per_jam) }}</td>
                            <td>Rp {{ number_format($gaji->total_gaji) }}</td>
                            <td>Rp {{ number_format($gaji->total_potongan_terlambat) }}</td>
                            <td>
                                <strong>Rp {{ number_format($gaji->total_gaji - $gaji->total_potongan_terlambat) }}</strong>
                            </td>
                            <td>
                                @if($gaji->status_pembayaran == 0)
                                    <span class="badge bg-danger">Belum Dibayar</span>
                                @elseif($gaji->status_pembayaran == 1)
                                    <span class="badge bg-success">Sudah Dibayar</span>
                                @else
                                    <span class="badge bg-warning">Dibayar Sebagian</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.gaji-pokok.show', $gaji->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data gaji untuk tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        @if($gajiHarian->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Ringkasan Tanggal {{ \Carbon\Carbon::parse($tanggal)->format('d/m/Y') }}</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Total Karyawan:</strong> {{ $gajiHarian->count() }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total Jam:</strong> {{ number_format($gajiHarian->sum('jumlah_jam_kerja'), 2) }} jam
                                </div>
                                <div class="col-md-3">
                                    <strong>Total Gaji:</strong> Rp {{ number_format($gajiHarian->sum('total_gaji')) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Total Potongan:</strong> Rp {{ number_format($gajiHarian->sum('total_potongan_terlambat')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection