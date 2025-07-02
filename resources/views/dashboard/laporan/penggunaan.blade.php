@extends('layouts.app')
@section('title', 'Laporan Penggunaan Bahan Baku')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme pb-3">
            <h3 class="fw-bold mb-0">🚀 Laporan Penggunaan Bahan (Terlaris)</h3>
            <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="bi bi-funnel"></i> Filter Tanggal
            </button>
        </div>
 
        <div class="collapse {{ request()->has('tanggal_dari') ? 'show' : '' }} mb-4" id="filterCollapse">
            <div class="card card-body">
                <form method="GET">
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

        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body p-0 table-responsive rounded-3">
                <table class="table table-striped table-borderless mb-0 rounded-3">
                    <thead style="background-color:#ca414e; color: white;">
                        <tr>
                            <th>Peringkat</th>
                            <th>Nama Bahan</th>
                            <th>Total Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bahanTerlaris as $index => $item)
                        <tr class="bg-white">
                            <td class="align-middle fw-bold">{{ $bahanTerlaris->firstItem() + $index }}</td>
                            <td class="align-middle">{{ $item->bahanBaku->nama ?? 'N/A' }}</td>
                            <td class="align-middle">{{ number_format($item->total_keluar, 0, ',', '.') }} {{ $item->bahanBaku->satuan_label ?? '' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5">
                                <p class="text-muted">Tidak ada data penggunaan pada periode yang dipilih.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $bahanTerlaris->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection