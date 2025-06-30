@extends('layouts.app')
@section('title', 'Generate Gaji Pokok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <!-- Header -->
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        ⚙️ Generate Gaji Pokok
                    </h3>
                    <div class="d-flex gap-3">
                        <a href="{{ route('admin.gaji-pokok.index') }}" class="btn btn-secondary py-2 px-3">
                            <i class="bi bi-arrow-left" style="font-size: 1.2rem"></i> Kembali
                        </a>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        @if(session('generated_count'))
                            <br><small>{{ session('generated_count') }} gaji pokok berhasil digenerate.</small>
                        @endif
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->generation_errors->any())
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Beberapa Error Ditemukan:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->generation_errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Form Generate -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Form Generate Gaji Pokok</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.gaji-pokok.generate') }}" id="generateForm">
                                    @csrf
                                    
                                    <!-- Periode Bulan -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Periode Bulan <span class="text-danger">*</span></label>
                                            <input type="month" class="form-control @error('periode_bulan') is-invalid @enderror" 
                                                   name="periode_bulan" value="{{ old('periode_bulan') }}" required>
                                            @error('periode_bulan')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Pilih periode bulan untuk generate gaji pokok</small>
                                        </div>
                                    </div>

                                    <!-- Pilih Karyawan -->
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <label class="form-label">Pilih Karyawan</label>
                                            <div class="card">
                                                <div class="card-body">
                                                    <!-- Checkbox All -->
                                                    <div class="form-check mb-3">
                                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                                        <label class="form-check-label fw-bold" for="selectAll">
                                                            Pilih Semua Karyawan
                                                        </label>
                                                    </div>
                                                    <hr>
                                                    
                                                    <!-- List Karyawan -->
                                                    <div class="row">
                                                        @forelse($users as $user)
                                                            <div class="col-md-6 col-lg-4 mb-2">
                                                                <div class="form-check">
                                                                    <input class="form-check-input user-checkbox" type="checkbox" 
                                                                           name="user_ids[]" value="{{ $user->id }}" 
                                                                           id="user_{{ $user->id }}">
                                                                    <label class="form-check-label" for="user_{{ $user->id }}">
                                                                        {{ $user->nama_lengkap }}
                                                                        <br><small class="text-muted">{{ $user->email }}</small>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div class="col-12">
                                                                <div class="text-center py-4">
                                                                    <i class="bi bi-person-x" style="font-size: 3rem; color: #ccc;"></i>
                                                                    <p class="text-muted mt-2">Tidak ada karyawan ditemukan</p>
                                                                </div>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">
                                                Jika tidak ada karyawan yang dipilih, sistem akan generate untuk semua karyawan.
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Warning Info -->
                                    <div class="alert alert-info" role="alert">
                                        <i class="bi bi-info-circle me-2"></i>
                                        <strong>Informasi Penting:</strong>
                                        <ul class="mb-0 mt-2">
                                            <li>Sistem akan menghitung gaji pokok berdasarkan presensi yang sudah disetujui</li>
                                            <li>Hanya shift normal yang akan dihitung untuk gaji pokok</li>
                                            <li>Jika sudah ada gaji pokok untuk periode yang sama, proses akan dilewati</li>
                                            <li>Perhitungan berdasarkan jam kerja efektif dan potongan keterlambatan</li>
                                        </ul>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex gap-3">
                                        <button type="submit" class="btn btn-primary" id="generateBtn">
                                            <i class="bi bi-gear-fill me-2"></i>
                                            Generate Gaji Pokok
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="bi bi-arrow-clockwise me-2"></i>
                                            Reset Form
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Cara Kerja Generate Gaji Pokok:</h6>
                                <ol class="small mb-0">
                                    <li>Sistem akan mengambil semua data presensi yang sudah disetujui pada periode yang dipilih</li>
                                    <li>Menghitung total jam kerja efektif dari setiap presensi (dalam batas jam shift normal)</li>
                                    <li>Menghitung total menit keterlambatan</li>
                                    <li>Menghitung gaji kotor = Total jam kerja × Tarif per jam</li>
                                    <li>Menghitung total potongan = Total menit terlambat × Tarif potongan per menit</li>
                                    <li>Gaji bersih = Gaji kotor - Total potongan</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const generateBtn = document.getElementById('generateBtn');
    const generateForm = document.getElementById('generateForm');

    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        userCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Individual checkbox change
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.user-checkbox:checked').length;
            const totalCount = userCheckboxes.length;
            
            selectAllCheckbox.checked = checkedCount === totalCount;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        });
    });

    // Form submit with loading state
    generateForm.addEventListener('submit', function() {
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Sedang Generate...';
    });
});

function resetForm() {
    document.getElementById('generateForm').reset();
    document.getElementById('selectAll').checked = false;
    document.getElementById('selectAll').indeterminate = false;
    
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}
</script>
@endsection