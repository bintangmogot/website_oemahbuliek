@extends('layouts.app')
@section('title', 'Gaji Lembur Saya')
@section('content')

<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme">
            <h3 class="fw-bold mb-0">💰 Gaji Lembur Saya</h3>
            <div class="d-flex gap-2">
                {{-- Filter Button --}}
                <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i>
                </button>
            </div>
        </div>

        {{-- Filter Section --}}
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <form method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Bulan</label>
                            <select name="bulan" class="form-select">
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ request('bulan', Carbon\Carbon::now()->month) == $i ? 'selected' : '' }}>
                                        {{ Carbon\Carbon::create()->month($i)->format('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tahun</label>
                            <select name="tahun" class="form-select">
                                @for($year = Carbon\Carbon::now()->year; $year >= Carbon\Carbon::now()->year - 2; $year--)
                                    <option value="{{ $year }}" {{ request('tahun', Carbon\Carbon::now()->year) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme info me-2 p-2 px-3">Filter</button>
                            <a href="{{ route('gaji-lembur.pegawai.index') }}" class="btn btn-theme primary p-2 px-3" style=" border-color: #B50000;">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5>Total Lembur Bulan Ini</h5>
                        <h3>Rp {{ number_format($totalLemburBulanIni, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5>Total Jam Lembur</h5>
                        <h3>{{ number_format($totalJamLemburBulanIni, 1) }} Jam</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h5>Belum Dibayar</h5>
                        <h3>Rp {{ number_format($totalBelumDibayar, 0, ',', '.') }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card rounded-2xl border-0 shadow-sm rounded-3">
            <div class="card-body p-0 table-responsive rounded-3" style="min-height: 50vh">
                <table class="table table-striped table-borderless mb-0 rounded-3">
                    <thead style="background-color:#FFD9D9">
                        <tr>
                            <th>Tanggal Lembur</th>
                            <th>Shift</th>
                            <th>Tipe Shift</th>
                            <th>Total Jam</th>
                            <th>Rate/Jam</th>
                            <th>Total Gaji</th>
                            <th>Status</th>
                            <th>Tgl Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gajiLembur as $item)
                            <tr class="bg-white">
                                <td class="align-middle">
                                    <strong>{{ $item->tgl_lembur->format('d/m/Y') }}</strong>
                                    <br><small class="text-muted">{{ $item->tgl_lembur->format('l') }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($item->presensi && $item->presensi->jadwalShift)
                                        <div>
                                            <strong>{{ $item->presensi->jadwalShift->shift->nama_shift }}</strong>
                                            <br><small class="text-muted">
                                                {{ $item->presensi->jadwalShift->shift->jam_mulai }} - 
                                                {{ $item->presensi->jadwalShift->shift->jam_selesai }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                        @if($item->presensi->jadwalShift && $item->presensi->jadwalShift->shift)
                                            @if($item->presensi->jadwalShift->shift->is_shift_lembur == 1)
                                                <span class="badge bg-info">Shift Lembur</span>
                                            @else
                                                <span class="badge bg-secondary">Shift Normal</span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $item->formatted_total_jam_lembur }}</strong>
                                </td>
                                <td class="align-middle">{{ $item->formatted_rate_lembur_per_jam }}</td>
                                <td class="align-middle">
                                    <strong class="text-success">{{ $item->formatted_total_gaji_lembur }}</strong>
                                </td>
                                <td class="align-middle">
                                    <span class="badge {{ $item->status_pembayaran_badge }}">
                                        {{ $item->status_pembayaran_label }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    {{ $item->tgl_bayar ? $item->tgl_bayar->format('d/m/Y') : '—' }}
                                </td>
                                <td class="align-middle">
                                    <a href="{{ route('gaji-lembur.show', $item->id) }}" class="btn btn-sm btn-theme info">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-clock fa-3x mb-3"></i>
                                        <p>Belum ada data gaji lembur untuk periode ini.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($gajiLembur->hasPages())
            <div class="d-flex justify-content-end mt-3">
                {{ $gajiLembur->appends(request()->query())->links() }}
            </div>
        @endif

        {{-- Info Box --}}
        <div class="alert alert-info mt-4" role="alert">
            <h6><i class="fas fa-info-circle"></i> Informasi:</h6>
            <ul class="mb-0">
                <li>Data gaji lembur dihitung berdasarkan presensi dan jadwal shift yang telah ditetapkan</li>
                <li>Status pembayaran akan diupdate oleh admin setelah gaji lembur dibayarkan</li>
                <li>Jika ada ketidaksesuaian data, silakan hubungi admin atau bagian HRD</li>
            </ul>
        </div>
    </div>
</div>

@endsection