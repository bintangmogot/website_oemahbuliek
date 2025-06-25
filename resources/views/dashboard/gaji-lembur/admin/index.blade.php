@extends('layouts.app')
@section('title', 'Gaji Lembur')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
            <h3 class="fw-bold mb-0">💰 Gaji Lembur</h3>
            <div class="d-flex gap-2">
                {{-- Filter Button --}}
                <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                </button>
                
                {{-- Laporan Button --}}
                <a href="{{ route('gaji-lembur.laporan') }}" class="btn btn-success">
                    <i class="fas fa-chart-bar"></i> Laporan
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Status Pembayaran</label>
                            <select name="status_pembayaran" class="form-select">
                                <option value="">Semua Status</option>
                                <option value="0" {{ request('status_pembayaran') == '0' ? 'selected' : '' }}>Belum Dibayar</option>
                                <option value="1" {{ request('status_pembayaran') == '1' ? 'selected' : '' }}>Sudah Dibayar</option>
                                <option value="2" {{ request('status_pembayaran') == '2' ? 'selected' : '' }}>Dibayar Sebagian</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Pegawai</label>
                            <select name="user_id" class="form-select">
                                <option value="">Semua Pegawai</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                <option value="">Pilih Bulan</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                <option value="">Pilih Tahun</option>
                                @for($year = Carbon\Carbon::now()->year; $year >= Carbon\Carbon::now()->year - 2; $year--)
                                    <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme info me-2 p-2 px-3">Filter</button>
                            <a href="{{ route('gaji-lembur.index') }}" class="btn btn-theme primary p-2 px-3" style=" border-color: #B50000;">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Belum Dibayar</h5>
                        <h3>Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</h3>
                        <small>{{ $countUnpaid }} transaksi</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Sudah Dibayar</h5>
                        <h3>Rp {{ number_format($totalPaid, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Keseluruhan</h5>
                        <h3>Rp {{ number_format($totalPaid + $totalUnpaid, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Batch Actions --}}
        <div class="mb-3">
            <form id="batchForm" action="{{ route('gaji-lembur.batch-payment') }}" method="POST">
                @csrf
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" id="selectAll" class="btn btn-sm btn-theme primary p-2 px-md-3 border border-1 border-danger">Pilih Semua</button>
                    <select name="status_pembayaran" class="form-select form-select-sm" style="width: auto;">
                        <option value="">Ubah Status</option>
                        <option value="0">Belum Dibayar</option>
                        <option value="1">Sudah Dibayar</option>
                        <option value="2">Dibayar Sebagian</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-theme info p-2 px-md-3">Terapkan</button>
                </div>
        </div>

        {{-- Table --}}
        <div class="card rounded-2xl border-0 shadow-sm rounded-3">
            <div class="card-body p-0 table-responsive rounded-3" style="min-height: 50vh">
                <table class="table table-striped table-borderless mb-0 rounded-3">
                    <thead style="background-color:#FFD9D9">
                        <tr>
                            <th><input type="checkbox" id="masterCheckbox"></th>
                            <th>Pegawai</th>
                            <th>Tanggal Lembur</th>
                            <th>Total Jam</th>
                            <th>Rate/Jam</th>
                            <th>Total Gaji</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gajiLembur as $item)
                            <tr class="bg-white">
                                <td class="align-middle">
                                    <input type="checkbox" name="gaji_lembur_ids[]" value="{{ $item->id }}" class="batch-checkbox">
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <strong>{{ $item->user->nama_lengkap }}</strong>
                                        @if($item->user->jabatan)
                                            <br><small class="text-muted">{{ $item->user->jabatan }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">{{ $item->tgl_lembur->format('d/m/Y') }}</td>
                                <td class="align-middle">{{ $item->formatted_total_jam_lembur }}</td>
                                <td class="align-middle">{{ $item->formatted_rate_lembur_per_jam }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->formatted_total_gaji_lembur }}</strong>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info {{ $item->status_pembayaran_badge }}">
                                        {{ $item->status_pembayaran_label }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    {{ $item->tgl_bayar ? $item->tgl_bayar->format('d/m/Y') : '—' }}
                                </td>
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-theme info dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu custom">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('gaji-lembur.show', $item->id) }}">
                                                    <i class="fas fa-eye"></i> Lihat
                                                </a>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" 
                                                    onclick="updatePayment({{ $item->id }}, '{{ $item->status_pembayaran }}')"
                                                    data-update-route="{{ route('gaji-lembur.update-payment', $item->id) }}">
                                                    <i class="fas fa-money-bill"></i> Update Pembayaran
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">Belum ada data gaji lembur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
     </form>

        {{-- Pagination --}}
        @if($gajiLembur->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $gajiLembur->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Modal Update Payment --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status Pembayaran</label>
                        <select name="status_pembayaran" id="statusPembayaranSelect" class="form-select" required>
                            <option value="0">Belum Dibayar</option>
                            <option value="1">Sudah Dibayar</option>
                            <option value="2">Dibayar Sebagian</option>
                        </select>
                    </div>
                    <div class="mb-3" id="tglBayarDiv">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-theme primary p-2 px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-theme info p-2 px-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Checkbox functionality
document.getElementById('masterCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.batch-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

document.getElementById('selectAll').addEventListener('click', function() {
    const checkboxes = document.querySelectorAll('.batch-checkbox');
    checkboxes.forEach(cb => cb.checked = true);
    document.getElementById('masterCheckbox').checked = true;
});

// Batch form submission
document.getElementById('batchForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.batch-checkbox:checked');
    const statusSelect = this.querySelector('select[name="status_pembayaran"]');
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Pilih minimal satu item untuk diproses');
        return;
    }
    
    if (!statusSelect.value) {
        e.preventDefault();
        alert('Pilih status pembayaran');
        return;
    }
    
    if (!confirm(`Ubah status pembayaran untuk ${checkedBoxes.length} item?`)) {
        e.preventDefault();
    }
});

// Update payment modal
function updatePayment(id, currentStatus) {
    // Ambil route dari data attribute (lebih aman)
    const updateRoute = event.target.getAttribute('data-update-route');
    document.getElementById('paymentForm').action = updateRoute;
    document.getElementById('statusPembayaranSelect').value = currentStatus;
    
    // Show/hide tanggal bayar based on status
    const statusSelect = document.getElementById('statusPembayaranSelect');
    const tglBayarDiv = document.getElementById('tglBayarDiv');
    
    function toggleTglBayar() {
        if (statusSelect.value === '1') {
            tglBayarDiv.style.display = 'block';
        } else {
            tglBayarDiv.style.display = 'none';
        }
    }
    
    statusSelect.addEventListener('change', toggleTglBayar);
    toggleTglBayar();
    
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}
</script>

@endsection