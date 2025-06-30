@extends('layouts.app')
@section('title', 'Detail Gaji Pokok - ' . $user->nama_lengkap)

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        👤 Detail Gaji Pokok - {{ $user->nama_lengkap }}
                    </h3>
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary py-2 px-3">
                            <i class="bi bi-arrow-left" style="font-size: 1.2rem"></i> Kembali
                        </a>
                    </div>
                </div>

                <!-- Employee Info -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Pegawai</h5>
                                <p class="card-text">
                                    <strong>Nama:</strong> {{ $user->nama_lengkap }}<br>
                                    <strong>Email:</strong> {{ $user->email }}<br>
                                    <strong>Jabatan:</strong> {{ ucfirst($user->jabatan) }}
                                    <br>
                                    <strong>Pengaturan Gaji: </strong> {{ $user->pengaturanGaji->nama }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Periode</h5>
                                <p class="card-text">
                                    <strong>Periode:</strong> {{ \Carbon\Carbon::parse($gajiPokok->periode_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($gajiPokok->periode_end)->format('d/m/Y') }}<br>
                                    <strong>Total Hari Kerja:</strong> {{ $ringkasanPerhitungan['total_hari_kerja'] }} hari<br>
                                    <strong>Total Jam Kerja:</strong> {{ $ringkasanPerhitungan['total_jam_kerja_formatted'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Gaji -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Status Pembayaran</h5>
                                <span class="badge {{ $gajiPokok->status_pembayaran == \App\Models\GajiPokok::STATUS_PAID ? 'bg-success' : 'bg-warning' }} fs-6">
                                    {{ $statusOptions[$gajiPokok->status_pembayaran] ?? 'Belum Dibayar' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ringkasan Gaji -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Ringkasan Gaji Periode {{ \Carbon\Carbon::parse($gajiPokok->periode_start)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($gajiPokok->periode_end)->format('d/m/Y') }}</h5>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $ringkasanPerhitungan['total_hari_kerja'] }}</h4>
                                            <small class="text-muted">Hari Kerja</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-info">{{ $ringkasanPerhitungan['total_jam_kerja_formatted'] }}</h4>
                                            <small class="text-muted">Total Jam Kerja</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning">{{ $ringkasanPerhitungan['total_menit_terlambat'] }}</h4>
                                            <small class="text-muted">Menit Terlambat</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success">Rp {{ number_format($gajiPokok->total_gaji_pokok ?? 0, 0, ',', '.') }}</h4>
                                            <small class="text-muted">Gaji Bersih</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Tarif Per Jam:</strong></td>
                                                <td>Rp {{ number_format($ringkasanPerhitungan['tarif_per_jam'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tarif Potongan Per Menit:</strong></td>
                                                <td>Rp {{ number_format($ringkasanPerhitungan['tarif_potongan_per_menit'], 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Gaji Kotor:</strong></td>
                                                <td>Rp {{ number_format($gajiPokok->gaji_kotor ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Potongan:</strong></td>
                                                <td>Rp {{ number_format($gajiPokok->total_potongan ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="table-success">
                                                <td><strong>Gaji Bersih:</strong></td>
                                                <td><strong>Rp {{ number_format($gajiPokok->total_gaji_pokok ?? 0, 0, ',', '.') }}</strong></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Presensi -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Detail Presensi Per Hari</h5>
                            </div>
                            <div class="card-body">
                                @if(count($detailPerHari) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                                <thead style="background-color:#FFD9D9">
                                                <tr>
                                                    <th style="background-color: #ca414e; color: white;">No</th>
                                                    <th style="background-color: #ca414e; color: white;">Tanggal</th>
                                                    <th style="background-color: #ca414e; color: white;">Hari</th>
                                                    <th style="background-color: #ca414e; color: white;">Shift</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Masuk</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Keluar</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Kerja Efektif</th>
                                                    <th style="background-color: #ca414e; color: white;">Keterlambatan</th>
                                                    <th style="background-color: #ca414e; color: white;">Gaji Per Hari</th>
                                                    <th style="background-color: #ca414e; color: white;">Potongan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detailPerHari as $index => $detail)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($detail['tanggal'])->format('d/m/Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($detail['tanggal'])->locale('id')->translatedFormat('l') }}</td>
                                                        <td>{{ $detail['shift'] }}</td>
                                                        <td>{{ $detail['jam_masuk'] ? \Carbon\Carbon::parse($detail['jam_masuk'])->format('H:i') : '-' }}</td>
                                                        <td>{{ $detail['jam_keluar'] ? \Carbon\Carbon::parse($detail['jam_keluar'])->format('H:i') : '-' }}</td>
                                                        <td>{{ $detail['jam_kerja_efektif_formatted'] }}</td>
                                                        <td>
                                                            @if($detail['menit_terlambat'] > 0)
                                                                <span class="text-danger">{{ $detail['menit_terlambat'] }} menit</span>
                                                            @else
                                                                <span class="text-success">Tepat waktu</span>
                                                            @endif
                                                        </td>
                                                            <td>Rp {{ number_format($detail['gaji_per_hari'] ?? 0, 0, ',', '.') }}</td>
                                                            <td>
                                                                @if(isset($detail['potongan_per_hari']) && $detail['potongan_per_hari'] > 0)
                                                                    <span class="text-danger">Rp {{ number_format($detail['potongan_per_hari'], 0, ',', '.') }}</span>
                                                                @else
                                                                    <span class="text-success">-</span>
                                                                @endif
                                                            </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <td colspan="6"><strong>Total</strong></td>
                                                    <td><strong>{{ $ringkasanPerhitungan['total_jam_kerja_formatted'] }}</strong></td>
                                                    <td><strong>{{ $ringkasanPerhitungan['total_menit_terlambat'] }} menit</strong></td>
                                                    <td><strong>Rp {{ number_format($gajiPokok->gaji_kotor ?? 0, 0, ',', '.') }}</strong></td>
                                                    <td><strong>Rp {{ number_format($gajiPokok->total_potongan ?? 0, 0, ',', '.') }}</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-calendar-x" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="text-muted mt-2">Tidak ada data presensi untuk periode ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection