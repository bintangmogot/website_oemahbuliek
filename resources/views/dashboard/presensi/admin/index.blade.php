@extends('layouts.app')
@section('title', 'Manajemen Presensi')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        <div class="container-fluid">
            <div class="row">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
                    <h3 class="card-title" style="font-weight: bold;">
                        📊 Manajemen Presensi - Semua Pegawai
                    </h3>
                    <div class="d-flex gap-3">
                        <!-- Filter Button -->
                        <button class="btn btn-theme primary me-2 py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                            <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                        </button>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="collapse mb-3" id="filterCollapse">
                    <div class="card card-body">
                        <form method="GET" action="{{ route('admin.presensi.index') }}">
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="form-label">Status Approval</label>
                                    <select name="status_approval" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="0" {{ request('status_approval') === '0' ? 'selected' : '' }}>Pending</option>
                                        <option value="1" {{ request('status_approval') === '1' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="2" {{ request('status_approval') === '2' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Pegawai</label>
                                    <select name="user_id" class="form-control">
                                        <option value="">Semua Pegawai</option>
                                        @if(isset($users) && $users->count() > 0)
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" 
                                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                                    {{ $user->nama_lengkap }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-4 d-flex align-items-end mt-2">
                                    <button type="submit" class="btn btn-theme info p-2 px-md-3 me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('admin.presensi.index') }}" class="btn btn-theme primary p-2 px-3" style="border-color: #B50000;">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $pendingCount ?? 0 }}</h5>
                                            <small>Pending Approval</small>
                                        </div>
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $approvedCount ?? 0 }}</h5>
                                            <small>Disetujui</small>
                                        </div>
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $lemburApprovedCount ?? 0 }}</h5>
                                            <small>Lembur Disetujui</small>
                                        </div>
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5>{{ $totalPresensi ?? 0 }}</h5>
                                            <small>Total Presensi</small>
                                        </div>
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive shadow-md rounded-3">
                        <table class="table table-borderless table-striped table-hover rounded-3 shadow-sm">
                            <thead>
                                <tr>
                                    <th style="background-color: #ca414e; color: white;">No</th>
                                    <th style="background-color: #ca414e; color: white;">Nama Pegawai</th>
                                    <th style="background-color: #ca414e; color: white;">Tanggal</th>
                                    <th style="background-color: #ca414e; color: white;">Shift</th>
                                    <th style="background-color: #ca414e; color: white;">Jam Masuk</th>
                                    <th style="background-color: #ca414e; color: white;">Jam Keluar</th>
                                    <th style="background-color: #ca414e; color: white;">Jam Kerja Efektif</th>
                                    <th style="background-color: #ca414e; color: white;">Status Kehadiran</th>
                                    <th style="background-color: #ca414e; color: white;">Status Lembur</th>
                                    <th style="background-color: #ca414e; color: white;">Status Approval</th>
                                    <th style="background-color: #ca414e; color: white;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($presensi as $index => $item)
                                @php
                                    $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
                                     $hariInggris = $item->tgl_presensi->format('l');
                                @endphp
                                <tr>
                                    <td>{{ $presensi->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item->user->foto_profil)
                                                <img src="{{ Storage::url($item->user->foto_profil) }}" 
                                                     class="rounded-circle me-2" 
                                                     width="30" height="30" 
                                                     style="object-fit:cover">
                                            @else
                                                <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                     style="width:30px; height:30px; font-size:12px; color:white;">
                                                    {{ substr($item->user->nama_lengkap, 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item->user->nama_lengkap }}</div>
                                                <small class="text-muted">{{ $item->user->jabatan }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $item->tgl_presensi->format('d/m/Y') }}
                                        <br><small class="text-muted">{{ $hari[$hariInggris] }}</small>
                                    </td>
                                    <td>
                                        @if($item->jadwalShift && $item->jadwalShift->shift)
                                            <span class="badge bg-info">{{ $item->jadwalShift->shift->nama_shift }}</span>
                                            <br><small class="text-muted">
                                                {{ \Carbon\Carbon::parse($item->jadwalShift->shift->jam_mulai)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($item->jadwalShift->shift->jam_selesai)->format('H:i') }}
                                            </small>
                                            <br><small class="fw-bold"> {{ $item->jadwalShift->shift->is_shift_lembur == 1 ? 'Shift Lembur'  : 'Shift Normal' }}</small>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_masuk)
                                            <strong>{{ \Carbon\Carbon::parse($item->jam_masuk)->format('H:i') }}</strong>
                                            @if($item->menit_terlambat > 0)
                                                <br><small class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> 
                                                    Terlambat {{ $item->menit_terlambat }} menit
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum Check In</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->jam_keluar)
                                            <strong>{{ \Carbon\Carbon::parse($item->jam_keluar)->format('H:i') }}</strong>
                                            @php
                                                $overtime = $item->calculateOvertime();
                                            @endphp
                                            @if($overtime > 0)
                                                <br><small class="text-info">
                                                    <i class="fas fa-clock"></i> 
                                                    Lembur {{ $overtime }} menit
                                                </small>
                                            @endif
                                        @else
                                            <span class="text-muted">Belum Check Out</span>
                                        @endif
                                    </td>
                                    <td>
                                    @php
                                        $jamKerjaEfektif = $item->calculateEffectiveWorkHours();
                                        $jamKerjaFormatted = $item->jam_kerja_efektif_formatted;
                                        $jamDecimal = $jamKerjaEfektif / 60;
                                    @endphp
                                    
                                    @if($jamKerjaEfektif > 0)
                                        <div class="text-center">
                                            <span class="fw-bold text-primary">{{ $jamKerjaFormatted }}</span>
                                            <br>
                                            <small class="text-muted">({{ number_format($jamDecimal, 2) }} jam)</small>
                                            
                                            @if($jamDecimal >= 7)
                                                <br><small class="badge bg-success">Full Time</small>
                                            @elseif($jamDecimal >= 4)
                                                <br><small class="badge bg-warning">Lebih Awal</small>
                                            @else
                                                <br><small class="badge bg-danger">Kurang</small>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <span class="text-muted">-</span>
                                            <br>
                                            <small class="badge bg-secondary">No Data</small>
                                        </div>
                                    @endif
                                </td>
                                    <td>
                                        @switch($item->status_kehadiran)
                                            @case(0)
                                                <span class="badge bg-danger">Tidak Hadir</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-success">Hadir</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-warning">Terlambat</span>
                                                @break
                                            @case(3)
                                                <span class="badge bg-info">Pulang Awal</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($item->status_lembur)
                                            @case(0)
                                                <span class="badge bg-secondary">Tidak Lembur</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-warning">Lembur (Pending)</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-success">Lembur (Disetujui)</span>
                                                @break
                                            @case(3)
                                                <span class="badge bg-info">Shift Lembur (Pending)</span>
                                                @break
                                            @case(4)
                                                <span class="badge bg-info">Shift Lembur <br> Overtime (Pending)</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        @switch($item->status_approval)
                                            @case(0)
                                                <span class="badge bg-warning">Pending</span>
                                                @break
                                            @case(1)
                                                <span class="badge bg-success">Disetujui</span>
                                                @break
                                            @case(2)
                                                <span class="badge bg-danger">Ditolak</span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('presensi.detail', $item->id) }}" 
                                               class="btn btn-outline-info btn-sm" 
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($item->status_approval == 0 || $item->status_approval == 2)
                                                <button class="btn btn-outline-success btn-sm" 
                                                        title="Setujui"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#approveModal{{ $item->id }}">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm" 
                                                        title="Tolak"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $item->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>

                                        <!-- Approve Modal -->
                                        <div class="modal fade" id="approveModal{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Setujui Presensi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.presensi.approve', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Yakin ingin menyetujui presensi <strong>{{ $item->user->nama_lengkap }}</strong> pada tanggal <strong>{{ $item->tgl_presensi->format('d/m/Y') }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Catatan Admin (Opsional)</label>
                                                                <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-success">Setujui</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Tolak Presensi</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form action="{{ route('admin.presensi.reject', $item->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <p>Yakin ingin menolak presensi <strong>{{ $item->user->nama_lengkap }}</strong> pada tanggal <strong>{{ $item->tgl_presensi->format('d/m/Y') }}</strong>?</p>
                                                            <div class="mb-3">
                                                                <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                <textarea name="catatan_admin" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..." required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger">Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Belum ada data presensi</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($presensi->hasPages())
                        <div class="d-flex justify-content-end mt-3">
                            {{ $presensi->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection