@extends('layouts.app')
@section('title', 'Detail Gaji Lembur')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <div>
                <h3 class="fw-bold mb-0">📋 Detail Gaji Lembur</h3>
                <p class="text-white ps-1 mb-0">{{ $gajiLembur->user->nama_lengkap }} - {{ $gajiLembur->tgl_lembur->format('d F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url()->previous() }}" class="btn btn-theme secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                @if(Auth::user()->role === 'admin')
                    <button class="btn btn-theme primary" 
                        onclick="updatePayment({{ $gajiLembur->id }}, '{{ $gajiLembur->status_pembayaran }}')"
                        data-update-route="{{ route('gaji-lembur.update-payment', $gajiLembur->id) }}">                        
                    <i class="fas fa-money-bill"></i> Update Pembayaran
                    </button>
                @endif
            </div>
        </div>

        <div class="row">
            {{-- Status Card --}}
            <div class="col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm justify-content-center align-items-center">
                    <div class="card-body">
                        <h5 class="card-title">Status Pembayaran</h5>
                        <div class="text-center py-3 row align-items-center ">
                            <span class="badge bg-info fs-6 {{ $gajiLembur->status_pembayaran_badge }} px-4 py-3">
                                {{ $gajiLembur->status_pembayaran_label }}
                            </span>
                        </div>
                        @if($gajiLembur->tgl_bayar)
                            <div class="text-center">
                                <small class="text-muted">Dibayar pada:</small>
                                <br>
                                <strong>{{ $gajiLembur->tgl_bayar->format('d F Y') }}</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Detail Information --}}
            <div class="col-lg-8 mb-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Informasi Detail</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Pegawai:</td>
                                        <td>{{ $gajiLembur->user->nama_lengkap }}</td>
                                    </tr>
                                    @if($gajiLembur->user->jabatan)
                                    <tr>
                                        <td class="fw-bold">Jabatan:</td>
                                        <td>{{ $gajiLembur->user->jabatan }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td class="fw-bold">Tanggal Lembur:</td>
                                        <td>{{ $gajiLembur->tgl_lembur->format('d F Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Total Jam Lembur:</td>
                                        <td>{{ $gajiLembur->formatted_total_jam_lembur }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td class="fw-bold">Rate per Jam:</td>
                                        <td>{{ $gajiLembur->formatted_rate_lembur_per_jam }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Total Gaji Lembur:</td>
                                        <td class="fs-5 fw-bold text-success">{{ $gajiLembur->formatted_total_gaji_lembur }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Dibuat pada:</td>
                                        <td>{{ $gajiLembur->created_at->format('d F Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="fw-bold">Terakhir diupdate:</td>
                                        <td>{{ $gajiLembur->updated_at->format('d F Y H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Presensi Information --}}
        @if($gajiLembur->presensi)
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Informasi Presensi Terkait</h5>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold" >Tanggal Presensi:</td>
                            <td>
                                {{ old('tanggal', optional($gajiLembur->presensi)->tgl_presensi ? optional($gajiLembur->presensi->tgl_presensi)->format('d F Y') : '') }}
                            </td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jam Masuk:</td>
                                <td>{{ $gajiLembur->presensi->jam_masuk ? $gajiLembur->presensi->jam_masuk->format('H:i') : '—' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jam Keluar:</td>
                                <td>{{ $gajiLembur->presensi->jam_keluar ? $gajiLembur->presensi->jam_keluar->format('H:i') : '—' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        @if($gajiLembur->presensi->jadwalShift)
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">Shift:</td>
                                <td>{{ $gajiLembur->presensi->jadwalShift->shift->nama_shift ?? '—' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">Jam Shift:</td>
                                <td>
                                    @if($gajiLembur->presensi->jadwalShift->shift)
                                        {{ $gajiLembur->presensi->jadwalShift->shift->jam_mulai }} - {{ $gajiLembur->presensi->jadwalShift->shift->jam_selesai }}
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Admin Actions --}}
        @if(Auth::user()->role === 'admin')
        <div class="row mt-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Tindakan Admin</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-theme primary" 
                                onclick="updatePayment({{ $gajiLembur->id }}, '{{ $gajiLembur->status_pembayaran }}')"
                                data-update-route="{{ route('gaji-lembur.update-payment', $gajiLembur->id) }}">
                            <i class="fas fa-money-bill"></i> Update Status Pembayaran
                            </button>

                        </div>
                    </div>
                </div>
            </div>
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
// Update payment modal
function updatePayment(id, currentStatus) {
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