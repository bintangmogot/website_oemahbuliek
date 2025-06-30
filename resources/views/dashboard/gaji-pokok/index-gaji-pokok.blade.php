@extends('layouts.app')

@section('title', 'Daftar Gaji Pokok - Admin')

@section('content')
<x-session-status/>

<div class="container py-5">
    {{-- Header: Title dan Actions --}}
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <h3 class="fw-bold mb-0">Manajemen Gaji Pokok</h3>
            <div class="d-flex gap-2">
                {{-- Filter Button --}}
                <button class="btn btn-theme primary me-2 py-2 px-3" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                </button>
                
                {{-- Generate Salary Button --}}
                <button class="btn btn-info me-2" data-bs-toggle="modal" data-bs-target="#generateModal">
                    <i class="fas fa-calculator"></i> Generate Gaji
                </button>
            </div>
        </div>

        {{-- Ringkasan Cards --}}
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-1">Total Keseluruhan</h6>
                                <h4 class="mb-0">{{ $ringkasan['total_keseluruhan'] ?? 0 }}</h4>
                                <small class="text-white-75">Rp {{ number_format($ringkasan['nominal_keseluruhan'] ?? 0) }}</small>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-chart-bar fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-1">Belum Dibayar</h6>
                                <h4 class="mb-0">{{ $ringkasan['total_belum_dibayar'] ?? 0 }}</h4>
                                <small class="text-white-75">Rp {{ number_format($ringkasan['nominal_belum_dibayar'] ?? 0) }}</small>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-1">Sudah Dibayar</h6>
                                <h4 class="mb-0">{{ $ringkasan['total_sudah_dibayar'] ?? 0 }}</h4>
                                <small class="text-white-75">Rp {{ number_format($ringkasan['nominal_sudah_dibayar'] ?? 0) }}</small>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                    <div class="card-body text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-white-50 mb-1">Dibayar Sebagian</h6>
                                <h4 class="mb-0">{{ $ringkasan['total_dibayar_sebagian'] ?? 0 }}</h4>
                                <small class="text-white-75">Rp {{ number_format($ringkasan['nominal_dibayar_sebagian'] ?? 0) }}</small>
                            </div>
                            <div class="opacity-75">
                                <i class="fas fa-minus-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body border-0 shadow-sm">
                <form method="GET" action="{{ route('admin.gaji-pokok.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="user_id" class="form-label fw-semibold">Karyawan</label>
                            <select name="user_id" id="user_id" class="form-select border-0 shadow-sm">
                                <option value="">Semua Karyawan</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->nama_lengkap }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="periode_awal" class="form-label fw-semibold">Periode Awal</label>
                            <input type="date" name="periode_awal" id="periode_awal" class="form-select border-0 shadow-sm" value="{{ request('periode_awal') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label for="periode_akhir" class="form-label fw-semibold">Periode Akhir</label>
                            <input type="date" name="periode_akhir" id="periode_akhir" class="form-select border-0 shadow-sm" value="{{ request('periode_akhir') }}">
                        </div>
                        
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary shadow-sm">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary shadow-sm">
                                    <i class="fas fa-times"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body p-0 table-responsive" style="min-height: 50vh">
                <table class="table table-striped table-borderless mb-0">
                    <thead style="background-color: #FFD9D9;">
                        <tr>
                            <th class="fw-bold py-3 ps-4">Karyawan</th>
                            <th class="fw-bold py-3">Periode</th>
                            <th class="fw-bold py-3">Jam Kerja</th>
                            <th class="fw-bold py-3">Total Gaji</th>
                            <th class="fw-bold py-3">Potongan</th>
                            <th class="fw-bold py-3">Gaji Bersih</th>
                            <th class="fw-bold py-3">Status</th>
                            <th class="fw-bold py-3">Tanggal Bayar</th>
                            <th class="fw-bold py-3 pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gajiPokok as $item)
                            <tr class="bg-white">
                                <td class="align-middle ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                            {{ strtoupper(substr($item->user->nama_lengkap ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $item->user->nama_lengkap ?? '-' }}</div>
                                            <small class="text-muted">{{ $item->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-semibold">{{ $item->periode_awal->format('d M Y') }}</div>
                                    <small class="text-muted">s/d {{ $item->periode_akhir->format('d M Y') }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ $item->jumlah_jam_kerja }} jam</span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-semibold">Rp {{ number_format($item->total_gaji) }}</div>
                                </td>
                                <td class="align-middle">
                                    <div class="text-danger">Rp {{ number_format($item->total_potongan_terlambat) }}</div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold text-success">Rp {{ number_format($item->total_gaji - $item->total_potongan_terlambat) }}</div>
                                </td>
                                <td class="align-middle">
                                    @if($item->status_pembayaran == 0)
                                        <span class="badge bg-danger">Belum Dibayar</span>
                                    @elseif($item->status_pembayaran == 1)
                                        <span class="badge bg-success">Sudah Dibayar</span>
                                    @else
                                        <span class="badge bg-warning">Dibayar Sebagian</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($item->tgl_bayar)
                                        <div class="fw-semibold">{{ $item->tgl_bayar->format('d M Y') }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="align-middle pe-4">
                                    <div class="dropdown">
                                        <button class="btn btn-theme info dropdown-toggle shadow-sm" type="button" id="actionsDropdown{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-cog"></i>
                                        </button>
                                        <ul class="dropdown-menu shadow" aria-labelledby="actionsDropdown{{ $loop->index }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.gaji-pokok.show', $item->id) }}">
                                                    <i class="fas fa-eye text-info"></i> Lihat Detail
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            @if($item->status_pembayaran == 0)
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="updatePembayaran({{ $item->id }}, 1)">
                                                        <i class="fas fa-check text-success"></i> Tandai Dibayar
                                                    </button>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="updatePembayaran({{ $item->id }}, 2)">
                                                        <i class="fas fa-minus-circle text-warning"></i> Tandai Sebagian
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                            @endif
                                            @if($item->status_pembayaran != 0)
                                                <li>
                                                    <button type="button" class="dropdown-item" onclick="updatePembayaran({{ $item->id }}, 0)">
                                                        <i class="fas fa-undo text-secondary"></i> Reset Status
                                                    </button>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <div class="h5">Belum ada data gaji pokok</div>
                                        <p>Data gaji pokok akan muncul setelah presensi disetujui</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($gajiPokok->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $gajiPokok->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Generate Gaji --}}
<div class="modal fade" id="generateModal" tabindex="-1" aria-labelledby="generateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateModalLabel">Generate Gaji Pokok</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="generateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="generate_user_id" class="form-label">Karyawan</label>
                        <select name="user_id" id="generate_user_id" class="form-select" required>
                            <option value="">Pilih Karyawan</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="generate_periode_awal" class="form-label">Periode Awal</label>
                            <input type="date" name="periode_awal" id="generate_periode_awal" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label for="generate_periode_akhir" class="form-label">Periode Akhir</label>
                            <input type="date" name="periode_akhir" id="generate_periode_akhir" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Update Pembayaran --}}
<div class="modal fade" id="updatePembayaranModal" tabindex="-1" aria-labelledby="updatePembayaranModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updatePembayaranModalLabel">Update Status Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updatePembayaranForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" id="status_pembayaran" class="form-select" required>
                            <option value="0">Belum Dibayar</option>
                            <option value="1">Sudah Dibayar</option>
                            <option value="2">Dibayar Sebagian</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: 600;
}

.card-header-theme {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
}

.btn-theme.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
}

.btn-theme.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    border: none;
    color: white;
}

.dropdown-menu.shadow {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.table-striped > tbody > tr:nth-of-type(odd) > td {
    background-color: rgba(0, 0, 0, 0.02);
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.7) !important;
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.9) !important;
}
</style>
@endpush

@push('scripts')
<script>
function updatePembayaran(id, status) {
    const modal = new bootstrap.Modal(document.getElementById('updatePembayaranModal'));
    const form = document.getElementById('updatePembayaranForm');
    const statusSelect = document.getElementById('status_pembayaran');
    
    // Set form action
    form.action = `/admin/gaji-pokok/${id}/pembayaran`;
    
    // Set status value
    statusSelect.value = status;
    
    // Show modal
    modal.show();
}

// Handle form submission
document.getElementById('updatePembayaranForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const url = this.action;
    
    fetch(url, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memperbarui data');
    });
});

// Handle generate form
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/gaji-pokok/generate', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat generate gaji');
    });
});
</script>
@endpush