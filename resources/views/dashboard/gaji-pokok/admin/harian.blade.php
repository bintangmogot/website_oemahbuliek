@extends('layouts.app')

@section('title', 'Gaji Pokok Harian - Admin')

@section('content')
<div class="container-fluid">
    {{-- Header: Title dan Date Picker --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gaji Pokok Harian</h1>
            <p class="mb-0 text-muted">Kelola gaji pokok per tanggal</p>
        </div>
        
        {{-- Date Picker --}}
        <div class="d-flex align-items-center">
            <form method="GET" action="{{ route('admin.gaji-pokok.harian') }}" class="d-flex align-items-center">
                <label class="me-2">Tanggal:</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="form-control me-2" style="width: auto;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </form>
        </div>
    </div>

    {{-- Alert jika tidak ada data --}}
    @if($gajiHarian->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Tidak ada data gaji pokok untuk tanggal {{ date('d/m/Y', strtotime($tanggal)) }}
        </div>
    @endif

    {{-- Tabel Gaji Harian --}}
    @if(!$gajiHarian->isEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    Data Gaji Pokok - {{ date('d/m/Y', strtotime($tanggal)) }}
                    <span class="badge badge-secondary ms-2">{{ $gajiHarian->count() }} Karyawan</span>
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Karyawan</th>
                                <th>Jam Kerja</th>
                                <th>Rate/Jam</th>
                                <th>Total Gaji</th>
                                <th>Potongan Terlambat</th>
                                <th>Gaji Bersih</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gajiHarian as $index => $gaji)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="ms-3">
                                                <div class="fw-bold">{{ $gaji->user->nama_lengkap }}</div>
                                                <div class="text-muted small">ID: {{ $gaji->user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $gaji->jumlah_jam_kerja }} jam</td>
                                    <td>Rp {{ number_format($gaji->rate_kerja_per_jam) }}</td>
                                    <td>Rp {{ number_format($gaji->total_gaji) }}</td>
                                    <td>Rp {{ number_format($gaji->total_potongan_terlambat) }}</td>
                                    <td>
                                        <strong>Rp {{ number_format($gaji->total_gaji - $gaji->total_potongan_terlambat) }}</strong>
                                    </td>
                                    <td>
                                        @if($gaji->status_pembayaran == 0)
                                            <span class="badge badge-danger">Belum Dibayar</span>
                                        @elseif($gaji->status_pembayaran == 1)
                                            <span class="badge badge-success">Sudah Dibayar</span>
                                        @else
                                            <span class="badge badge-warning">Dibayar Sebagian</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.gaji-pokok.show', $gaji->id) }}" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light">
                                <th colspan="4">Total</th>
                                <th>Rp {{ number_format($gajiHarian->sum('total_gaji')) }}</th>
                                <th>Rp {{ number_format($gajiHarian->sum('total_potongan_terlambat')) }}</th>
                                <th>
                                    <strong>Rp {{ number_format($gajiHarian->sum('total_gaji') - $gajiHarian->sum('total_potongan_terlambat')) }}</strong>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Back Button --}}
    <div class="d-flex justify-content-start">
        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Gaji
        </a>
    </div>
</div>

@push('scripts')
<script>
    // Auto submit form when date changes
    document.querySelector('input[name="tanggal"]').addEventListener('change', function() {
        this.form.submit();
    });
</script>
@endpush
@endsection