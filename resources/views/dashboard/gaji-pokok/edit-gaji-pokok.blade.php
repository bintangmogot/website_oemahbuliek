@extends('layouts.app')

@section('title', 'Edit Gaji Pokok')

@section('content')
<x-session-status/>

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme">
            <h3 class="fw-bold mb-0">Edit Gaji Pokok</h3>
            <a href="{{ route('gaji-pokok.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        {{-- Form Card --}}
        <div class="card rounded-2xl border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('gaji-pokok.update', $gajiPokok->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        {{-- Karyawan (Read-only) --}}
                        <div class="col-md-6">
                            <label for="user_name" class="form-label">Karyawan</label>
                            <input type="text" id="user_name" class="form-control" 
                                   value="{{ $gajiPokok->user->nama_lengkap }}" readonly>
                        </div>

                        {{-- Periode (Read-only) --}}
                        <div class="col-md-6">
                            <label for="periode" class="form-label">Periode</label>
                            <input type="text" id="periode" class="form-control" 
                                   value="{{ $gajiPokok->periode_awal->format('d M Y') }} - {{ $gajiPokok->periode_akhir->format('d M Y') }}" readonly>
                        </div>

                        {{-- Pengaturan Gaji --}}
                        <div class="col-md-6">
                            <label for="pengaturan_gaji_id" class="form-label">Pengaturan Gaji</label>
                            <select name="pengaturan_gaji_id" id="pengaturan_gaji_id" class="form-select @error('pengaturan_gaji_id') is-invalid @enderror">
                                @foreach($pengaturanGaji as $pengaturan)
                                    <option value="{{ $pengaturan->id }}" 
                                            data-tarif="{{ $pengaturan->tarif_per_jam }}"
                                            {{ old('pengaturan_gaji_id', $gajiPokok->pengaturan_gaji_id) == $pengaturan->id ? 'selected' : '' }}>
                                        {{ $pengaturan->nama_pengaturan }} - Rp {{ number_format($pengaturan->tarif_per_jam) }}/jam
                                    </option>
                                @endforeach
                            </select>
                            @error('pengaturan_gaji_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Status Pembayaran --}}
                        <div class="col-md-6">
                            <label for="status_pembayaran" class="form-label">Status Pembayaran</label>
                            <select name="status_pembayaran" id="status_pembayaran" class="form-select @error('status_pembayaran') is-invalid @enderror">
                                @foreach($statusOptions as $key => $label)
                                    <option value="{{ $key }}" {{ old('status_pembayaran', $gajiPokok->status_pembayaran) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_pembayaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Jumlah Jam Kerja --}}
                        <div class="col-md-6">
                            <label for="jumlah_jam_kerja" class="form-label">Jumlah Jam Kerja</label>
                            <input type="number" name="jumlah_jam_kerja" id="jumlah_jam_kerja" 
                                   class="form-control @error('jumlah_jam_kerja') is-invalid @enderror" 
                                   value="{{ old('jumlah_jam_kerja', $gajiPokok->jumlah_jam_kerja) }}" min="0">
                            @error('jumlah_jam_kerja')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total Gaji --}}
                        <div class="col-md-6">
                            <label for="total_gaji" class="form-label">Total Gaji</label>
                            <input type="number" name="total_gaji" id="total_gaji" 
                                   class="form-control @error('total_gaji') is-invalid @enderror" 
                                   value="{{ old('total_gaji', $gajiPokok->total_gaji) }}" min="0" readonly>
                            @error('total_gaji')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Total Potongan Terlambat --}}
                        <div class="col-md-6">
                            <label for="total_potongan_terlambat" class="form-label">Total Potongan Terlambat</label>
                            <input type="number" name="total_potongan_terlambat" id="total_potongan_terlambat" 
                                   class="form-control @error('total_potongan_terlambat') is-invalid @enderror" 
                                   value="{{ old('total_potongan_terlambat', $gajiPokok->total_potongan_terlambat) }}" min="0">
                            @error('total_potongan_terlambat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal Bayar --}}
                        <div class="col-md-6">
                            <label for="tgl_bayar" class="form-label">Tanggal Bayar</label>
                            <input type="date" name="tgl_bayar" id="tgl_bayar" 
                                   class="form-control @error('tgl_bayar') is-invalid @enderror" 
                                   value="{{ old('tgl_bayar', $gajiPokok->tgl_bayar?->format('Y-m-d')) }}">
                            @error('tgl_bayar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Gaji Bersih (Display only) --}}
                        <div class="col-md-12">
                            <label for="gaji_bersih" class="form-label">Gaji Bersih</label>
                            <input type="text" id="gaji_bersih" class="form-control" readonly 
                                   value="Rp {{ number_format($gajiPokok->total_gaji_bersih) }}">
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('gaji-pokok.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-theme primary">
                            <i class="fas fa-save"></i> Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pengaturanGajiSelect = document.getElementById('pengaturan_gaji_id');
    const jamKerjaInput = document.getElementById('jumlah_jam_kerja');
    const totalGajiInput = document.getElementById('total_gaji');
    const potonganInput = document.getElementById('total_potongan_terlambat');
    const gajiBersihInput = document.getElementById('gaji_bersih');

    // Calculate total when inputs change
    [pengaturanGajiSelect, jamKerjaInput, potonganInput].forEach(input => {
        input.addEventListener('input', calculateTotal);
    });

    function calculateTotal() {
        const selectedOption = pengaturanGajiSelect.options[pengaturanGajiSelect.selectedIndex];
        const tarif = parseFloat(selectedOption.dataset.tarif) || 0;
        const jamKerja = parseFloat(jamKerjaInput.value) || 0;
        const potongan = parseFloat(potonganInput.value) || 0;

        const totalGaji = tarif * jamKerja;
        const gajiBersih = totalGaji - potongan;

        totalGajiInput.value = totalGaji;
        gajiBersihInput.value = 'Rp ' + new Intl.NumberFormat('id-ID').format(gajiBersih);
    }
});
</script>
@endsection