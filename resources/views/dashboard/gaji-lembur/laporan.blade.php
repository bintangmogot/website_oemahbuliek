@extends('layouts.app')
@section('title', 'Laporan Gaji Lembur')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <div>
                <h3 class="fw-bold mb-0">📊 Laporan Gaji Lembur</h3>
                {{-- <p class="text-white mb-0">Periode: {{ $tanggalMulai ? date('d/m/Y', strtotime($tanggalMulai)) : 'Semua' }} - {{ $tanggalSelesai ? date('d/m/Y', strtotime($tanggalSelesai)) : 'Semua' }}</p> --}}
            </div>
            <div class="d-flex gap-2">

                <a href="{{ route('gaji-lembur.index', ['tanggal_mulai' => request('tanggal_mulai'), 'tanggal_selesai' => request('tanggal_selesai'), 'status_pembayaran' => request('status_pembayaran')]) }}" 
                    class="btn btn-theme primary p-2 px-3" style="border-color: #B50000;">
                    <i class="fas fa-chart-bar"></i> Detail Lembur
                </a>
                <button class="btn btn-success p-2 px-md-3" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Filter Laporan</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-select" value="{{ request('tanggal_mulai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-select" value="{{ request('tanggal_selesai') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="0" {{ request('status_pembayaran') == '0' ? 'selected' : '' }}>Belum Dibayar</option>
                            <option value="1" {{ request('status_pembayaran') == '1' ? 'selected' : '' }}>Sudah Dibayar</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-theme info p-2 px-md-3 me-2">Tampilkan Laporan</button>
                        <a href="{{ route('gaji-lembur.index') }}" class="btn btn-theme primary p-2 px-3" style="border-color: #B50000;">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Total Jam Lembur</h5>
                        <h2>{{ number_format($totalKeseluruhan->total_jam ?? 0, 1) }}</h2>
                        <small>jam</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Total Gaji Lembur</h5>
                        <h2>{{ number_format($totalKeseluruhan->total_gaji ?? 0, 0, ',', '.') }}</h2>
                        <small>rupiah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5>Sudah Dibayar</h5>
                        <h2>{{ number_format($totalKeseluruhan->total_sudah_dibayar ?? 0, 0, ',', '.') }}</h2>
                        <small>rupiah</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5>Belum Dibayar</h5>
                        <h2>{{ number_format($totalKeseluruhan->total_belum_dibayar ?? 0, 0, ',', '.') }}</h2>
                        <small>rupiah</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Laporan Per Pegawai --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header" style="background-color:#FFD9D9">
                <h5 class="mb-0">Laporan Per Pegawai</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-borderless mb-0">
                        <thead style="background-color:#FFE5E5">
                            <tr>
                                <th style="background-color: #ca414e; color: white;">No</th>
                                <th style="background-color: #ca414e; color: white;">Nama Pegawai</th>
                                <th style="background-color: #ca414e; color: white;">Jabatan</th>
                                <th style="background-color: #ca414e; color: white;">Total Hari Lembur</th>
                                <th style="background-color: #ca414e; color: white;">Total Jam Lembur</th>
                                <th style="background-color: #ca414e; color: white;">Total Gaji Lembur</th>
                                <th style="background-color: #ca414e; color: white;">Sudah Dibayar</th>
                                <th style="background-color: #ca414e; color: white;">Belum Dibayar</th>
                                <th style="background-color: #ca414e; color: white;">Persentase Pembayaran</th>
                                <th style="background-color: #ca414e; color: white;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPerPegawai as $index => $laporan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $laporan->nama_lengkap ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $laporan->jabatan ?? '—' }}</td>
                                    <td class="text-center">{{ $laporan->total_hari_lembur }}</td>
                                    <td class="text-center">{{ number_format($laporan->total_jam, 1) }} jam</td>
                                    <td class="text-end">
                                        <strong>Rp {{ number_format($laporan->total_gaji, 0, ',', '.') }}</strong>
                                    </td>
                                    <td class="text-end text-success">
                                        Rp {{ number_format($laporan->total_sudah_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-end text-danger">
                                        Rp {{ number_format($laporan->total_belum_dibayar, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $persentase = $laporan->total_gaji > 0 ? ($laporan->total_sudah_dibayar / $laporan->total_gaji) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                @if($persentase >= 100) bg-success
                                                @elseif($persentase >= 50) bg-warning
                                                @else bg-danger
                                                @endif" 
                                                role="progressbar" 
                                                style="width: {{ $persentase }}%">
                                                {{ number_format($persentase, 0) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <a href="{{ route('gaji-lembur.detail-pegawai', ['user_id' => $laporan->user_id]) }}"
                                        class="btn btn-theme info btn-sm p-2 px-md-3">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4">
                                        Tidak ada data untuk periode ini
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($laporanPerPegawai->count() > 0)
                        <tfoot style="background-color:#F8F9FA">
                            <tr class="fw-bold">
                                <td colspan="3">TOTAL KESELURUHAN</td>
                                <td class="text-center">{{ $totalKeseluruhan->total_record ?? 0 }}</td>
                                <td class="text-center">{{ number_format($totalKeseluruhan->total_jam ?? 0, 1) }} jam</td>
                                <td class="text-end">Rp {{ number_format($totalKeseluruhan->total_gaji ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end text-success">Rp {{ number_format($totalKeseluruhan->total_sudah_dibayar ?? 0, 0, ',', '.') }}</td>
                                <td class="text-end text-danger">Rp {{ number_format($totalKeseluruhan->total_belum_dibayar ?? 0, 0, ',', '.') }}</td>
                                <td class="text-center">
                                    @php
                                        $totalPersentase = ($totalKeseluruhan->total_gaji ?? 0) > 0 ? 
                                            (($totalKeseluruhan->total_sudah_dibayar ?? 0) / ($totalKeseluruhan->total_gaji ?? 1)) * 100 : 0;
                                    @endphp
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar 
                                            @if($totalPersentase >= 100) bg-success
                                            @elseif($totalPersentase >= 50) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $totalPersentase }}%">
                                            {{ number_format($totalPersentase, 0) }}%
                                        </div>
                                    </div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Status Pembayaran</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center">
                        <div style="width: 100%; max-width: 400px;">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Info --}}
        <div class="mt-4 text-center text-muted">
            <small>Laporan digenerate pada: {{ Carbon\Carbon::now()->format('d F Y H:i:s') }}</small>
        </div>

    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
// Payment Status Chart
const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
new Chart(paymentStatusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Sudah Dibayar', 'Belum Dibayar'],
        datasets: [{
            data: [
                {{ $totalKeseluruhan->total_sudah_dibayar ?? 0 }},
                {{ $totalKeseluruhan->total_belum_dibayar ?? 0 }}
            ],
            backgroundColor: ['#28a745', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});


</script>

{{-- Print Styles --}}
<style>
@media print {
    .btn, .card-header button {
        display: none !important;
    }
    
    .card {
        box-shadow: none !important;
        border: 1px solid #dee2e6 !important;
    }
    
    .container {
        max-width: 100% !important;
        padding: 0 !important;
    }
    
    body {
        font-size: 12px !important;
    }
    
    .progress {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
}
</style>

@endsection