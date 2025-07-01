@extends('layouts.app')
@section('title', 'Detail Gaji Lembur - ' . $pegawai->nama_lengkap)
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
            <div>
                <h3 class="fw-bold mb-0">💰 Detail Gaji Lembur</h3>
                <p class="text-white mb-0">{{ $pegawai->nama_lengkap }} - {{ $pegawai->jabatan ?? 'Tidak ada jabatan' }}</p>
                <small class="text-white">Periode: {{ $tanggalMulai ? date('d/m/Y', strtotime($tanggalMulai)) : 'Semua' }} - {{ $tanggalSelesai ? date('d/m/Y', strtotime($tanggalSelesai)) : 'Semua' }}</small>
            </div>
            <div class="d-flex gap-2">
                {{-- Filter Button --}}
                <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                </button>
                
                {{-- Print Button --}}
                <button class="btn btn-success p-2 px-md-3" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
                
                {{-- Back Button --}}
                <a href="{{ route('gaji-lembur.laporan') }}" class="btn btn-theme secondary p-2 px-md-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <form method="GET">
                    <input type="hidden" name="user_id" value="{{ $pegawai->id }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai') }}">
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
                            <button type="submit" class="btn btn-theme info me-2 p-2 px-3">Filter</button>
                            <a href="{{ route('gaji-lembur.detail-pegawai', $pegawai->id) }}" class="btn btn-theme primary p-2 px-3" style="border-color: #B50000;">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Hari Lembur</h5>
                        <h3>{{ $statistik->total_hari ?? 0 }}</h3>
                        <small>hari</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Jam Lembur</h5>
                        <h3>{{ number_format($statistik->total_jam ?? 0, 1) }}</h3>
                        <small>jam</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Sudah Dibayar</h5>
                        <h3>Rp {{ number_format($statistik->total_sudah_dibayar ?? 0, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5>Belum Dibayar</h5>
                        <h3>Rp {{ number_format($statistik->total_belum_dibayar ?? 0, 0, ',', '.') }}</h3>
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
                    </select>
                    <button type="submit" class="btn btn-sm btn-theme info p-2 px-md-3">Terapkan</button>
                </div>
        </div>

        {{-- Table --}}
        <div class="card rounded-2xl border-0 shadow-sm rounded-3">
            <div class="card-body p-0 table-responsive rounded-3" style="min-height: 50vh">
                <table class="table table-striped table-borderless table-hover mb-0 rounded-3">
                    <thead>
                        <tr >
                            <th style="background-color: #ca414e; color: white;"><input type="checkbox" id="masterCheckbox"></th>
                            <th style="background-color: #ca414e; color: white;">Tanggal</th>
                            <th style="background-color: #ca414e; color: white;">Shift</th>
                            <th style="background-color: #ca414e; color: white;">Tipe Lembur</th>
                            <th style="background-color: #ca414e; color: white; min-width: 120px;">Jam Aktual</th>
                            <th style="background-color: #ca414e; color: white; min-width: 120px">Total Jam Dihitung</th>
                            <th style="background-color: #ca414e; color: white; min-width: 120px">Rate/Jam</th>
                            <th style="background-color: #ca414e; color: white; min-width: 120px">Total Gaji</th>
                            <th style="background-color: #ca414e; color: white;">Status Presensi</th>
                            <th style="background-color: #ca414e; color: white;">Status Bayar</th>
                            <th style="background-color: #ca414e; color: white;">Tgl Bayar</th>
                            <th style="background-color: #ca414e; color: white;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gajiLembur as $item)
                            <tr class="bg-white">
                                <td class="align-middle">
                                    <input type="checkbox" name="gaji_lembur_ids[]" value="{{ $item->id }}" class="batch-checkbox">
                                </td>
                                <td class="align-middle">{{ $item->tgl_lembur->format('d/m/Y') }}</td>
                                <td class="align-middle">
                                    @if($item->presensi && $item->presensi->jadwalShift && $item->presensi->jadwalShift->shift)
                                        <span class="badge bg-secondary">{{ $item->presensi->jadwalShift->shift->nama_shift }}</span>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="align-middle">
                                    <span class="badge {{ $item->tipe_lembur_badge }}">{{ $item->tipe_lembur_label }}</span>
                                </td>

                                <td class="align-middle">
                                    @if($item->presensi)
                                        {{ $item->presensi->jam_masuk ? \Carbon\Carbon::parse($item->presensi->jam_masuk)->format('H:i') : 'N/A' }} - 
                                        {{ $item->presensi->jam_keluar ? \Carbon\Carbon::parse($item->presensi->jam_keluar)->format('H:i') : 'N/A' }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="align-middle">{{ $item->formatted_total_jam_lembur }}</td>
                                <td class="align-middle">{{ $item->formatted_rate_lembur_per_jam }}</td>
                                <td class="align-middle">
                                    <strong>{{ $item->formatted_total_gaji_lembur }}</strong>
                                </td>
                                {{-- STATUS PRESENSI --}}
                                <td class="align-middle">
                                    @if($item->presensi)
                                        <span class="badge 
                                            @if($item->presensi->status_approval == 1) bg-success
                                            @elseif($item->presensi->status_approval == 2) bg-danger
                                            @else bg-warning @endif">
                                            {{ $item->presensi->status_approval_label }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- STATUS BAYAR --}}
                                <td class="align-middle">
                                    <span class="badge {{ $item->status_pembayaran_badge }}">
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
                                                    <i class="fas fa-eye"></i> Lihat Detail
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
                                <td colspan="11" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p>Belum ada data gaji lembur untuk pegawai ini pada periode yang dipilih.</p>
                                        @if(request('tanggal_mulai') || request('tanggal_selesai') || request('status_pembayaran'))
                                            <a href="{{ route('gaji-lembur.detail-pegawai', $pegawai->id) }}" class="btn btn-theme primary btn-sm">
                                                Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                    @if($gajiLembur->count() > 0)
                    <tfoot style="background-color:#F8F9FA">
                        <tr class="fw-bold">
                            <td colspan="5" class="text-end">TOTAL:</td>
                            <td>{{ number_format($statistik->total_jam ?? 0, 1) }} jam</td>
                            <td>—</td>
                            <td>Rp {{ number_format(($statistik->total_sudah_dibayar ?? 0) + ($statistik->total_belum_dibayar ?? 0), 0, ',', '.') }}</td>
                            <td colspan="4">—</td>
                        </tr>
                    </tfoot>
                    @endif
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

        {{-- Summary Info --}}
        <div class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Informasi Pegawai</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="120">Nama Lengkap</td>
                                    <td>: {{ $pegawai->nama_lengkap }}</td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>: {{ $pegawai->jabatan ?? 'Belum ditentukan' }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>: {{ $pegawai->email ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td>No. HP</td>
                                    <td>: {{ $pegawai->no_hp ?? '—' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title">Ringkasan Pembayaran</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td width="140">Total Keseluruhan</td>
                                    <td>: Rp {{ number_format(($statistik->total_sudah_dibayar ?? 0) + ($statistik->total_belum_dibayar ?? 0), 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-success">
                                    <td>Sudah Dibayar</td>
                                    <td>: Rp {{ number_format($statistik->total_sudah_dibayar ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr class="text-danger">
                                    <td>Belum Dibayar</td>
                                    <td>: Rp {{ number_format($statistik->total_belum_dibayar ?? 0, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Persentase Bayar</td>
                                    <td>: 
                                        @php
                                            $total = ($statistik->total_sudah_dibayar ?? 0) + ($statistik->total_belum_dibayar ?? 0);
                                            $persentase = $total > 0 ? (($statistik->total_sudah_dibayar ?? 0) / $total) * 100 : 0;
                                        @endphp
                                        <span class="badge {{ $persentase >= 100 ? 'bg-success' : ($persentase >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($persentase, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        </select>
                    </div>
                    <div class="mb-3" id="tglBayarDiv">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date" name="tgl_bayar" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3" id="keteranganDiv">
                        <label class="form-label">Keterangan (Opsional)</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan pembayaran..."></textarea>
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
    const keteranganDiv = document.getElementById('keteranganDiv');
    
    function toggleFields() {
        if (statusSelect.value === '1') {
            tglBayarDiv.style.display = 'block';
            keteranganDiv.style.display = 'block';
        } else if (statusSelect.value === '2') {
            tglBayarDiv.style.display = 'none';
            keteranganDiv.style.display = 'block';
        } else {
            tglBayarDiv.style.display = 'none';
            keteranganDiv.style.display = 'block';
        }
    }
    
    statusSelect.addEventListener('change', toggleFields);
    toggleFields();
    
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

// Auto submit filter when date changes
document.querySelector('input[name="tanggal_mulai"]').addEventListener('change', function() {
    if (this.value && document.querySelector('input[name="tanggal_selesai"]').value) {
        this.form.submit();
    }
});

document.querySelector('input[name="tanggal_selesai"]').addEventListener('change', function() {
    if (this.value && document.querySelector('input[name="tanggal_mulai"]').value) {
        this.form.submit();
    }
});
</script>


@endsection