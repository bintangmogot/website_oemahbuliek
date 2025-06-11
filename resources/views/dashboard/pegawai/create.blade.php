@extends('layouts.app')

@section('content')

<x-form-layout title="👤 Tambah Data Pegawai">

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <form action="{{ route('admin.pegawai.store') }}" method="POST">
    @csrf

    <!-- Semua input -->
    <div class="mb-3">
      <label for="id_akun" class="form-label">Pilih Email User</label>
      <select name="id_akun" class="form-select">
        <option value="">— Pilih Email —</option>
        @foreach($users as $email)
          <option value="{{ $email }}" {{ old('id_akun') == $email ? 'selected' : '' }}>
            {{ $email }}
          </option>
        @endforeach
      </select>
      @error('id_akun') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}">
              @error('nama_lengkap') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Jabatan</label>
              <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}">
              @error('jabatan') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">Tanggal Masuk</label>
              <input type="date" name="tgl_masuk" class="form-control" value="{{ old('tgl_masuk') }}">
              @error('tgl_masuk') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
              <label class="form-label">No. HP</label>
              <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
              @error('no_hp') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-4">
              <label class="form-label">Alamat</label>
              <textarea name="alamat" class="form-control" rows="3">{{ old('alamat') }}</textarea>
              @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

    <div class="d-grid">
      <button class="btn-theme success">Tambah Pegawai</button>
    </div>
  </form>

</x-form-layout>

@endsection
