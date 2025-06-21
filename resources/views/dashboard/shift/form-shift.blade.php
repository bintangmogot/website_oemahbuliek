@php
  $isEdit = isset($shift) && $shift->id_shift;
@endphp

@isset($shift)
  @method('PUT')
@endisset
@csrf

{{-- Nama Shift --}}
<div class="mb-3">
  <label for="nama_shift" class="form-label">Nama Shift</label>
  <input type="text"
         name="nama_shift"
         id="nama_shift"
         class="form-control"
         value="{{ old('nama_shift', $shift->nama_shift ?? '') }}">
  @error('nama_shift') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Jam Mulai --}}
<div class="mb-3">
  <label for="jam_mulai" class="form-label">Jam Mulai</label>
  <input type="time"
         name="jam_mulai"
         id="jam_mulai"
         class="form-control"
         value="{{ old('jam_mulai', $shift->jam_mulai ?? '') }}">
  @error('jam_mulai') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Jam Selesai --}}
<div class="mb-3">
  <label for="jam_selesai" class="form-label">Jam Selesai</label>
  <input type="time"
         name="jam_selesai"
         id="jam_selesai"
         class="form-control"
         value="{{ old('jam_selesai', $shift->jam_selesai ?? '') }}">
  @error('jam_selesai') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Toleransi Telat --}}
<div class="mb-3">
  <label for="toleransi_terlambat" class="form-label">Toleransi Terlambat (menit)</label>
  <input type="number"
         name="toleransi_terlambat"
         id="toleransi_terlambat"
         class="form-control"
         min="0"
         value="{{ old('toleransi_terlambat', $shift->toleransi_terlambat ?? 0) }}">
  @error('toleransi_terlambat') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Batas Lembur --}}
<div class="mb-3">
  <label for="batas_lembur_min" class="form-label">Batas Lembur (menit)</label>
  <input type="number"
         name="batas_lembur_min"
         id="batas_lembur_min"
         class="form-control"
         min="0"
         value="{{ old('batas_lembur_min', $shift->batas_lembur_min ?? 0) }}">
  @error('batas_lembur_min') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<button class="btn btn-{{ $isEdit ? 'primary' : 'success' }}">
  {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Shift' }}
</button>
