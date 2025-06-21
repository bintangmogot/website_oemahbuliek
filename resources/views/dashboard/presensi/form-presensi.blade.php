@php
  // Jika create, $presensi bisa instance baru tanpa id
  $isEdit = isset($presensi) && $presensi->id_presensi;
@endphp

{{-- id_users --}}
<div class="mb-3">
  <label for="id_users" class="form-label">Pegawai</label>
  <select name="id_users" id="id_users" class="form-control">
    @foreach($users as $id => $nama)
      <option value="{{ $id }}"
        {{ old('id_users', $presensi->id_users ?? '') == $id ? 'selected' : '' }}>
        {{ $nama }}
      </option>
    @endforeach
  </select>
  @error('id_users') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- id_jadwal --}}
<div class="mb-3">
  <label for="id_jadwal" class="form-label">Jadwal Shift</label>
  <select name="id_jadwal" id="id_jadwal" class="form-control">
    @foreach($jadwals as $id => $tanggal)
      <option value="{{ $id }}"
        {{ old('id_jadwal', $presensi->id_jadwal ?? '') == $id ? 'selected' : '' }}>
        {{ $tanggal->format('d-m-Y') }}
      </option>
    @endforeach
  </select>
  @error('id_jadwal') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- tgl_presensi --}}
<div class="mb-3">
  <label for="tgl_presensi" class="form-label">Tanggal Presensi</label>
  <input type="date"
         name="tgl_presensi"
         id="tgl_presensi"
         class="form-control"
         value="{{ old('tgl_presensi', optional($presensi->tgl_presensi)->format('d-m-Y')) }}">
  @error('tgl_presensi') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- shift_ke --}}
<div class="mb-3">
  <label for="shift_ke" class="form-label">Shift Ke-</label>
  <input type="number"
         name="shift_ke"
         id="shift_ke"
         class="form-control"
         value="{{ old('shift_ke', $presensi->shift_ke ?? '') }}">
  @error('shift_ke') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- status_kehadiran --}}
<div class="mb-3">
  <label for="status_kehadiran" class="form-label">Status Kehadiran</label>
  <input type="text"
         name="status_kehadiran"
         id="status_kehadiran"
         class="form-control"
         value="{{ old('status_kehadiran', $presensi->status_kehadiran ?? '') }}">
  @error('status_kehadiran') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- keterangan --}}
<div class="mb-3">
  <label for="keterangan" class="form-label">Keterangan</label>
  <textarea name="keterangan"
            id="keterangan"
            class="form-control"
            rows="3">{{ old('keterangan', $presensi->keterangan ?? '') }}</textarea>
  @error('keterangan') <small class="text-danger">{{ $message }}</small> @enderror
</div>
