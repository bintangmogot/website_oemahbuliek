@extends('layouts.app')

@section('title', 'Tambah Gaji Pokok')

@section('content')
<x-session-status/>

<div class="container py-5">
    {{-- Header: Title, Back Button --}}
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
            <h3 class="fw-bold mb-0">Tambah Gaji Pokok</h3>
            <a href="{{ route('gaji-pokok.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Form Card --}}
        <div class="card rounded-2xl border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('gaji-pokok.store') }}" method="POST" id="gajiPokokForm">
                    @csrf
                    
                    <div class="row">
                        {{-- Left Column --}}
                        <div class="col-md-6">
                            {{-- User Selection --}}
                            <div class="mb-3">
                                <label for="users_id" class="form-label fw-semibold">
                                    Karyawan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('users_id') is-invalid @enderror" 
                                        id="users_id" name="users_id" required>
                                    <option value="">Pilih Karyawan</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                data-tarif="{{ $user->pengaturanGaji->tarif_kerja_per_jam ?? 0 }}"
                                                data-potongan="{{ $user->pengaturanGaji->potongan_terlambat_per_menit ?? 0 }}"
                                                {{ old('users_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->nama_lengkap }}
                                            @if($user->pengaturanGaji)
                                                (Rp {{ number_format($user->pengaturanGaji->tarif_kerja_per_jam, 0, ',', '.') }}/jam)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('users_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Periode Awal --}}
                            <div class="mb-3">
                                <label for="periode_awal" class="form-label fw-semibold">
                                    Periode Awal <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('periode_awal') is-invalid @enderror" 
                                       id="periode_awal" 
                                       name="periode_awal" 
                                       value="{{ old('periode_awal') }}" 
                                       required>
                                @error('periode_awal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Periode Akhir --}}
                            <div class="mb-3">
                                <label for="periode_akhir" class="form-label fw-semibold">
                                    Periode Akhir <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('periode_akhir') is-invalid @enderror" 
                                       id="periode_akhir" 
                                       name="periode_akhir" 
                                       value="{{ old('periode_akhir') }}" 
                                       required>
                                @error('periode_akhir')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Jumlah Jam Kerja --}}
                            <div class="mb-3">
                                <label for="jumlah_jam_kerja" class="form-label fw-semibold">
                                    Jumlah Jam Kerja <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control @error('jumlah_jam_kerja') is-invalid @enderror" 
                                           id="jumlah_jam_kerja" 
                                           name="jumlah_jam_kerja" 
                                           value="{{ old('jumlah_jam_kerja', 0) }}" 
                                           min="0" 
                                           step="1"
                                           required>
                                    <span class="input-group-text">Jam</span>
                                </div>
                                @error('jumlah_jam_kerja')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Akan dihitung otomatis dari presensi jika menggunakan generate
                                </small>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="col-md-6">
                            {{-- Rate Per Jam --}}
                            <div class="mb-3">
                                <label for="rate_per_jam" class="form-label fw-semibold">
                                    Rate Per Jam <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('rate_per_jam') is-invalid @enderror" 
                                           id="rate_kerja_per_jam" 
                                           name="rate_kerja_per_jam" 
                                           value="{{ old('rate_kerja_per_jam', 0) }}" 
                                           min="0" 
                                           step="1000"
                                           required>
                                </div>
                                @error('rate_kerja_per_jam')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Rate Potongan Terlambat --}}
                            <div class="mb-3">
                                <label for="rate_potongan_per_menit" class="form-label fw-semibold">
                                    Rate Potongan Terlambat <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('rate_potongan_per_menit') is-invalid @enderror" 
                                           id="rate_potongan_per_menit" 
                                           name="rate_potongan_per_menit" 
                                           value="{{ old('rate_potongan_per_menit', 0) }}" 
                                           min="0" 
                                           step="100"
                                           required>
                                    <span class="input-group-text">/menit</span>
                                </div>
                                @error('rate_potongan_per_menit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Total Potongan Terlambat --}}
                            <div class="mb-3">
                                <label for="total_potongan_terlambat" class="form-label fw-semibold">
                                    Total Potongan Terlambat
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('total_potongan_terlambat') is-invalid @enderror" 
                                           id="total_potongan_terlambat" 
                                           name="total_potongan_terlambat" 
                                           value="{{ old('total_potongan_terlambat', 0) }}" 
                                           min="0" 
                                           step="1000">
                                </div>
                                @error('total_potongan_terlambat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> 
                                    Otomatis dihitung: menit terlambat × rate potongan
                                </small>
                            </div>

                            {{-- Total Gaji --}}
                            <div class="mb-3">
                                <label for="total_gaji" class="form-label fw-semibold">
                                    Total Gaji <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" 
                                           class="form-control @error('total_gaji') is-invalid @enderror" 
                                           id="total_gaji" 
                                           name="total_gaji" 
                                           value="{{ old('total_gaji', 0) }}" 
                                           min="0" 
                                           step="1000"
                                           required>
                                </div>
                                @error('total_gaji')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-calculator"></i> 
                                    Otomatis dihitung: jam kerja × rate per jam
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Calculation Summary Card --}}
                    <div class="card bg-light border-0 mb-4" id="calculationSummary" style="display: none;">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-calculator"></i> Ringkasan Perhitungan
                            </h6>
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h5 class="fw-bold text-success mb-1" id="totalGajiKotor">Rp 0</h5>
                                        <small class="text-muted">Total Gaji Kotor</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h5 class="fw-bold text-danger mb-1" id="totalPotongan">Rp 0</h5>
                                        <small class="text-muted">Total Potongan</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="border-end">
                                        <h5 class="fw-bold text-primary mb-1" id="totalJamKerja">0</h5>
                                        <small class="text-muted">Total Jam</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <h5 class="fw-bold text-success mb-1" id="gajiBersih">Rp 0</h5>
                                    <small class="text-muted">Gaji Bersih</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">
                        <a href="{{ route('gaji-pokok.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="button" class="btn btn-info" id="calculateBtn">
                            <i class="fas fa-calculator"></i> Hitung Ulang
                        </button>
                        <button type="submit" class="btn btn-yellow">
                            <i class="fas fa-save"></i> Simpan Gaji Pokok
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for Auto Calculation --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const usersSelect = document.getElementById('users_id');
    const jamKerjaInput = document.getElementById('jumlah_jam_kerja');
    const ratePerJamInput = document.getElementById('rate_kerja_per_jam');
    const ratePotonganInput = document.getElementById('rate_potongan_per_menit');
    const totalPotonganInput = document.getElementById('total_potongan_terlambat');
    const totalGajiInput = document.getElementById('total_gaji');
    const calculateBtn = document.getElementById('calculateBtn');
    const calculationSummary = document.getElementById('calculationSummary');

    // Auto fill rate when user selected
    usersSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const tarif = selectedOption.dataset.tarif || 0;
            const potongan = selectedOption.dataset.potongan || 0;
            
            ratePerJamInput.value = tarif;
            ratePotonganInput.value = potongan;
            
            calculateTotal();
        } else {
            ratePerJamInput.value = 0;
            ratePotonganInput.value = 0;
            calculationSummary.style.display = 'none';
        }
    });

    // Auto calculate when inputs change
    [jamKerjaInput, ratePerJamInput, totalPotonganInput].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    // Manual calculate button
    calculateBtn.addEventListener('click', calculateTotal);

    function calculateTotal() {
        const jamKerja = parseInt(jamKerjaInput.value) || 0;
        const ratePerJam = parseInt(ratePerJamInput.value) || 0;
        const totalPotongan = parseInt(totalPotonganInput.value) || 0;
        
        if (jamKerja > 0 && ratePerJam > 0) {
            const totalGajiKotor = jamKerja * ratePerJam;
            const gajiBersih = totalGajiKotor - totalPotongan;
            
            totalGajiInput.value = totalGajiKotor;
            
            // Update summary
            document.getElementById('totalGajiKotor').textContent = formatRupiah(totalGajiKotor);
            document.getElementById('totalPotongan').textContent = formatRupiah(totalPotongan);
            document.getElementById('totalJamKerja').textContent = jamKerja + ' Jam';
            document.getElementById('gajiBersih').textContent = formatRupiah(gajiBersih);
            
            calculationSummary.style.display = 'block';
        } else {
            calculationSummary.style.display = 'none';
        }
    }

    function formatRupiah(amount) {
        return 'Rp ' + amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Validate periode
    const periodeAwal = document.getElementById('periode_awal');
    const periodeAkhir = document.getElementById('periode_akhir');
    
    periodeAwal.addEventListener('change', function() {
        periodeAkhir.min = this.value;
        if (periodeAkhir.value && periodeAkhir.value < this.value) {
            periodeAkhir.value = this.value;
        }
    });

    periodeAkhir.addEventListener('change', function() {
        if (this.value < periodeAwal.value) {
            alert('Periode akhir tidak boleh kurang dari periode awal');
            this.value = periodeAwal.value;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.card-header-theme {
    border-bottom: 2px solid #f8f9fa;
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
}

.btn-yellow {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #000;
}

.btn-yellow:hover {
    background-color: #e0a800;
    border-color: #d39e00;
    color: #000;
}

.input-group-text {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.calculation-summary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.border-end {
    border-right: 1px solid #dee2e6 !important;
}

@media (max-width: 768px) {
    .border-end {
        border-right: none !important;
        border-bottom: 1px solid #dee2e6 !important;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
    }
    
    .border-end:last-child {
        border-bottom: none !important;
        margin-bottom: 0;
        padding-bottom: 0;
    }
}
</style>
@endpush