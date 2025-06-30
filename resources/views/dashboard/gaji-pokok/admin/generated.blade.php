@extends('layouts.app')
@section('title', 'Data Gaji Pokok Tersimpan')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        💾 Data Gaji Pokok Tersimpan
                    </h3>
                    <div class="d-flex gap-3">
                        <!-- Filter Button -->
                        <button class="btn btn-theme primary me-2 py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                        </button>
                        <!-- Back to Main Button -->
                        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary py-2 px-3">
                            <i class="bi bi-arrow-left" style="font-size: 1.2rem"></i> Kembali
                        </a>
                        <!-- Summary Button -->
                        <a href="{{ route('admin.gaji-pokok.summary') }}{{ request()->has('start_date') ? '?start_date=' . request('start_date') . '&end_date=' . request('end_date') : '' }}" class="btn btn-info py-2 px-3">
                            <i class="bi bi-graph-up" style="font-size: 1.2rem"></i> Ringkasan
                        </a>
                    </div>
                </div>

                <!-- Filter Collapse -->
                <div class="collapse mb-4" id="filterCollapse">
                    <div class="card card-body">
                        <form method="GET" action="{{ route('admin.gaji-pokok.generated') }}">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
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
                                <div class="col-md-2">
                                    <label class="form-label">Pegawai</label>
                                    <select class="form-select" name="user_id">
                                        <option value="">Semua Pegawai</option>
                                        @foreach($usersForFilter as $user)
                                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->nama_lengkap }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status Pembayaran</label>
                                    <select class="form-select" name="status_pembayaran">
                                        <option value="">Semua Status</option>
                                        @foreach($statusOptions as $key => $value)
                                            <option value="{{ $key }}" {{ request('status_pembayaran') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <a href="{{ route('admin.gaji-pokok.generated') }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Information Alert -->
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle"></i>
                    <strong>Informasi:</strong> Halaman ini menampilkan data gaji pokok yang sudah di-generate dan tersimpan di database. 
                    Data ini dapat dikelola status pembayarannya.
                </div>

                <!-- Statistics Cards -->
                @if(count($gajiPokokData) > 0)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center summary-card border-primary">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Total Record</h5>
                                    <h3 class="text-primary">{{ $totalSummary['total_records'] }}</h3>
                                    <small class="text-muted">data tersimpan</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card border-warning">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">Belum Dibayar</h5>
                                    <h4 class="text-warning">{{ $totalSummary['belum_dibayar']['jumlah'] }}</h4>
                                    <small class="text-muted">Rp {{ number_format($totalSummary['belum_dibayar']['total'], 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card border-success">
                                <div class="card-body">
                                    <h5 class="card-title text-success">Sudah Dibayar</h5>
                                    <h4 class="text-success">{{ $totalSummary['sudah_dibayar']['jumlah'] }}</h4>
                                    <small class="text-muted">Rp {{ number_format($totalSummary['sudah_dibayar']['total'], 0, ',', '.') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card border-info">
                                <div class="card-body">
                                    <h5 class="card-title text-info">Total Gaji</h5>
                                    <h4 class="text-info">Rp {{ number_format($totalSummary['total_gaji_bersih'], 0, ',', '.') }}</h4>
                                    <small class="text-muted">total keseluruhan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="background-color: #ca414e; color: white;">No</th>
                                <th style="background-color: #ca414e; color: white;">Nama Pegawai</th>
                                <th style="background-color: #ca414e; color: white;">Periode Bulan</th>
                                <th style="background-color: #ca414e; color: white;">Periode Kerja</th>
                                <th style="background-color: #ca414e; color: white;">Total Jam</th>
                                <th style="background-color: #ca414e; color: white;">Terlambat</th>
                                <th style="background-color: #ca414e; color: white;">Gaji Kotor</th>
                                <th style="background-color: #ca414e; color: white;">Potongan</th>
                                <th style="background-color: #ca414e; color: white;">Gaji Bersih</th>
                                <th style="background-color: #ca414e; color: white;">Status</th>
                                <th style="background-color: #ca414e; color: white;">Tanggal Generate</th>
                                <th style="background-color: #ca414e; color: white;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($gajiPokokData as $index => $gaji)
                            <tr>
                                <td>{{ $gajiPokokData->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                                {{ strtoupper(substr($gaji->user->nama_lengkap, 0, 1)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <strong>{{ $gaji->user->nama_lengkap }}</strong>
                                            <br><small class="text-muted">ID: {{ $gaji->user->id }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ Carbon\Carbon::parse($gaji->periode_bulan)->format('M Y') }}
                                    </span>
                                </td>
                                <td>
                                    {{ Carbon\Carbon::parse($gaji->periode_start)->format('d/m/Y') }} 
                                    <br>
                                    <small class="text-muted">s/d {{ Carbon\Carbon::parse($gaji->periode_end)->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <strong>{{ number_format($gaji->jumlah_jam_kerja, 2) }}</strong> jam
                                </td>
                                <td>
                                    @if($gaji->total_menit_terlambat > 0)
                                        <span class="text-danger fw-bold">{{ $gaji->total_menit_terlambat }} menit</span>
                                    @else
                                        <span class="text-success">0 menit</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-primary">Rp {{ number_format($gaji->gaji_kotor, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($gaji->total_potongan > 0)
                                        <span class="text-danger fw-bold">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-success">Rp 0</span>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-success fs-6">Rp {{ number_format($gaji->total_gaji_pokok, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @if($gaji->status_pembayaran == 0)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock-history"></i> Belum Dibayar
                                        </span>
                                    @elseif($gaji->status_pembayaran == 1)
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Sudah Dibayar
                                        </span>
                                        @if($gaji->tgl_bayar)
                                            <small class="d-block text-muted mt-1">
                                                {{ Carbon\Carbon::parse($gaji->tgl_bayar)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $gaji->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.gaji-pokok.show', $gaji->id) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Detail Gaji">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal"
                                                data-gaji-id="{{ $gaji->id }}"
                                                data-user-name="{{ $gaji->user->nama_lengkap }}"
                                                data-current-status="{{ $gaji->status_pembayaran }}"
                                                data-current-date="{{ $gaji->tgl_bayar }}"
                                                data-periode-start="{{ $gaji->periode_start }}"
                                                data-periode-end="{{ $gaji->periode_end }}"
                                                title="Update Status Pembayaran">
                                            <i class="bi bi-cash-coin"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">
                                    <div class="py-4">
                                        <i class="bi bi-database-x" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-2 text-muted">Tidak ada data gaji pokok yang tersimpan untuk periode yang dipilih</p>
                                        <small class="text-muted">Generate data gaji dari halaman utama untuk melihat data tersimpan di sini</small>
                                        <br>
                                        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-primary mt-2">
                                            <i class="bi bi-arrow-left"></i> Kembali ke Halaman Utama
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if(count($gajiPokokData) > 0)
                            <tfoot class="table-light">
                                <tr style="font-weight: bold;">
                                    <td colspan="4">TOTAL (Halaman Ini)</td>
                                    <td>{{ number_format($gajiPokokData->sum('jumlah_jam_kerja'), 2) }} jam</td>
                                    <td>{{ $gajiPokokData->sum('total_menit_terlambat') }} menit</td>
                                    <td>Rp {{ number_format($gajiPokokData->sum('gaji_kotor'), 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($gajiPokokData->sum('total_potongan'), 0, ',', '.') }}</td>
                                    <td><strong class="text-success">Rp {{ number_format($gajiPokokData->sum('total_gaji_pokok'), 0, ',', '.') }}</strong></td>
                                    <td colspan="3">-</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $gajiPokokData->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Update Status Pembayaran -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusModalLabel">
                    <i class="bi bi-cash-coin"></i> Update Status Pembayaran
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" method="POST" action="{{ route('admin.gaji-pokok.update-pembayaran') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="gaji_pokok_id" id="modal_gaji_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Pegawai:</strong></label>
                        <p id="modal_user_name" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Periode:</strong></label>
                        <p id="modal_period" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status_pembayaran" class="form-label"><strong>Status Pembayaran:</strong></label>
                        <select class="form-select" name="status_pembayaran" id="status_pembayaran" required>
                            <option value="0">Belum Dibayar</option>
                            <option value="1">Sudah Dibayar</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="tanggal_bayar_group">
                        <label for="tgl_bayar" class="form-label"><strong>Tanggal Bayar:</strong></label>
                        <input type="date" class="form-control" name="tgl_bayar" id="tgl_bayar">
                        <small class="form-text text-muted">Kosongkan jika belum dibayar</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.summary-card {
    transition: transform 0.2s;
}
.summary-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.avatar-sm {
    flex-shrink: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle modal update status
    const updateStatusModal = document.getElementById('updateStatusModal');
    if (updateStatusModal) {
        updateStatusModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const gajiId = button.getAttribute('data-gaji-id');
            const userName = button.getAttribute('data-user-name');
            const currentStatus = button.getAttribute('data-current-status');
            const currentDate = button.getAttribute('data-current-date');
            const periodeStart = button.getAttribute('data-periode-start');
            const periodeEnd = button.getAttribute('data-periode-end');

            // Update modal content
            document.getElementById('modal_gaji_id').value = gajiId;
            document.getElementById('modal_user_name').textContent = userName;
            document.getElementById('modal_period').textContent = 
                new Date(periodeStart).toLocaleDateString('id-ID') + ' - ' + 
                new Date(periodeEnd).toLocaleDateString('id-ID');
            document.getElementById('status_pembayaran').value = currentStatus;
            document.getElementById('tgl_bayar').value = currentDate || '';

            // Show/hide tanggal bayar berdasarkan status
            toggleTanggalBayar(currentStatus);
        });
    }

    // Handle status pembayaran change
    const statusSelect = document.getElementById('status_pembayaran');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            toggleTanggalBayar(this.value);
        });
    }
});

function toggleTanggalBayar(status) {
    const tanggalBayarGroup = document.getElementById('tanggal_bayar_group');
    if (status == '1') {
        tanggalBayarGroup.style.display = 'block';
        document.getElementById('tgl_bayar').required = true;
        // Set default tanggal hari ini jika kosong
        if (!document.getElementById('tgl_bayar').value) {
            document.getElementById('tgl_bayar').value = new Date().toISOString().split('T')[0];
        }
    } else {
        tanggalBayarGroup.style.display = 'none';
        document.getElementById('tgl_bayar').required = false;
        document.getElementById('tgl_bayar').value = '';
    }
}

</script>


@endsection