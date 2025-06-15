@csrf

{{-- Jika edit: kirim method PUT dan hidden email --}}
@isset($user)
  @method('PUT')
  <input type="hidden" name="email" value="{{ $user->email }}">
@else
  {{-- create: nothing ekstra --}}
@endisset

{{-- Email --}}
<div class="mb-3">
  <label for="email" class="form-label">Email</label>
  @if(isset($user))
    <input type="email" id="email" class="form-control" value="{{ $user->email }}" readonly>
  @else
    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}">
    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
  @endif
</div>

{{-- Password --}}
<div class="mb-3">
  <label for="password" class="form-label">
    {{ isset($user) ? 'Password Baru (opsional)' : 'Password' }}
  </label>
  <input type="password"
        name="password"
        id="password"
        class="form-control"
        placeholder="{{ isset($user) ? 'Kosongkan jika tidak diubah' : '' }}">
  @error('password') <small class="text-danger">{{ $message }}</small> @enderror
</div>

{{-- Role --}}
<div class="mb-3">
  <label for="role" class="form-label">Role</label>
  <select name="role" id="role" class="form-control">
    <option value="admin"   {{ old('role', $user->role ?? '')=='admin'   ? 'selected':'' }}>Admin</option>
    <option value="pegawai" {{ old('role', $user->role ?? '')=='pegawai' ? 'selected':'' }}>Pegawai</option>
  </select>
</div>


{{-- Fields pegawai --}}
  <hr>
  <div class="mb-3">
    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
    <input type="text"
          name="nama_lengkap"
          id="nama_lengkap"
          class="form-control"
          value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}">
    @error('nama_lengkap') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="mb-3">
    <label for="jabatan" class="form-label">Jabatan</label>
    <input type="text"
          name="jabatan"
          id="jabatan"
          class="form-control"
          value="{{ old('jabatan', $user->jabatan ?? '') }}">
    @error('jabatan') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="mb-3">
    <label for="tgl_masuk" class="form-label">Tanggal Masuk</label>
    <input type="date"
          name="tgl_masuk"
          id="tgl_masuk"
          class="form-control"
          value="{{ old('tgl_masuk', isset($user->tgl_masuk) ? \Carbon\Carbon::parse($user->tgl_masuk)->format('Y-m-d') : '') }}"
    @error('tgl_masuk') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="mb-3">
    <label for="no_hp" class="form-label">No. HP</label>
    <input type="text"
          name="no_hp"
          id="no_hp"
          class="form-control"
          value="{{ old('no_hp', $user->no_hp ?? '') }}">
    @error('no_hp') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="mb-3">
    <label for="alamat" class="form-label">Alamat</label>
    <textarea name="alamat"
              id="alamat"
              class="form-control"
              rows="3">{{ old('alamat', $user->alamat ?? '') }}</textarea>
    @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
  </div>

  <div class="mb-3">
    <label for="foto_profil" class="form-label">Foto Profil</label>
      @if(optional($user)->foto_profil)
      <div class="mb-2">
        <img src="{{ asset('storage/'.$user->foto_profil) }}"
            class="rounded-circle"
            width="100"
            height="100"
            alt="Foto Profil"
            style="object-fit:cover">
      </div>
    @endif
    <input type="file" name="foto_profil" id="foto_profil" class="form-control">
    @error('foto_profil') <small class="text-danger">{{ $message }}</small> @enderror
  </div>


@push('scripts')
<script>
  const roleSelect = document.getElementById('role');
  const pegFields  = document.getElementById('fields-pegawai');
  function toggle() {
    pegFields.style.display = roleSelect.value==='pegawai' ? 'block' : 'none';
  }
  roleSelect.addEventListener('change', toggle);
  document.addEventListener('DOMContentLoaded', toggle);
</script>
@endpush
