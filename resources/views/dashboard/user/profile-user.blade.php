@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-body py-4 px-4 px-lg-5 gap-3 bg-white">
                <div class="card-theme py-3 mb-3">
                    <h3 class="card-title text-center fw-bold">👤 Profil Saya</h3>
                </div>
                <div class="card-body">
                    {{-- Foto Profil --}}
                    <div class="text-center mb-4">
                        <x-avatar :src="$user->foto_profil" size="150" />
                    </div>

                    {{-- Informasi Pribadi --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Informasi Pribadi</label>
                        <div class="p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Nama Lengkap:</small>
                                    <div class="fw-bold fs-6">{{ $user->nama_lengkap }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Email:</small>
                                    <div class="fw-bold fs-6">{{ $user->email }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">No. HP:</small>
                                    <div class="fw-bold fs-6">{{ $user->no_hp }}</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Role:</small>
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-user-tag me-1"></i>
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Pekerjaan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Informasi Pekerjaan</label>
                        <div class="p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Jabatan:</small>
                                    <div class="fw-bold fs-6">
                                        <i class="fas fa-briefcase me-2 text-primary"></i>
                                        {{ $user->jabatan }}
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <small class="text-muted d-block">Pengaturan Gaji:</small>
                                    <div class="fw-bold fs-6">
                                        <i class="fas fa-money-bill-wave me-2 text-success"></i>
                                        {{ optional($user->pengaturanGaji)->nama ?? '-' }}
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <small class="text-muted d-block">Tanggal Masuk:</small>
                                    <div class="fw-bold fs-6">
                                        <i class="fas fa-calendar-plus me-2 text-info"></i>
                                        {{ $user->tgl_masuk }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Alamat --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Alamat</label>
                        <div class="p-3 bg-light rounded">
                            <div class="fs-6">
                                <i class="fas fa-map-marker-alt me-2 text-danger"></i>
                                {{ $user->alamat }}
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Akun --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Informasi Akun</label>
                        <div class="p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Akun dibuat:</small>
                                    <div>{{ $user->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Terakhir diupdate:</small>
                                    <div>{{ $user->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer d-flex justify-content-between">
                    @if(request()->routeIs('user.show'))
                    <a href="{{ route('user.index') }}" class="btn btn-theme info">
                        <i class="fas fa-arrow-left"></i> Kembali
                        </a>

                    @endif
                    @if (Auth::user()->role === 'admin')
                    <a href="{{ route('user.edit', $user->id) }}" class="btn btn-theme success">
                        <i class="fas fa-edit"></i> Edit Profil
                    </a>      
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection