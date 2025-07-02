@extends('layouts.app')
@section('title', 'Riwayat Mutasi Stok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 80vh">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme pb-3">
            <h3 class="fw-bold mb-0">📜 Riwayat Mutasi Stok</h3>
            <div class="d-flex gap-2">
                <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                    <i class="bi bi-funnel" style="font-size: 1.2rem"></i> Filter
                </button>
                <a href="{{ route('riwayat-stok.create') }}" class="btn btn-yellow">
                    <i class="fas fa-plus"></i> Catat Transaksi Stok
                </a>
            </div>
        </div>
 
        {{-- Filter Section --}}
        <div class="collapse mb-3" id="filterCollapse">
            <div class="card card-body">
                <form method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Bahan Baku</label>
                            <select name="bahan_baku_id" class="form-select">
                                <option value="">Semua Bahan</option>
                                @foreach($bahanBakus as $bahan)
                                <option value="{{ $bahan->id }}" {{ request('bahan_baku_id') == $bahan->id ? 'selected' : '' }}>
                                    {{ $bahan->nama }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Tipe Mutasi</label>
                            <select name="tipe_mutasi" class="form-select">
                                <option value="">Semua Tipe</option>
                                <option value="masuk" {{ request('tipe_mutasi') == 'masuk' ? 'selected' : '' }}>Masuk</option>
                                <option value="produksi" {{ request('tipe_mutasi') == 'produksi' ? 'selected' : '' }}>Produksi</option>
                                <option value="rusak" {{ request('tipe_mutasi') == 'rusak' ? 'selected' : '' }}>Rusak</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme info me-2 p-2 px-3">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Statistics Cards --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-success text-white"><div class="card-body">
                    <h4>{{ $totalMasuk }}</h4><p>Transaksi Masuk</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark"><div class="card-body">
                    <h4>{{ $totalProduksi }}</h4><p>Transaksi Produksi</p>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white"><div class="card-body">
                    <h4>{{ $totalRusak }}</h4><p>Transaksi Barang Rusak</p>
                </div></div>
            </div>
        </div>

        {{-- Table --}}
        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body p-0 table-responsive rounded-3" style="min-height: 50vh">
                <table class="table table-striped table-borderless mb-0 rounded-3">
                    <thead style="background-color:#ca414e; color: white;">
                        <tr>
                            <th>Tanggal</th>
                            <th>Bahan Baku</th>
                            <th>Tipe</th>
                            <th>Kuantitas</th>
                            <th>Harga Satuan (Beli)</th>
                            <th>Dicatat Oleh</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayatStok as $item)
                        <tr class="bg-white">
                            <td class="align-middle">{{ $item->tanggal->format('d M Y, H:i') }}</td>
                            <td class="align-middle">{{ $item->bahanBaku->nama ?? 'N/A' }}</td>
                            <td class="align-middle">
                                <span class="badge bg-{{ $item->tipe_mutasi == 'masuk' ? 'success' : ($item->tipe_mutasi == 'produksi' ? 'warning' : 'danger') }}">
                                    {{ Str::ucfirst($item->tipe_mutasi) }}
                                </span>
                            </td>
                            <td class="align-middle fw-bold {{ $item->kuantitas > 0 ? 'text-success' : 'text-danger' }}">
                                {{ $item->kuantitas }} {{ $item->bahanBaku->satuan_label ?? '' }}
                            </td>
                            <td class="align-middle">
                                {{ $item->harga_satuan ? 'Rp ' . number_format($item->harga_satuan, 2, ',', '.') : '-' }}
                            </td>
                            <td class="align-middle">{{ $item->user->nama_lengkap ?? 'N/A' }}</td>
                            <td class="align-middle">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada data riwayat stok.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $riwayatStok->links() }}
        </div>
    </div>
</div>
@endsection