@extends('layouts.app')

@section('title', 'Gaji Pokok Saya')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        💰 Gaji Pokok Saya
                    </h3>
                    <div class="d-flex gap-3">
                        <!-- Filter Button -->
                        <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-funnel" style="font-size: 1.2rem"></i> Filter
                        </button>
                    </div>
                </div>

                <!-- Filter Collapse -->
                <div class="collapse mb-4" id="filterCollapse">
                    <div class="card card-body">
                        <form method="GET" action="{{ route('pegawai.gaji-pokok.index') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Status Pembayaran</label>
                                    <select class="form-select" name="status_pembayaran">
                                        <option value="">Semua Status</option>
                                        @foreach($statusOptions as $key => $label)
                                            <option value="{{ $key }}" {{ request('status_pembayaran') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ route('pegawai.gaji-pokok.index') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ringkasan Cards -->
                <div class="row mb-4">
                    <!-- Belum Dibayar -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Belum Dibayar</h6>
                                        <h4 class="mb-0">Rp {{ number_format($ringkasan['belum_dibayar'], 0, ',', '.') }}</h4>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sudah Dibayar -->
                    <div class="col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Sudah Dibayar</h6>
                                        <h4 class="mb-0">Rp {{ number_format($ringkasan['sudah_dibayar'], 0, ',', '.') }}</h4>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Data Gaji Pokok</h5>
                    </div>
                    <div class="card-body">
                        @if($gajiPokokData->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Periode Kerja</th>
                                            <th>Total Jam Kerja</th>
                                            <th>Gaji Kotor</th>
                                            <th>Total Potongan</th>
                                            <th>Total Gaji</th>
                                            <th>Status</th>
                                            <th>Tgl Bayar</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gajiPokokData as $gaji)
                                            <tr>
                                                <td>
                                                    <strong>{{ Carbon\Carbon::parse($gaji->periode_start)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($gaji->periode_end)->format('d/m/Y') }}</strong>
                                                </td>
                                                <td>{{ number_format($gaji->jumlah_jam_kerja, 2) }} jam</td>
                                                <td>Rp {{ number_format($gaji->gaji_kotor, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</td>
                                                <td>
                                                    <strong class="text-success">
                                                        Rp {{ number_format($gaji->total_gaji_pokok, 0, ',', '.') }}
                                                    </strong>
                                                </td>
                                                <td>
                                                    @if($gaji->status_pembayaran == 0)
                                                        <span class="badge bg-warning">Belum Dibayar</span>
                                                    @elseif($gaji->status_pembayaran == 1)
                                                        <span class="badge bg-success">Sudah Dibayar</span>
                                                    @else
                                                        <span class="badge bg-info">Dibayar Sebagian</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $gaji->tgl_bayar ? Carbon\Carbon::parse($gaji->tgl_bayar)->format('d/m/Y') : '-' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('pegawai.gaji-pokok.detail', $gaji->id) }}" 
                                                       class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $gajiPokokData->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                <h5 class="mt-3 text-muted">Belum ada data gaji pokok</h5>
                                <p class="text-muted">Data gaji pokok akan muncul setelah admin melakukan generate gaji</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection