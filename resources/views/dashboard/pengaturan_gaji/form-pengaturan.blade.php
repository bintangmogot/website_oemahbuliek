@csrf
<div class="mb-3">
  <label for="nama" class="form-label">Nama Pengaturan</label>
  <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama', $pengaturan_gaji->nama ?? '') }}">
  @error('nama')<small class="text-danger">{{ $message }}</small>@enderror
</div>
<div class="mb-3">
  <label for="tarif_kerja_per_jam" class="form-label">Tarif Kerja per Jam</label>
  <input type="number" name="tarif_kerja_per_jam" id="tarif_kerja_per_jam" class="form-control" value="{{ old('tarif_kerja_per_jam', $pengaturan_gaji->tarif_kerja_per_jam ?? '') }}">
  @error('tarif_kerja_per_jam')<small class="text-danger">{{ $message }}</small>@enderror
</div>
<div class="mb-3">
  <label for="tarif_lembur_per_jam" class="form-label">Tarif Lembur per Jam</label>
  <input type="number" name="tarif_lembur_per_jam" id="tarif_lembur_per_jam" class="form-control" value="{{ old('tarif_lembur_per_jam', $pengaturan_gaji->tarif_lembur_per_jam ?? '') }}">
  @error('tarif_lembur_per_jam')<small class="text-danger">{{ $message }}</small>@enderror
</div>
<div class="mb-3">
  <label for="potongan_terlambat_per_menit" class="form-label">Potongan Terlambat per Menit</label>
  <input type="number" name="potongan_terlambat_per_menit" id="potongan_terlambat_per_menit" class="form-control" value="{{ old('potongan_terlambat_per_menit', $pengaturan_gaji->potongan_terlambat_per_menit ?? '') }}">
  @error('potongan_terlambat_per_menit')<small class="text-danger">{{ $message }}</small>@enderror
</div>
<div class="mb-3">
  <label for="status" class="form-label">Status</label>
  <select name="status" id="status" class="form-control">
    <option value="1" {{ old('status', $pengaturan_gaji->status ?? 1)==1?'selected':'' }}>Active</option>
    <option value="0" {{ old('status', $pengaturan_gaji->status ?? 1)==0?'selected':'' }}>Inactive</option>
  </select>
</div>
