@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-theme" style="background-color: #ffffff">
                <div class="card-header-theme ps-lg-5  pt-md-4">
                    <h3 class="card-title">
                        <i class="fas fa-clock me-2"></i>
                        Pilih Shift untuk Melihat Detail
                    </h3>
                
                </div>
                <hr>
                <div class="card-body p-2 p-sm-3 p-md-4 p-lg-5">
                    @if($shifts->count() > 0)
                        <div class="row">
                            @foreach($shifts as $shift)
                            <div class="col-md-6 mb-4">
                                <div class="card-theme shadow-md ">
                                    <div class="card-body">
                                        <h5 class="card-title text-primary">
                                            <i class="fas fa-business-time me-2"></i>
                                            {{ $shift->nama_shift }}
                                        </h5>
                                        <div class="mb-3">
                                            <div class="text-muted">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Waktu:</strong> 
                                                {{ $shift->jam_mulai ? \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') : '—' }}
                                                –
                                                {{ $shift->jam_selesai ? \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') : '—' }}
                                            </div>
                                            <div class="text-muted mt-1">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Toleransi:</strong> {{ $shift->toleransi_terlambat }} menit
                                            </div>
                                        </div>
                                        
                                        {{-- Form untuk memilih tanggal --}}
                                        <form action="{{ route('jadwal-shift.detail', ['shift_id' => $shift->id, 'tanggal' => 'TANGGAL_PLACEHOLDER']) }}" 
                                              method="GET" 
                                              class="shift-form">
                                            <div class="mb-3">
                                                <label for="tanggal_{{ $shift->id }}" class="form-label">Pilih Tanggal:</label>
                                                <input type="date" 
                                                       class="form-control" 
                                                       id="tanggal_{{ $shift->id }}" 
                                                       name="tanggal" 
                                                       value="{{ date('Y-m-d') }}" 
                                                       required>
                                            </div>
                                            <button type="submit" class="btn btn-theme primary w-100">
                                                <i class="fas fa-eye me-2"></i>
                                                Lihat Detail Shift
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Tidak ada shift yang tersedia.</p>
                        </div>
                    @endif
                </div>
                <hr class="m-2">
                <div class="card-footer p-2 pb-md-3 ps-sm-3 ps-md-4 ps-lg-5">
                    <a href="{{ route('jadwal-shift.index') }}" class="btn btn-theme info">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle form submission untuk mengganti placeholder dengan tanggal yang dipilih
    const forms = document.querySelectorAll('.shift-form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const tanggal = this.querySelector('input[name="tanggal"]').value;
            const currentAction = this.getAttribute('action');
            const newAction = currentAction.replace('TANGGAL_PLACEHOLDER', tanggal);
            
            // Redirect ke URL yang benar
            window.location.href = newAction;
        });
    });
});
</script>
@endsection