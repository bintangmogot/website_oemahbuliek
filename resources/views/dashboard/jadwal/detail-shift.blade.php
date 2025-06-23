@extends('layouts.app')

@section('content')
<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header-theme">
                    <h3 class="card-title">Detail Shift: {{ $shift->nama_shift }}</h3>
                    <div class="text-white">
                        Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}
                    </div>
                </div>
                <div class="card-body">
                    {{-- Informasi Shift --}}
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <h5 class="fw-bold text-primary">{{ $shift->nama_shift }}</h5>
                                    <div class="text-muted">
                                        <i class="fas fa-clock me-2"></i>
                                        {{ $shift->jam_mulai ? \Carbon\Carbon::parse($shift->jam_mulai)->format('H:i') : '—' }}
                                        –
                                        {{ $shift->jam_selesai ? \Carbon\Carbon::parse($shift->jam_selesai)->format('H:i') : '—' }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded">
                                    <div><strong>Toleransi Terlambat:</strong> {{ $shift->toleransi_terlambat }} menit</div>
                                    <div><strong>Batas Lembur:</strong> {{ $shift->batas_lembur_min }} menit</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Daftar Pegawai yang Dijadwalkan --}}
                    <div class="mb-4">
                        <h5 class="fw-bold">Pegawai yang Dijadwalkan ({{ $jadwalShifts->count() }} orang)</h5>
                        
                        @if($jadwalShifts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead style="background-color:#FFD9D9">
                                        <tr>
                                            <th>No</th>
                                            <th>Foto</th>
                                            <th>Nama Pegawai</th>
                                            <th>Jabatan</th>
                                            <th>Status Jadwal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($jadwalShifts as $index => $jadwal)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                @if($jadwal->user->foto_profil)
                                                    <img src="{{ asset('storage/'.$jadwal->user->foto_profil) }}" 
                                                         class="rounded-circle" 
                                                         width="40" height="40" 
                                                         style="object-fit:cover">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center" 
                                                         style="width:40px; height:40px; font-size:14px; color:white;">
                                                        {{ substr($jadwal->user->nama_lengkap, 0, 1) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $jadwal->user->nama_lengkap }}</div>
                                                <small class="text-muted">{{ $jadwal->user->email }}</small>
                                            </td>
                                            <td>{{ $jadwal->user->jabatan }}</td>
                                            <td>
                                                @switch($jadwal->status)
                                                    @case(0)
                                                        <span class="badge bg-danger">Dibatalkan</span>
                                                        @break
                                                    @case(1)
                                                        <span class="badge bg-success">Aktif</span>
                                                        @break
                                                    @case(2)
                                                        <span class="badge bg-secondary">Selesai</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ route('jadwal-shift.show', $jadwal->id) }}" 
                                                       class="btn btn-outline-info btn-sm" 
                                                       title="Detail Jadwal">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('jadwal-shift.edit', $jadwal->id) }}" 
                                                       class="btn btn-outline-warning btn-sm" 
                                                       title="Edit Jadwal">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada pegawai yang dijadwalkan untuk shift ini pada tanggal tersebut.</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card-footer">
                    <a href="{{ route('jadwal-shift.pilih-shift') }}" class="btn btn-theme info">
                        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Shift
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection