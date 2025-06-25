@extends('layouts.app')
@section('title', 'Laporan Gaji Lembur')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <div>
                <h3 class="fw-bold mb-0">📊 Laporan Gaji Lembur</h3>
                <p class="text-white mb-0">Periode: {{ Carbon\Carbon::create()->month($bulan)->format('F') }} {{ $tahun }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('gaji-lembur.index') }}" class="btn btn-theme secondary p-2 px-md-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <button class="btn btn-success p-2 px-md-3" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak Laporan
                </button>
            </div>
        </div>

        {{-- Filter Periode --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">Filter Periode</h5>
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-select">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                    {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tahun</label>
                        <select name="tahun" class="form-select">
                            @for($year = Carbon\Carbon::now()->year; $year >= Carbon\Carbon::now()->year - 3; $year--)
                                <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-theme info p-2 px-md-3">Tampilkan Laporan</button>
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
                                <th>No</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan</th>
                                <th>Total Hari Lembur</th>
                                <th>Total Jam Lembur</th>
                                <th>Total Gaji Lembur</th>
                                <th>Sudah Dibayar</th>
                                <th>Belum Dibayar</th>
                                <th>Persentase Pembayaran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($laporanPerPegawai as $index => $laporan)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $laporan->user->nama_lengkap ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ $laporan->user->jabatan ?? '—' }}</td>
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
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
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
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        {{-- Chart Section --}}
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Status Pembayaran</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="paymentStatusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0">Top 5 Pegawai Lembur Terbanyak</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="topEmployeesChart" width="400" height="200"></canvas>
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

// Top Employees Chart
const topEmployeesCtx = document.getElementById('topEmployeesChart').getContext('2d');
const topEmployees = @json($laporanPerPegawai->take(5));

new Chart(topEmployeesCtx, {
    type: 'bar',
    data: {
        labels: topEmployees.map(emp => emp.user.nama_lengkap?.split(' ')[0] || 'N/A'),
        datasets: [{
            label: 'Jam Lembur',
            data: topEmployees.map(emp => emp.total_jam),
            backgroundColor: '#007bff',
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value + ' jam';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
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