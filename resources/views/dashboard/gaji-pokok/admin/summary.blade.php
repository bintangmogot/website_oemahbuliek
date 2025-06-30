@extends('layouts.app')
@section('title', 'Ringkasan Gaji Pokok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        📊 Ringkasan Gaji Pokok
                    </h3>
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary py-2 px-3">
                            <i class="bi bi-arrow-left" style="font-size: 1.2rem"></i> Kembali
                        </a>
                        <!-- Filter Button -->
                        <button class="btn btn-theme primary me-2 py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Collapse -->
                <div class="collapse mb-4" id="filterCollapse">
                    <div class="card card-body">
                        <form method="GET" action="{{ route('admin.gaji-pokok.summary') }}">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Periode Bulan</label>
                                    <select class="form-select" name="periode_bulan">
                                        <option value="">Semua Bulan</option>
                                        @foreach($periodeBulanOptions as $periode)
                                            <option value="{{ $periode['value'] }}" {{ request('periode_bulan') == $periode['value'] ? 'selected' : '' }}>
                                                {{ $periode['label'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.gaji-pokok.summary') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <!-- Total Pegawai -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Pegawai</h6>
                                        <h4 class="mb-0">{{ $ringkasan['total_pegawai'] ?? 0 }}</h4>
                                        <small>Pegawai</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-people" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Belum Dibayar -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Belum Dibayar</h6>
                                        <h4 class="mb-0">{{ $ringkasan['belum_dibayar']['jumlah'] ?? 0 }}</h4>
                                        <small>Transaksi</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-clock-history" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="text-center">
                                    <strong>Rp {{ number_format($ringkasan['belum_dibayar']['total'] ?? 0, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sudah Dibayar -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Sudah Dibayar</h6>
                                        <h4 class="mb-0">{{ $ringkasan['sudah_dibayar']['jumlah'] ?? 0 }}</h4>
                                        <small>Transaksi</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="text-center">
                                    <strong>Rp {{ number_format($ringkasan['sudah_dibayar']['total'] ?? 0, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Keseluruhan -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="card-title">Total Gaji Bersih</h6>
                                        <h4 class="mb-0">{{ $ringkasan['total_transaksi'] ?? 0 }}</h4>
                                        <small>Transaksi</small>
                                    </div>
                                    <div class="align-self-center">
                                        <i class="bi bi-cash-stack" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="text-center">
                                    <strong>Rp {{ number_format($ringkasan['total_gaji_bersih'] ?? 0, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Summary Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Detail Ringkasan</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th style="background-color: #ca414e; color: white;">Komponen</th>
                                                <th style="background-color: #ca414e; color: white;">Jumlah</th>
                                                <th style="background-color: #ca414e; color: white;">Total Nominal</th>
                                                <th style="background-color: #ca414e; color: white;">Persentase</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><strong>Gaji Kotor</strong></td>
                                                <td>-</td>
                                                <td>Rp {{ number_format($ringkasan['total_gaji_kotor'] ?? 0, 0, ',', '.') }}</td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Potongan</strong></td>
                                                <td>-</td>
                                                <td>Rp {{ number_format($ringkasan['total_potongan'] ?? 0, 0, ',', '.') }}</td>
                                                <td>-</td>
                                            </tr>
                                            <tr class="table-light">
                                                <td><strong>Gaji Bersih</strong></td>
                                                <td>-</td>
                                                <td><strong>Rp {{ number_format($ringkasan['total_gaji_bersih'] ?? 0, 0, ',', '.') }}</strong></td>
                                                <td>-</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-warning">Belum Dibayar</span>
                                                </td>
                                                <td>{{ $ringkasan['belum_dibayar']['jumlah'] ?? 0 }} transaksi</td>
                                                <td>Rp {{ number_format($ringkasan['belum_dibayar']['total'] ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $totalGajiBersih = $ringkasan['total_gaji_bersih'] ?? 0;
                                                        $belumDibayarTotal = $ringkasan['belum_dibayar']['total'] ?? 0;
                                                        $persentase = $totalGajiBersih > 0 ? round(($belumDibayarTotal / $totalGajiBersih) * 100, 1) : 0;
                                                    @endphp
                                                    {{ $persentase }}%
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span class="badge bg-success">Sudah Dibayar</span>
                                                </td>
                                                <td>{{ $ringkasan['sudah_dibayar']['jumlah'] ?? 0 }} transaksi</td>
                                                <td>Rp {{ number_format($ringkasan['sudah_dibayar']['total'] ?? 0, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $totalGajiBersih = $ringkasan['total_gaji_bersih'] ?? 0;
                                                        $sudahDibayarTotal = $ringkasan['sudah_dibayar']['total'] ?? 0;
                                                        $persentase = $totalGajiBersih > 0 ? round(($sudahDibayarTotal / $totalGajiBersih) * 100, 1) : 0;
                                                    @endphp
                                                    {{ $persentase }}%
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection