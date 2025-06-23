@extends('layouts.app')
@section('content')
@if(auth()->user()->role === 'admin')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card-body px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
                <div class="card-theme py-2 mb-3">
                    <h3 class="text-center fw-bold">🗓️ Tambah Jadwal Shift</h3>
                </div>
                <form action="{{ route('jadwal-shift.store') }}" method="POST">
                    <div class="card-body">
                        @csrf
                        
                        {{-- Pilih Pegawai (Multiple Select) --}}
                    <div class="mb-3">
                    <label class="form-label">Pilih Pegawai</label>
                    <div class="row">
                        @foreach($users as $user)
                        <div class="col-6">
                            <label class="form-check">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="user_ids[]"
                                value="{{ $user->id }}"
                                {{ in_array($user->id, old('user_ids', [])) ? 'checked' : '' }}
                            >
                            <span class="form-check-label">
                                {{ $user->nama_lengkap }} — {{ $user->jabatan }}
                            </span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    </div>

                        {{-- Pilih Shift --}}
                        <div class="mb-3">
                            <label for="shift_id" class="form-label" >Shift <span class="text-danger">*</span></label>
                            <select name="shift_id" id="shift_id" class="form-select" required >
                                <option value="" >-- Pilih Shift --</option>
                                @foreach($shifts as $shift)
                                    <option 
                                        value="{{ $shift->id }}" 
                                        {{ old('shift_id') == $shift->id ? 'selected' : '' }}
                                    >
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
                            <input type="date" 
                                name="tanggal" 
                                id="tanggal" 
                                class="form-control" 
                                value="{{ old('tanggal') }}"
                                min="{{ date('Y-m-d') }}"
                                required>
                            @if($errors->has('tanggal'))
                                <small class="text-danger">{{ $errors->first('tanggal') }}</small>
                            @endif
                        </div>

                        {{-- Status --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Dibatalkan</option>
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
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection