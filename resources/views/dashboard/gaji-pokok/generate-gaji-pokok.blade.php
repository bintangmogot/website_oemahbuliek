@extends('layouts.app')

@section('title', 'Generate Gaji Pokok - Admin')

@section('content')
<x-session-status/>

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <h3 class="fw-bold mb-0">Generate Gaji Pokok</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>

        <div class="row">
            {{-- Generate Individual --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> Generate Gaji Individual</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Generate gaji pokok untuk karyawan tertentu berdasarkan periode yang dipilih.</p>
                        
                        <form action="{{ route('admin.gaji-pokok.generate') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Pilih Karyawan</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">-- Pilih Karyawan --</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->nama_lengkap }} - {{ $user->email }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="periode_awal" class="form-label">Periode Awal</label>
                                <input type="date" name="periode_awal" id="periode_awal" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="periode_akhir" class="form-label">Periode Akhir</label>
                                <input type="date" name="periode_akhir" id="periode_akhir" class="form-control" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-calculator"></i> Generate Gaji Individual
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Bulk Generate --}}
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Generate Gaji Semua Karyawan</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Generate gaji pokok untuk semua karyawan sekaligus berdasarkan periode yang dipilih.</p>
                        
                        <form action="{{ route('admin.gaji-pokok.bulk-generate') }}" method="POST" onsubmit="return confirm('Yakin ingin generate gaji untuk semua karyawan? Proses ini tidak dapat dibatalkan.')">
                            @csrf
                            <div class="mb-3">
                                <label for="bulk_periode_awal" class="form-label">Periode Awal</label>
                                <input type="date" name="periode_awal" id="bulk_periode_awal" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bulk_periode_akhir" class="form-label">Periode Akhir</label>
                                <input type="date" name="periode_akhir" id="bulk_periode_akhir" class="form-control" required>
                            </div>
                            
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Peringatan:</strong> Proses ini akan generate gaji untuk semua karyawan yang memiliki presensi yang sudah disetujui dalam periode tersebut.
                            </div>
                            
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-users-cog"></i> Generate Gaji Semua Karyawan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Panduan --}}
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Panduan Generate Gaji</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Pastikan presensi karyawan sudah disetujui (approved) sebelum generate gaji</li>
                            <li>Sistem akan menghitung jam kerja berdasarkan presensi yang sudah disetujui</li>
                            <li>Potongan keterlambatan akan dihitung otomatis jika ada</li>
                            <li>Jika sudah ada gaji untuk periode yang sama, generate akan gagal</li>
                            <li>Rate gaji per jam diambil dari jadwal kerja karyawan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto set periode akhir ketika periode awal dipilih
document.getElementById('periode_awal').addEventListener('change', function() {
    const periodeAkhir = document.getElementById('periode_akhir');
    if (!periodeAkhir.value) {
        periodeAkhir.value = this.value;
    }
});

document.getElementById('bulk_periode_awal').addEventListener('change', function() {
    const periodeAkhir = document.getElementById('bulk_periode_akhir');
    if (!periodeAkhir.value) {
        periodeAkhir.value = this.value;
    }
});
</script>
@endsection