@php
  $isEdit = isset($jadwal_shift) && !is_null($jadwal_shift) && $jadwal_shift->id_jadwal;
@endphp

@if($isEdit)
  @method('PUT')
@endif
@csrf
{{-- nama_periode --}}
<div class="mb-3">
  <label for="nama_periode" class="form-label">Nama Periode</label>
  <input type="text" name="nama_periode" id="nama_periode"
         class="form-control"
         value="{{ old('nama_periode', $jadwal_shift->nama_periode ?? '') }}">
  @error('nama_periode')<small class="text-danger">{{ $message }}</small>@enderror
</div>

{{-- shift_id --}}
<div class="mb-3">
  <label for="shift_id" class="form-label">Shift</label>
  <select name="shift_id" id="shift_id" class="form-control">
    @foreach($shifts as $id => $nama)
      <option value="{{ $id }}" {{ old('shift_id', $jadwal_shift->shift_id ?? '')==$id?'selected':'' }}>
        {{ $nama }}
      </option>
    @endforeach
  </select>
  @error('shift_id')<small class="text-danger">{{ $message }}</small>@enderror
</div>

{{-- periode tanggal --}}
<div class="row">
  <div class="col">
    <label class="form-label">Mulai Berlaku</label>
<input type="date" name="mulai_berlaku" class="form-control"
       value="{{ old('mulai_berlaku', optional($jadwal_shift)->mulai_berlaku ? 
       \Carbon\Carbon::parse($jadwal_shift->mulai_berlaku)->format('d-m-Y') : '') }}">

    @error('mulai_berlaku')<small class="text-danger">{{ $message }}</small>@enderror
  </div>
  <div class="col">
    <label class="form-label">Berakhir Berlaku</label>
    <input type="date" name="berakhir_berlaku" class="form-control"
       value="{{ old('berakhir_berlaku', optional($jadwal_shift)->berakhir_berlaku ? 
       \Carbon\Carbon::parse($jadwal_shift->berakhir_berlaku)->format('d-m-Y') : '') }}">
    @error('berakhir_berlaku')<small class="text-danger">{{ $message }}</small>@enderror
  </div>
</div>

{{-- hari_kerja --}}
<div class="mb-3">
  <label class="form-label">Hari Kerja</label>
  <div>
    @php
      $selected = old('hari_kerja', isset($jadwal_shift) ? explode(',', $jadwal_shift->hari_kerja) : []);
    @endphp
    @foreach(['Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu','Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu','Sun'=>'Minggu'] as $code=>$label)
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"
               name="hari_kerja[]" value="{{ $code }}"
               id="hari_{{ $code }}"
               {{ in_array($code, $selected)?'checked':'' }}>
        <label class="form-check-label" for="hari_{{ $code }}">{{ $label }}</label>
      </div>
    @endforeach
  </div>
  @error('hari_kerja')<small class="text-danger">{{ $message }}</small>@enderror
</div>

<button class="btn btn-{{ $isEdit ? 'primary' : 'success' }}">
  {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Jadwal' }}
</button>
