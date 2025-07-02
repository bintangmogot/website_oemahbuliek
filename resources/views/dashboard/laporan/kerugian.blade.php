@extends('layouts.app')
@section('title', 'Laporan Kerugian Bahan Baku')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme pb-3">
            <h3 class="fw-bold mb-0">📉 Laporan Kerugian Bahan Baku</h3>
            <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="bi bi-funnel" style="font-size: 1.2rem"></i> Filter Tanggal
            </button>
        </div>
 
        {{-- Filter Section --}}
        <div class="collapse {{ request()->has('tanggal_dari') ? 'show' : '' }} mb-4" id="filterCollapse">
            <div class="card card-body">
                <form method="GET" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai', now()->endOfMonth()->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-theme info w-100"><i class="fas fa-search"></i> Terapkan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Summary Card --}}
        <div class="card bg-danger text-white mb-4">
            <div class="card-body text-center">
                <h5 class="card-title">Total Kerugian pada Periode Ini</h5>
                <h2 class="display-5 fw-bold">Rp {{ number_format($totalKerugian, 0, ',', '.') }}</h2>
            </div>
        </div>

        {{-- Table --}}
        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body p-0 table-responsive rounded-3">
                <table class="table table-striped table-borderless mb-0 rounded-3">
                    <thead style="background-color:#ca414e; color: white;">
                        <tr>
                            <th>Tanggal Rusak</th>
                            <th>Nama Bahan</th>
                            <th>Kuantitas Rusak</th>
                            <th>Harga Satuan (Beli)</th>
                            <th>Nilai Kerugian</th>
                            <th>Dicatat Oleh</th>
                            <th>Alasan/Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($itemsRusak as $item)
                        <tr class="bg-white">
                            <td class="align-middle">{{ $item->tanggal->format('d M Y, H:i') }}</td>
                            <td class="align-middle fw-bold">{{ $item->bahanBaku->nama ?? 'N/A' }}</td>
                            <td class="align-middle text-danger">{{ abs($item->kuantitas) }} {{ $item->bahanBaku->satuan_label ?? '' }}</td>
                            <td class="align-middle">Rp {{ number_format($item->harga_saat_rusak, 0, ',', '.') }}</td>
                            <td class="align-middle text-danger fw-bold">Rp {{ number_format($item->nilai_kerugian, 0, ',', '.') }}</td>
                            <td class="align-middle">{{ $item->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="align-middle">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <p class="text-muted">Tidak ada data kerugian pada periode yang dipilih.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $itemsRusak->links() }}
        </div>
    </div>
</div>
@endsection