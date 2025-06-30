@extends('layouts.app')

@section('title', 'Detail Gaji Pokok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h3 class="card-title mb-1" style="font-weight: bold;">
                            📊 Detail Gaji Pokok
                        </h3>
                            <p class="text-muted mb-0">
                                @if(isset($gajiPokok->periode_bulan))
                                    Periode {{ Carbon\Carbon::parse($gajiPokok->periode_bulan)->format('F Y') }}
                                @else
                                    Periode {{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}
                                @endif
                            </p>
                    </div>
                    <div>
                        <a href="{{ route('pegawai.gaji-pokok.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Hari Kerja</h6>
                                <h4 class="mb-0">{{ $totalHariKerja ?? ($gajiPokok->total_hari_kerja ?? 0) }} hari</h4>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Jam Kerja</h6>
                                <h4 class="mb-0">{{ number_format($gajiPokok->jumlah_jam_kerja ?? $gajiData['total_jam_kerja'] ?? 0, 1) }} jam</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Total Keterlambatan</h6>
                                <h4 class="mb-0">{{ $gajiPokok->total_menit_terlambat ?? $gajiData['total_menit_terlambat'] ?? 0 }} menit</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h6 class="card-title">Status Pembayaran</h6>
                        <h6 class="mb-0">
                            @if(isset($gajiData['is_realtime']) && $gajiData['is_realtime'])
                                <span class="badge bg-info">Data Real-time</span>
                            @elseif($gajiPokok->status_pembayaran == 0)
                                Belum Dibayar
                            @elseif($gajiPokok->status_pembayaran == 1)
                                Sudah Dibayar
                            @endif
                        </h6>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Perhitungan Gaji -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">💰 Perhitungan Gaji</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Gaji Kotor:</strong></td>
                                        <td class="text-end">
                                            <span class="text-success fw-bold">
                                                    Rp {{ number_format($gajiPokok->gaji_kotor ?? $gajiData['gaji_kotor'] ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Potongan:</strong></td>
                                        <td class="text-end">
                                            <span class="text-danger fw-bold">
                                                - Rp {{ number_format($gajiPokok->total_potongan ?? $gajiData['total_potongan'] ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr class="border-top">
                                        <td><strong class="fs-5">Total Gaji Pokok:</strong></td>
                                        <td class="text-end">
                                            <span class="text-primary fw-bold fs-5">
                                                Rp {{ number_format($gajiPokok->total_gaji_pokok ?? $gajiData['total_gaji_pokok'] ?? 0, 0, ',', '.') }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-3 rounded">
                                    <h6 class="text-muted mb-2">Informasi Pembayaran</h6>
                                    <p class="mb-1">
                                        <strong>Status:</strong> 
                                        @if(isset($gajiData['is_realtime']) && $gajiData['is_realtime'])
                                            <span class="badge bg-info">Data Real-time</span>
                                        @elseif($gajiPokok->status_pembayaran == 0)
                                            <span class="badge bg-warning">Belum Dibayar</span>
                                        @elseif($gajiPokok->status_pembayaran == 1)
                                            <span class="badge bg-success">Sudah Dibayar</span>
                                        @endif
                                    </p>
                                    <p class="mb-0">
                                        <strong>Tanggal Bayar:</strong> 
                                        {{ $gajiPokok->tgl_bayar ? Carbon\Carbon::parse($gajiPokok->tgl_bayar)->format('d F Y') : 'Belum dibayar' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Presensi Per Hari -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📅 Detail Presensi Per Hari</h5>
                    </div>
                    <div class="card-body">
                        @if(count($detailPerHari) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Shift</th>
                                            <th>Jam Masuk</th>
                                            <th>Jam Keluar</th>
                                            <th>Jam Kerja Efektif</th>
                                            <th>Keterlambatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detailPerHari as $detail)
                                            <tr>
                                                <td>
                                                    <strong>{{ Carbon\Carbon::parse($detail['tanggal'])->format('d/m/Y') }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ Carbon\Carbon::parse($detail['tanggal'])->format('l') }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $detail['shift'] }}</span>
                                                </td>
                                                <td>{{ $detail['jam_masuk'] ? Carbon\Carbon::parse($detail['jam_masuk'])->format('H:i') : '-' }}</td>
                                                <td>{{ $detail['jam_keluar'] ? Carbon\Carbon::parse($detail['jam_keluar'])->format('H:i') : '-' }}</td>
                                                <td>
                                                    <span class="text-success fw-bold">
                                                        {{ $detail['jam_kerja_efektif_formatted'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($detail['menit_terlambat'] > 0)
                                                        <span class="text-danger fw-bold">
                                                            {{ $detail['menit_terlambat'] }} menit
                                                        </span>
                                                    @else
                                                        <span class="text-success">Tepat waktu</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-calendar-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h5 class="mt-3 text-muted">Tidak ada data presensi</h5>
                                <p class="text-muted">Belum ada presensi yang disetujui untuk periode ini</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading">
                                <i class="bi bi-info-circle"></i> Informasi Perhitungan
                            </h6>
                            <hr>
                            <ul class="mb-0">
                                <li>Gaji pokok dihitung berdasarkan jam kerja efektif pada shift normal yang telah disetujui</li>
                                <li>Jam kerja efektif adalah waktu kerja dalam batas jam shift (tidak termasuk lembur)</li>
                                <li>Potongan dikenakan untuk setiap menit keterlambatan</li>
                                <li>Tarif: Rp {{ number_format($gajiPokok->tarif_per_jam ?? $gajiData['tarif_per_jam'] ?? 15000, 0, ',', '.') }}/jam | Potongan: 
                                    Rp {{ number_format($gajiPokok->tarif_potongan_per_menit ?? $gajiData['tarif_potongan_per_menit'] ?? 500, 0, ',', '.') }}/menit keterlambatan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection