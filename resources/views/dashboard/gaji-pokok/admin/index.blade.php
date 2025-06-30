@extends('layouts.app')
@section('title', 'Manajemen Gaji Pokok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        💰 Manajemen Gaji Pokok - Semua Pegawai
                    </h3>
                    <div class="d-flex gap-3">
                        <!-- Filter Button -->
                        <button class="btn btn-theme primary me-2 py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                        </button>
                        {{-- History Button --}}
                        <a href="{{ route('admin.gaji-pokok.generated') }}" class="btn btn-success py-2 px-3">
                            <i class="bi bi-clock-history" style="font-size: 1.2rem"></i> History
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
                        <form method="GET" action="{{ route('admin.gaji-pokok.index') }}">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Mulai</label>
                                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tanggal Selesai</label>
                                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
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
                                    <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary">Reset</a>
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

                <!-- Statistics Cards -->
                @if(count($gajiPokokData) > 0)
                    <div class="row mt-2 mb-4">
                        <div class="col-md-3">
                            <div class="card text-center summary-card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">Total Pegawai</h5>
                                    <h3 class="text-primary">{{ $totalSummary['total_records'] }}</h3>
                                    <small class="text-muted">pegawai aktif</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card">
                                <div class="card-body">
                                    <h5 class="card-title text-success">Total Jam Kerja</h5>
                                    <h3 class="text-success">{{ number_format($totalSummary['total_jam_kerja'], 1) }}</h3>
                                    <small class="text-muted">jam efektif</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card">
                                <div class="card-body">
                                    <h5 class="card-title text-warning">Total Keterlambatan</h5>
                                    <h3 class="text-warning">{{ $totalSummary['total_menit_terlambat'] }}</h3>
                                    <small class="text-muted">menit terlambat</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center summary-card">
                                <div class="card-body">
                                    <h5 class="card-title text-info">Rata-rata Gaji</h5>
                                    <h3 class="text-info">Rp {{ number_format($totalSummary['rata_rata_gaji'], 0, ',', '.') }}</h3>
                                    <small class="text-muted">per pegawai</small>
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
                                <th style="background-color: #ca414e; color: white;">Periode Kerja</th>
                                <th style="background-color: #ca414e; color: white;">Total Jam Kerja</th>
                                <th style="background-color: #ca414e; color: white;">Menit Terlambat</th>
                                <th style="background-color: #ca414e; color: white;">Gaji Kotor</th>
                                <th style="background-color: #ca414e; color: white;">Total Potongan</th>
                                <th style="background-color: #ca414e; color: white;">Gaji Bersih</th>
                                <th style="background-color: #ca414e; color: white;">Status Pembayaran</th>
                                <th style="background-color: #ca414e; color: white;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($gajiPokokData as $index => $gaji)
                            <tr class="{{ isset($gaji->is_realtime) && $gaji->is_realtime ? 'table-warning' : '' }}">
                                <td>{{ $gajiPokokData->firstItem() + $index }}</td>
                                <td>
                                    {{ $gaji->user->nama_lengkap }}
                                    @if(isset($gaji->is_realtime) && $gaji->is_realtime)
                                        <span class="badge bg-info ms-2">Realtime</span>
                                    @endif
                                </td>
                                <td>{{ Carbon\Carbon::parse($gaji->periode_start)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($gaji->periode_end)->format('d/m/Y') }}</td>
                                <td>{{ number_format($gaji->jumlah_jam_kerja, 2) }} jam</td>
                                <td>
                                    @if($gaji->total_menit_terlambat > 0)
                                        <span class="text-danger">{{ $gaji->total_menit_terlambat }} menit</span>
                                    @else
                                        <span class="text-success">0 menit</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($gaji->gaji_kotor, 0, ',', '.') }}</td>
                                <td>
                                    @if($gaji->total_potongan > 0)
                                        <span class="text-danger">Rp {{ number_format($gaji->total_potongan, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-success">Rp 0</span>
                                    @endif
                                </td>
                                <td><strong class="text-success">Rp {{ number_format($gaji->total_gaji_pokok, 0, ',', '.') }}</strong></td>
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
                                            <small class="d-block text-muted">
                                                {{ Carbon\Carbon::parse($gaji->tgl_bayar)->format('d/m/Y') }}
                                            </small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if(isset($gaji->is_realtime) && $gaji->is_realtime)
                                            {{-- Tombol untuk data realtime --}}
                                            <a href="{{ route('admin.gaji-pokok.detail-realtime') }}?user_id={{ $gaji->users_id }}&start_date={{ $gaji->periode_start }}&end_date={{ $gaji->periode_end }}" 
                                            class="btn btn-sm btn-info" 
                                            title="Detail Gaji Realtime">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-success" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#generateRealtimeModal"
                                                    data-user-id="{{ $gaji->users_id }}"
                                                    data-user-name="{{ $gaji->user->nama_lengkap }}"
                                                    data-start-date="{{ $gaji->periode_start }}"
                                                    data-end-date="{{ $gaji->periode_end }}"
                                                    title="Generate & Simpan Gaji">
                                                <i class="bi bi-save"></i>
                                            </button>
                                        @else
                                            {{-- Tombol untuk data yang sudah tersimpan --}}
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
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="py-4">
                                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                        <p class="mt-2 text-muted">Tidak ada data gaji pokok untuk periode yang dipilih</p>
                                        <small class="text-muted">Silakan ubah filter periode atau pastikan ada data presensi yang disetujui</small>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                        @if(count($gajiPokokData) > 0)
                            <tfoot class="table-light">
                                <tr style="font-weight: bold;">
                                    <td colspan="2">TOTAL (Semua Data)</td>
                                    <td>{{ $totalSummary['total_records'] }} record</td>
                                    <td>{{ number_format($totalSummary['total_jam_kerja'], 2) }} jam</td>
                                    <td>{{ $totalSummary['total_menit_terlambat'] }} menit</td>
                                    <td>Rp {{ number_format($totalSummary['total_gaji_kotor'], 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($totalSummary['total_potongan'], 0, ',', '.') }}</td>
                                    <td><strong class="text-success">Rp {{ number_format($totalSummary['total_gaji_bersih'], 0, ',', '.') }}</strong></td>
                                    <td>-</td>
                                    <td>-</td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $gajiPokokData->links() }}
                </div>
                                <!-- Export/Action Buttons -->
                                @if(count($gajiPokokData) > 0)
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button onclick="printTable()" class="btn btn-outline-primary">
                                                    <i class="bi bi-printer"></i> Print
                                                </button>
                                                <button onclick="exportToCSV()" class="btn btn-outline-success">
                                                    <i class="bi bi-file-earmark-excel"></i> Export CSV
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
            </div>
        </div>
    </div>
</div>

{{-- Modal untuk Generate Realtime Data --}}
{{-- Modal untuk Generate Realtime Data --}}
<div class="modal fade" id="generateRealtimeModal" tabindex="-1" aria-labelledby="generateRealtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="generateRealtimeModalLabel">
                    <i class="bi bi-save"></i> Generate & Simpan Gaji
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.gaji-pokok.generate-from-realtime') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="generate_user_id">
                    <input type="hidden" name="start_date" id="generate_start_date">
                    <input type="hidden" name="end_date" id="generate_end_date">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Pegawai:</strong></label>
                        <p id="generate_user_name" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Periode:</strong></label>
                        <p id="generate_period" class="form-control-plaintext"></p>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        Data gaji ini akan disimpan ke database dan dapat dikelola statusnya (dibayar/belum dibayar).
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-save"></i> Generate & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal untuk Preview Realtime Data --}}
<div class="modal fade" id="previewRealtimeModal" tabindex="-1" aria-labelledby="previewRealtimeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewRealtimeModalLabel">
                    <i class="bi bi-eye"></i> Preview Detail Gaji Realtime
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informasi Pegawai</h6>
                        <p><strong>Nama:</strong> <span id="preview_user_name"></span></p>
                        <p><strong>Periode:</strong> <span id="preview_period"></span></p>
                    </div>
                    <div class="col-md-6">
                        <h6>Detail Perhitungan</h6>
                        <p><strong>Total Jam Kerja:</strong> <span id="preview_jam_kerja"></span> jam</p>
                        <p><strong>Menit Terlambat:</strong> <span id="preview_terlambat"></span> menit</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-title text-primary">Gaji Kotor</h6>
                                <h4 class="text-primary" id="preview_gaji_kotor">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-title text-danger">Total Potongan</h6>
                                <h4 class="text-danger" id="preview_potongan">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6 class="card-title text-success">Gaji Bersih</h6>
                                <h4 class="text-success" id="preview_gaji_bersih">Rp 0</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Catatan:</strong> Data ini dihitung secara realtime. Untuk menyimpan dan mengelola status pembayaran, klik tombol "Generate & Simpan".
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Modal generate realtime
        const generateRealtimeModal = document.getElementById('generateRealtimeModal');
        generateRealtimeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const userName = button.getAttribute('data-user-name');
            const startDate = button.getAttribute('data-start-date');
            const endDate = button.getAttribute('data-end-date');

            document.getElementById('generate_user_id').value = userId;
            document.getElementById('generate_start_date').value = startDate;
            document.getElementById('generate_end_date').value = endDate;
            document.getElementById('generate_user_name').textContent = userName;
            document.getElementById('generate_period').textContent = `${startDate} s/d ${endDate}`;
        });

        // Modal preview realtime
        const previewRealtimeModal = document.getElementById('previewRealtimeModal');
        previewRealtimeModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userName = button.getAttribute('data-user-name');
            const gajiData = JSON.parse(button.getAttribute('data-gaji-data'));

            document.getElementById('preview_user_name').textContent = userName;
            document.getElementById('preview_period').textContent = `${gajiData.periode_start} s/d ${gajiData.periode_end}`;
            document.getElementById('preview_jam_kerja').textContent = parseFloat(gajiData.jumlah_jam_kerja).toFixed(2);
            document.getElementById('preview_terlambat').textContent = gajiData.total_menit_terlambat;
            document.getElementById('preview_gaji_kotor').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(gajiData.gaji_kotor)}`;
            document.getElementById('preview_potongan').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(gajiData.total_potongan)}`;
            document.getElementById('preview_gaji_bersih').textContent = `Rp ${new Intl.NumberFormat('id-ID').format(gajiData.total_gaji_pokok)}`;
        });

        // Auto hide filter collapse after form submission
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('start_date') || urlParams.has('end_date') || urlParams.has('user_id')) {
            const filterCollapse = document.getElementById('filterCollapse');
            if (filterCollapse) {
                filterCollapse.classList.remove('show');
            }
        }

        // Date validation
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

        // Modal update status pembayaran
        const updateStatusModal = document.getElementById('updateStatusModal');
        updateStatusModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const userName = button.getAttribute('data-user-name');
            const currentStatus = button.getAttribute('data-current-status');
            const currentDate = button.getAttribute('data-current-date');
        const gajiId = button.getAttribute('data-gaji-id');
        const periodeStart = button.getAttribute('data-periode-start');
        const periodeEnd = button.getAttribute('data-periode-end');

document.getElementById('modal_gaji_id').value = gajiId;
        document.getElementById('modal_user_name').textContent = userName;
        document.getElementById('modal_period').textContent = `${periodeStart} s/d ${periodeEnd}`;
            document.getElementById('status_pembayaran').value = currentStatus || '0';
            document.getElementById('tgl_bayar').value = currentDate || '';
        });

        // Show/hide tanggal bayar based on status
        const statusSelect = document.getElementById('status_pembayaran');
        const tanggalBayarGroup = document.getElementById('tanggal_bayar_group');
        const tanggalBayarInput = document.getElementById('tgl_bayar');

        function toggleTanggalBayar() {
            if (statusSelect.value === '0') {
                tanggalBayarGroup.style.display = 'none';
                tanggalBayarInput.value = '';
                tanggalBayarInput.required = false;
            } else {
                tanggalBayarGroup.style.display = 'block';
                if (statusSelect.value === '1' && !tanggalBayarInput.value) {
                    tanggalBayarInput.value = new Date().toISOString().split('T')[0];
                }
                tanggalBayarInput.required = statusSelect.value === '1';
            }
        }

        statusSelect.addEventListener('change', toggleTanggalBayar);
        toggleTanggalBayar(); // Initial call
    });

    // Print table function
    function printTable() {
        const printWindow = window.open('', '_blank');
        const table = document.querySelector('.table-responsive').innerHTML;
        const period = document.querySelector('.alert-info').innerHTML;
        
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Gaji Pokok</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        .btn-group { display: none !important; }
                        .table { font-size: 12px; }
                    }
                    body { font-family: Arial, sans-serif; }
                </style>
            </head>
            <body>
                <div class="container-fluid p-3">
                    <h3 class="text-center mb-3">Laporan Gaji Pokok Pegawai</h3>
                    <div class="alert alert-info">${period}</div>
                    ${table}
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

    // Export to CSV function
    function exportToCSV() {
        const table = document.querySelector('.table');
        const rows = Array.from(table.querySelectorAll('tr'));
        
        let csvContent = '';
        
        rows.forEach(row => {
            const cols = Array.from(row.querySelectorAll('th, td'));
            const rowData = cols.map(col => {
                // Clean up the text content
                let text = col.textContent.trim();
                // Remove extra whitespace and newlines
                text = text.replace(/\s+/g, ' ');
                // Remove currency formatting for numbers
                if (text.includes('Rp ')) {
                    text = text.replace('Rp ', '').replace(/\./g, '');
                }
                return `"${text}"`;
            });
            
            // Skip empty rows and action column
            if (rowData.length > 1 && !rowData[0].includes('bi-')) {
                csvContent += rowData.slice(0, -1).join(',') + '\n'; // Remove last column (actions)
            }
        });
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', `gaji_pokok_{{ $startDate->format('Y-m-d') }}_{{ $endDate->format('Y-m-d') }}.csv`);
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endpush


@endsection