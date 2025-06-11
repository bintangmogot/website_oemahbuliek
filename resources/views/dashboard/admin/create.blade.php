@extends('layouts.app')
@section('content')
<x-form-layout title="Tambah User">
  
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

<div class="container">
  <form action="{{ route('admin.user.store') }}" method="POST">
    @csrf

    <div class="mb-3">
      <label>Email</label>
      <input type="email" name="email" class="form-control" value="{{ old('email') }}">
      @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label>Password</label>
      <input type="password" name="password" class="form-control">
      @error('password') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
      <label>Role</label>
      <select name="role" class="form-control">
        <option value="admin"   {{ old('role')=='admin'?'selected':'' }}>Admin</option>
        <option value="pegawai" {{ old('role')=='pegawai'?'selected':'' }}>Pegawai</option>
      </select>
      @error('role') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <button class="btn btn-success">Simpan</button>
  </form>
</div>
</x-form-layout>
@endsection
