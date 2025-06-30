@php
  $isEdit = isset($shift) && $shift->id;
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

{{-- Jenis Shift --}}
<div class="mb-3">
  <label for="is_shift_lembur" class="form-label">Jenis Shift</label>
  <select name="is_shift_lembur" 
          id="is_shift_lembur" 
          class="form-select"
          required>
    <option value="">Pilih Jenis Shift</option>
    <option value="0" {{ old('is_shift_lembur', $shift->is_shift_lembur ?? '') == '0' ? 'selected' : '' }}>
      Normal
    </option>
    <option value="1" {{ old('is_shift_lembur', $shift->is_shift_lembur ?? '') == '1' ? 'selected' : '' }}>
      Lembur
    </option>
  </select>
  @error('is_shift_lembur') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Jam Mulai --}}
<div class="mb-3">
  <label for="jam_mulai" class="form-label">Jam Mulai</label>
  <input type="time"
        name="jam_mulai"
        id="jam_mulai"
        class="form-control"
      value="{{ old('jam_mulai', isset($shift->jam_mulai) ? \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') : '') }}">
  @error('jam_mulai') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Jam Selesai --}}
<div class="mb-3">
  <label for="jam_selesai" class="form-label">Jam Selesai</label>
  <input type="time"
         name="jam_selesai"
         id="jam_selesai"
         class="form-control"
       value="{{ old('jam_selesai', isset($shift->jam_selesai) ? \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') : '') }}">

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

{{-- Status --}}
<div class="mb-3">
  <label for="status" class="form-label">Status</label>
  <select name="status" 
          id="status" 
          class="form-select"
          required>
    <option value="1" {{ old('status', $shift->status ?? 1) == 1 ? 'selected' : '' }}>
      Aktif
    </option>
    <option value="0" {{ old('status', $shift->status ?? 1) == 0 ? 'selected' : '' }}>
      Tidak Aktif
    </option>
  </select>
  @error('status') <small class="text-danger">{{ $message }}</small> @enderror
</div>