
@csrf
@if(isset($pegawai))
    @method('PUT')
@endif

{{-- Email dan Password untuk User --}}
<div class="form-group mb-3">
    <label for="id_akun">Email</label>

    @if(isset($pegawai))
        {{-- Saat edit: tampilkan email readonly + hidden input --}}
        <input type="email" class="form-control" value="{{ $pegawai->id_akun }}" readonly>
        <input type="hidden" name="id_akun" value="{{ $pegawai->id_akun }}">
    @else
        {{-- Saat create: input biasa --}}
        <input type="email" name="id_akun" id="id_akun"
               value="{{ old('id_akun') }}"
               class="form-control" required>
        @error('id_akun') <small class="text-danger">{{ $message }}</small> @enderror
    @endif
</div>



{{-- Hanya saat create user baru, masukkan password --}}
@if(!isset($pegawai))
<div class="form-group mb-3">
    <label for="password">Password User</label>
    <input type="password" name="password" id="password"
           class="form-control" required>
@if($errors->has('password'))
    <small class="text-danger">{{ $errors->first('password') }}</small>
@endif

</div>
@endif

{{-- Data Pegawai --}}
<div class="form-group mb-3">
    <label for="nama_lengkap">Nama Lengkap</label>
    <input type="text" name="nama_lengkap" id="nama_lengkap"
           value="{{ old('nama_lengkap', $pegawai->nama_lengkap ?? '') }}"
           class="form-control" required>
    @error('nama_lengkap') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group mb-3">
    <label for="jabatan">Jabatan</label>
    <input type="text" name="jabatan" id="jabatan"
           value="{{ old('jabatan', $pegawai->jabatan ?? '') }}"
           class="form-control" required>
    @error('jabatan') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group mb-3">
    <label for="tgl_masuk">Tanggal Masuk</label>
    <input type="date" name="tgl_masuk" id="tgl_masuk"
           value="{{ old('tgl_masuk', isset($pegawai) ? $pegawai->tgl_masuk->format('Y-m-d') : '') }}"
           class="form-control" required>
    @error('tgl_masuk') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group mb-3">
    <label for="no_hp">No. HP</label>
    <input type="text" name="no_hp" id="no_hp"
           value="{{ old('no_hp', $pegawai->no_hp ?? '') }}"
           class="form-control" required>
    @error('no_hp') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<div class="form-group mb-3">
    <label for="alamat">Alamat</label>
    <textarea name="alamat" id="alamat" rows="3"
              class="form-control">{{ old('alamat', $pegawai->alamat ?? '') }}</textarea>
    @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
</div>

<button type="submit" class="btn btn-primary">
    {{ isset($pegawai) ? 'Update Pegawai' : 'Tambah Pegawai' }}
</button>
