@extends('layouts.app')
@section('title', 'Detail Breakdown Gaji Pokok - ' . $user->nama_lengkap)

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        📋 Detail Breakdown Gaji Pokok - {{ $user->nama_lengkap }}
                    </h3>
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.gaji-pokok.index', $user->id) }}?start_date={{ $startDate->format('Y-m-d') }}&end_date={{ $endDate->format('Y-m-d') }}" class="btn btn-secondary py-2 px-3">
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
                        <form method="GET" action="{{ route('admin.gaji-pokok.detail-realtime') }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ route('admin.gaji-pokok.detail-realtime') }}?user_id={{ $user->id }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if($gajiData)
                <!-- Informasi Umum -->
                <div class="row mb-4">
                                @if(isset($gajiData['is_realtime']) && $gajiData['is_realtime'])
                                <div class="alert alert-info mb-4">
                                    <i class="bi bi-clock-history"></i> 
                                    <strong>Data Realtime:</strong> Data ini dihitung secara real-time berdasarkan presensi terkini dan belum disimpan sebagai data gaji resmi.
                                </div>
                                @endif
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Informasi Periode</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Periode:</strong></td>
                                        <td>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Hari Kerja:</strong></td>
                                        <td>{{ $gajiData['total_hari_kerja'] }} hari</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Pegawai:</strong></td>
                                        <td>{{ $user->nama_lengkap }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Ringkasan Gaji</h5>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Total Jam Kerja:</strong></td>
                                        <td>{{ number_format($gajiData['total_jam_kerja'], 2) }} jam</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Keterlambatan:</strong></td>
                                        <td>{{ $gajiData['total_menit_terlambat'] }} menit</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Gaji Kotor:</strong></td>
                                        <td>Rp {{ number_format($gajiData['gaji_kotor'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Total Potongan:</strong></td>
                                        <td>Rp {{ number_format($gajiData['total_potongan'], 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="table-success">
                                        <td><strong>Gaji Bersih:</strong></td>
                                        <td><strong>Rp {{ number_format($gajiData['total_gaji_pokok'], 0, ',', '.') }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Breakdown Tarif -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Breakdown Perhitungan Gaji</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <h6><strong>Perhitungan Gaji Kotor:</strong></h6>
                                            <p class="mb-1">{{ number_format($gajiData['total_jam_kerja'], 2) }} jam × Rp {{ number_format($gajiData['tarif_per_jam'] ?? 15000, 0, ',', '.') }}/jam</p>
                                            <p class="mb-0"><strong>= Rp {{ number_format($gajiData['gaji_kotor'], 0, ',', '.') }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-warning">
                                            <h6><strong>Perhitungan Potongan:</strong></h6>
                                            <p class="mb-1">{{ $gajiData['total_menit_terlambat'] }} menit × Rp {{ number_format($gajiData['tarif_potongan_per_menit'] ?? 500, 0, ',', '.') }}/menit</p>
                                            <p class="mb-0"><strong>= Rp {{ number_format($gajiData['total_potongan'], 0, ',', '.') }}</strong></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="alert alert-success">
                                        <h5><strong>Gaji Bersih = Gaji Kotor - Potongan</strong></h5>
                                        <h4>Rp {{ number_format($gajiData['gaji_kotor'], 0, ',', '.') }} - Rp {{ number_format($gajiData['total_potongan'], 0, ',', '.') }} = <strong>Rp {{ number_format($gajiData['total_gaji_pokok'], 0, ',', '.') }}</strong></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Per Hari -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Detail Presensi Per Hari ({{ $totalHariKerja }} hari kerja)</h5>
                            </div>
                            <div class="card-body">
                                @if(count($detailPerHari) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr style="background-color:#FFD9D9">
                                                    <th style="background-color: #ca414e; color: white;">No</th>
                                                    <th style="background-color: #ca414e; color: white;">Tanggal</th>
                                                    <th style="background-color: #ca414e; color: white;">Hari</th>
                                                    <th style="background-color: #ca414e; color: white;">Shift</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Masuk</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Keluar</th>
                                                    <th style="background-color: #ca414e; color: white;">Jam Kerja Efektif</th>
                                                    <th style="background-color: #ca414e; color: white;">Keterlambatan</th>
                                                    <th style="background-color: #ca414e; color: white;">Kontribusi Gaji</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($detailPerHari as $index => $detail)
                                                @php
                                                // ubah hari inggris jadi indo
                                                    $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                                                    $hariInggris = $detail['tanggal']->format('l'); 
                                                    $jamKerjaDecimal = $detail['jam_kerja_efektif_menit'] / 60;
                                                    $tarifPerJam = $gajiData['tarif_per_jam'] ?? 15000;
                                                    $tarifPotonganPerMenit = $gajiData['tarif_potongan_per_menit'] ?? 500;
                                                    $kontribusiGaji = $jamKerjaDecimal * $tarifPerJam;
                                                    $potonganHari = $detail['menit_terlambat'] * $tarifPotonganPerMenit;
                                                    $gajiHariBersih = $kontribusiGaji - $potonganHari;
                                                @endphp
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($detail['tanggal'])->format('d/m/Y') }}</td>
                                                                <td>{{ $hari[$hariInggris] }}</td>
                                                        <td>
                                                            <span class="badge bg-primary">{{ $detail['shift'] }}</span>
                                                        </td>
                                                        <td>{{ $detail['jam_masuk'] ? \Carbon\Carbon::parse($detail['jam_masuk'])->format('H:i') : '-' }}</td>
                                                        <td>{{ $detail['jam_keluar'] ? \Carbon\Carbon::parse($detail['jam_keluar'])->format('H:i') : '-' }}</td>
                                                        <td>
                                                            <strong>{{ $detail['jam_kerja_efektif_formatted'] }}</strong>
                                                            <br><small class="text-muted">({{ number_format($jamKerjaDecimal, 2) }} jam)</small>
                                                        </td>
                                                        <td>
                                                            @if($detail['menit_terlambat'] > 0)
                                                                <span class="text-danger">
                                                                    <strong>{{ $detail['menit_terlambat'] }} menit</strong>
                                                                    <br><small>-Rp {{ number_format($potonganHari, 0, ',', '.') }}</small>
                                                                </span>
                                                            @else
                                                                <span class="text-success">
                                                                    <i class="bi bi-check-circle"></i> Tepat waktu
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="text-end">
                                                                <div class="text-success">+Rp {{ number_format($kontribusiGaji, 0, ',', '.') }}</div>
                                                                @if($potonganHari > 0)
                                                                    <div class="text-danger">-Rp {{ number_format($potonganHari, 0, ',', '.') }}</div>
                                                                    <hr class="my-1">
                                                                    <div class="fw-bold">Rp {{ number_format($gajiHariBersih, 0, ',', '.') }}</div>
                                                                @else
                                                                    <div class="fw-bold">Rp {{ number_format($kontribusiGaji, 0, ',', '.') }}</div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="table-secondary">
                                                <tr>
                                                    <td colspan="6"><strong>TOTAL</strong></td>
                                                    <td><strong>{{ number_format($gajiData['total_jam_kerja'], 2) }} jam</strong></td>
                                                    <td>
                                                        <strong class="text-danger">{{ $gajiData['total_menit_terlambat'] }} menit</strong>
                                                        @if($gajiData['total_potongan'] > 0)
                                                            <br><small>-Rp {{ number_format($gajiData['total_potongan'], 0, ',', '.') }}</small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="text-success">+Rp {{ number_format($gajiData['gaji_kotor'], 0, ',', '.') }}</div>
                                                        @if($gajiData['total_potongan'] > 0)
                                                            <div class="text-danger">-Rp {{ number_format($gajiData['total_potongan'], 0, ',', '.') }}</div>
                                                            <hr class="my-1">
                                                        @endif
                                                        <div class="fw-bold fs-5 text-primary">Rp {{ number_format($gajiData['total_gaji_pokok'], 0, ',', '.') }}</div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Summary Statistics -->
                                    <div class="row mt-4">
                                        <div class="col-md-3">
                                            <div class="card bg-primary text-white text-center">
                                                <div class="card-body">
                                                    <h4>{{ $totalHariKerja }}</h4>
                                                    <p class="mb-0">Hari Kerja</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white text-center">
                                                <div class="card-body">
                                                    <h4>{{ number_format($gajiData['total_jam_kerja'] / $totalHariKerja, 1) }}</h4>
                                                    <p class="mb-0">Rata-rata Jam/Hari</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-warning text-white text-center">
                                                <div class="card-body">
                                                    <h4>{{ round($gajiData['total_menit_terlambat'] / max($totalHariKerja, 1), 1) }}</h4>
                                                    <p class="mb-0">Rata-rata Terlambat/Hari</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white text-center">
                                                <div class="card-body">
                                                    <h4>{{ number_format($gajiData['total_gaji_pokok'] / $totalHariKerja, 0) }}</h4>
                                                    <p class="mb-0">Rata-rata Gaji/Hari</p>
                                                </div>
                                            </div>
                                        </div>
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
                @else
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="bi bi-info-circle" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Tidak Ada Data</h5>
                                <p class="text-muted">Silakan pilih periode tanggal untuk melihat data gaji pokok pegawai.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto hide filter collapse after form submission
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_date') && urlParams.has('end_date')) {
            const filterCollapse = document.getElementById('filterCollapse');
            if (filterCollapse) {
                filterCollapse.classList.remove('show');
            }
        }

        // Validate date range
        const startDateInput = document.querySelector('input[name="start_date"]');
        const endDateInput = document.querySelector('input[name="end_date"]');
        
        if (startDateInput && endDateInput) {
            function validateDateRange() {
                const startDate = new Date(startDateInput.value);
                const endDate = new Date(endDateInput.value);
                
                if (startDate && endDate && startDate > endDate) {
                    endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
                } else {
                    endDateInput.setCustomValidity('');
                }
            }
            
            startDateInput.addEventListener('change', validateDateRange);
            endDateInput.addEventListener('change', validateDateRange);
        }

        // Add tooltip for calculation details
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Print functionality
        function printSalaryDetail() {
            const printWindow = window.open('', '_blank');
            const content = document.querySelector('.container').innerHTML;
            
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Detail Gaji Pokok (Realtime)- {{ $user->nama_lengkap ?? 'Pegawai' }}</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        @media print {
                            .btn, .collapse, .card-header button { display: none !important; }
                            .card { border: 1px solid #000 !important; }
                            .table { font-size: 12px; }
                        }
                        body { font-family: Arial, sans-serif; }
                    </style>
                </head>
                <body>
                    <div class="container-fluid p-3">
                        ${content}
                    </div>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            printWindow.focus();
            
            setTimeout(() => {
                printWindow.print();
            }, 250);
        }

        // Add print button if data exists
        @if($gajiData && count($detailPerHari) > 0)
        const headerDiv = document.querySelector('.card-header-theme');
        if (headerDiv) {
            const printBtn = document.createElement('button');
            printBtn.className = 'btn btn-outline-primary py-2 px-3';
            printBtn.innerHTML = '<i class="bi bi-printer"></i> Print';
            printBtn.onclick = printSalaryDetail;
            
            const buttonGroup = headerDiv.querySelector('.d-flex.gap-3');
            if (buttonGroup) {
                buttonGroup.appendChild(printBtn);
            }
        }
        @endif
    });
</script>
@endpush

@push('styles')
<style>
    .card-header-theme {
        border-bottom: 2px solid #0d6efd;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .alert {
        border: none;
        border-radius: 0.5rem;
    }
    
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .card-body {
        padding: 1.25rem;
    }
    
    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .btn-theme.primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    
    .btn-theme.primary:hover {
        background-color: #0b5ed7;
        border-color: #0a58ca;
    }
    
    @media (max-width: 768px) {
        .card-header-theme {
            flex-direction: column;
            align-items: stretch;
        }
        
        .card-header-theme .d-flex.gap-3 {
            justify-content: center;
            margin-top: 1rem;
        }
        
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
    
    .summary-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .summary-card:hover {
        transform: translateY(-2px);
    }
    
    .text-truncate-custom {
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 0.375rem;
    }
    
    .contribution-cell {
        min-width: 120px;
    }
    
    .working-hours-cell {
        min-width: 100px;
    }
</style>
@endpush
@endsection