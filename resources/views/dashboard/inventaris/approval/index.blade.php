@extends('layouts.app')
@section('title', 'Persetujuan Transaksi Stok')

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        {{-- Header dengan Tombol Filter --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-3 card-header-theme pb-3">
            <h3 class="fw-bold mb-0">✔️ Persetujuan Transaksi Stok</h3>
            <button class="btn btn-theme primary py-2 px-3" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
        
        <div class="collapse show mb-4" id="filterCollapse">
            <div class="card card-body">
                <form method="GET">
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
                                <option value="penyesuaian" {{ request('tipe_mutasi') == 'penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
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
                            <button type="submit" class="btn btn-theme info w-100"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <x-session-status/>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Pegawai</th>
                        <th>Bahan Baku</th>
                        <th>Tipe</th>
                        <th>Kuantitas</th>
                        <th>Keterangan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingTransactions as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y, H:i') }}</td>
                        <td>{{ $item->user->nama_lengkap ?? 'N/A' }}</td>
                        <td>{{ $item->bahanBaku->nama ?? 'N/A' }}</td>
                        <td><span class="badge bg-info">{{ Str::ucfirst($item->tipe_mutasi) }}</span></td>
                        <td class="fw-bold {{ $item->kuantitas > 0 ? 'text-success' : 'text-danger' }}">{{ $item->kuantitas }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <form action="{{ route('stok.approve', $item->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menyetujui transaksi ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">Terima</button>
                                </form>
                                <form action="{{ route('stok.reject', $item->id) }}" method="POST" onsubmit="return confirm('Anda yakin ingin menolak transaksi ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm ms-1">Tolak</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">Tidak ada transaksi yang menunggu persetujuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $pendingTransactions->links() }}
        </div>
    </div>
</div>
@endsection
