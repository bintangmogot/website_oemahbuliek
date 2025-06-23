@extends('layouts.app')
@section('title', 'Detail Jadwal')
@section('content')

{{-- Check authorization: admin can see all, user can only see their own --}}
@if(auth()->user()->role === 'admin' || $jadwalShift->users_id === auth()->id())
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-body py-4 px-4 px-lg-5 gap-3 bg-white">
                <div class="card-theme py-3 mb-3">
                    <h3 class="card-title text-center fw-bold">🗓️ Detail Jadwal Shift</h3>
                </div>
                <div class="card-body">
                    {{-- Informasi Pegawai --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pegawai</label>
                        <div class="p-3 bg-light rounded">
                            <div class="d-flex align-items-center">
                                @if($jadwalShift->user->foto_profil)
                                    <img src="{{ asset('storage/'.$jadwalShift->user->foto_profil) }}" 
                                         class="rounded-circle me-3" 
                                         width="60" height="60" 
                                         style="object-fit:cover">
                                @else
                                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center me-3" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold fs-5">{{ $jadwalShift->user->nama_lengkap }}</div>
                                    <small class="text-muted">{{ $jadwalShift->user->jabatan ?? 'Tidak ada jabatan' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Informasi Shift --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Shift</label>
                        <div class="p-3 bg-light rounded">
                            <div class="fs-5 fw-bold text-primary">{{ $jadwalShift->shift->nama_shift }}</div>
                            <div class="text-muted">
                                <i class="fas fa-clock me-2"></i>
                                  {{ $jadwalShift->shift->jam_mulai ? \Carbon\Carbon::parse($jadwalShift->shift->jam_mulai)->format('H:i') : '—' }}
                                  –
                                  {{ $jadwalShift->shift->jam_selesai ? \Carbon\Carbon::parse($jadwalShift->shift->jam_selesai)->format('H:i') : '—' }}

                            </div>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Tanggal</label>
                        <div class="p-3 bg-light rounded">
                            <div class="fs-5">
                                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                                {{ $jadwalShift->tanggal->format('d F Y') }}
                            </div>
                            <small class="text-muted">{{ $jadwalShift->tanggal->format('l') }}</small>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Status</label>
                        <div class="p-3 bg-light rounded">
                            @php
                                $statusColor = match($jadwalShift->status) {
                                    0 => 'danger',
                                    1 => 'success', 
                                    2 => 'info',
                                    default => 'secondary'
                                };
                                $statusIcon = match($jadwalShift->status) {
                                    0 => 'fas fa-times-circle',
                                    1 => 'fas fa-check-circle',
                                    2 => 'fas fa-flag-checkered',
                                    default => 'fas fa-question-circle'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }} fs-6">
                                <i class="{{ $statusIcon }} me-1"></i>
                                {{ $jadwalShift->status_label }}
                            </span>
                        </div>
                    </div>

                    {{-- Informasi Tambahan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Informasi Tambahan</label>
                        <div class="p-3 bg-light rounded">
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Dibuat pada:</small>
                                    <div>{{ $jadwalShift->created_at->format('d/m/Y H:i') }}</div>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted d-block">Terakhir diupdate:</small>
                                    <div>{{ $jadwalShift->updated_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('jadwal-shift.index') }}" class="btn btn-theme info">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    
                    {{-- Only admin can edit --}}
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('jadwal-shift.edit', $jadwalShift->id) }}" class="btn btn-theme success">
                            <i class="fas fa-edit"></i> Edit Jadwal
                        </a>
                    @endif

                    
                </div>
            </div>
        </div>
    </div>
</div>

@else
{{-- Unauthorized access --}}
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <h4>Akses Ditolak</h4>
                    <p class="text-muted">Anda tidak memiliki izin untuk melihat jadwal shift ini.</p>
                    <a href="{{ route('jadwal-shift.index') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Jadwal
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection