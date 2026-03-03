@extends('layouts.app')
@section('title', 'Edit Jadwal Pegawai')

@section('content')

    @if(auth()->user()->role === 'admin')
        <div class="container-fluid mt-5">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Terjadi kesalahan:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    <div class="card-body px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
                        <div class="card-theme py-2 mb-3">
                            <h3 class="text-center fw-bold">🗓️ Edit Jadwal Shift</h3>
                        </div>
                        <form action="{{ route('jadwal-shift.update', $jadwalShift->id) }}" method="POST">
                            <div class="card-body">
                                @csrf
                                @method('PUT')

                                {{-- Info Pegawai yang sudah dipilih --}}
                                <div class="mb-3">
                                    <label class="form-label">Pegawai Saat Ini</label>
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            @if($jadwalShift->user->foto_profil)
                                                <img src="{{ Storage::url($jadwalShift->user->foto_profil) }}"
                                                    class="rounded-circle me-3" width="50" height="50" style="object-fit:cover">
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $jadwalShift->user->nama_lengkap }}</div>
                                                <small class="text-muted">{{ $jadwalShift->user->jabatan }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Ganti Pegawai --}}
                                <div class="mb-3">
                                    <label for="users_id" class="form-label">Ganti Pegawai</label>
                                    <select name="users_id" id="users_id" class="form-select">
                                        <option value="{{ $jadwalShift->users_id }}">{{ $jadwalShift->user->nama_lengkap }}
                                            (Tidak berubah)</option>
                                        @foreach($users as $user)
                                            @if($user->id != $jadwalShift->users_id)
                                                <option value="{{ $user->id }}" {{ old('users_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->nama_lengkap }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    @if($errors->has('users_id'))
                                        <small class="text-danger">{{ $errors->first('users_id') }}</small>
                                    @endif
                                </div>

                                {{-- Pilih Shift --}}
                                <div class="mb-3">
                                    <label for="shift_id" class="form-label">Shift <span class="text-danger">*</span></label>
                                    <select name="shift_id" id="shift_id" class="form-control" required>
                                        @foreach($shifts as $shift)
                                            <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                                {{ $shift->nama_shift }}
                                                (
                                                {{ $shift->jam_mulai ? \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') : '—' }}
                                                –
                                                {{ $shift->jam_selesai ? \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') : '—' }}
                                                )
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($errors->has('shift_id'))
                                        <small class="text-danger">{{ $errors->first('shift_id') }}</small>
                                    @endif
                                </div>

                                {{-- Tanggal --}}
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal" id="tanggal" class="form-control"
                                        value="{{ old('tanggal', $jadwalShift->tanggal->format('Y-m-d')) }}" required>
                                    @if($errors->has('tanggal'))
                                        <small class="text-danger">{{ $errors->first('tanggal') }}</small>
                                    @endif
                                </div>

                                {{-- Status --}}
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="1" {{ old('status', $jadwalShift->status) == '1' ? 'selected' : '' }}>Aktif
                                        </option>
                                        <option value="0" {{ old('status', $jadwalShift->status) == '0' ? 'selected' : '' }}>
                                            Dibatalkan</option>
                                        <option value="2" {{ old('status', $jadwalShift->status) == '2' ? 'selected' : '' }}>
                                            Selesai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="card-footer d-flex justify-content-between">
                                <a href="{{ route('jadwal-shift.index') }}" class="btn btn-theme info">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-theme success">
                                    <i class="fas fa-save"></i> Simpan Jadwal
                                </button>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        </div>
    @endif
@endsection