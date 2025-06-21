@php
  $isEdit = isset($pegawai_jadwal);
@endphp

@csrf
@isset($pegawai_jadwal)
  @method('PUT')
@endisset
{{-- Pegawai --}}
<div class="mb-3">
  <label for="users_id" class="form-label">Pegawai</label>
  <select name="users_id" id="users_id" class="form-control">
    @foreach($users as $id => $nama)
      <option value="{{ $id }}"
        {{ old('users_id', $pegawai_jadwal->users_id ?? '') == $id ? 'selected':'' }}>
        {{ $nama }}
      </option>
    @endforeach
  </select>
  @error('users_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Jadwal --}}
<div class="mb-3">
  <label for="jadwal_shift_id" class="form-label">Periode & Shift</label>
  <select name="jadwal_shift_id" id="jadwal_shift_id" class="form-control">
    @foreach($jadwals as $id => $js)
      <option value="{{ $id }}"
        data-mulai="{{ $js->mulai_berlaku->format('d-m-Y') }}"
        data-akhir="{{ $js->berakhir_berlaku?->format('d-m-Y') }}"
        {{ old('jadwal_shift_id', $pegawai_jadwal->jadwal_shift_id ?? '') == $id ? 'selected':'' }}>
        {{ $js->nama_periode }} ({{ $js->shift->nama_shift }})
      </option>
    @endforeach
  </select>
  @error('jadwal_shift_id') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Tampilkan rentang tanggal dinamis --}}
<div class="mb-3">
  <label class="form-label">Rentang Tanggal</label>
  <p id="rentang-tanggal" class="form-control-plaintext">
    {{-- Default on load --}}
    @php
      $first = $jadwals->first();
      $start = optional($first)->mulai_berlaku?->format('d-m-Y') ?? '-';
      $end   = optional($first)->berakhir_berlaku?->format('d-m-Y') ?: '∞';
    @endphp
    {{ $start }} - {{ $end }}
  </p>
</div>

{{-- Script untuk update dinamis --}}
@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('jadwal_shift_id');
    const rentang = document.getElementById('rentang-tanggal');
    select.addEventListener('change', function() {
      const opt = select.options[select.selectedIndex];
      const mulai = opt.dataset.mulai;
      const akhir = opt.dataset.akhir || '∞';
      // Format tanggal dari YYYY-MM-DD ke DD-MM-YYYY
      const fmt = (d) => {
        if (!d || d === '∞') return '∞';
        const [day,m,y] = d.split('-');
        return `${day}-${m}-${y}`;
      };
      rentang.textContent = `${fmt(mulai)} - ${fmt(akhir)}`;
    });
  });
</script>
@endpush
<button class="btn btn-{{ $isEdit ? 'primary' : 'success' }}">
  {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Jadwal' }}
</button>
