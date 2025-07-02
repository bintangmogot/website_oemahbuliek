@extends('layouts.app')
@section('title', 'Detail Bahan Baku: ' . $bahanBaku->nama)

@section('content')
<div class="container py-5">
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
        
        {{-- Header --}}
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-3 mb-4 card-header-theme pb-3">
            <h3 class="fw-bold mb-0">📦 Detail: {{ $bahanBaku->nama }}</h3>
            <div class="d-flex gap-2">
                <a href="{{ route('riwayat-stok.create') }}" class="btn btn-yellow">
                    <i class="fas fa-plus"></i> Catat Transaksi Stok
                </a>
            <a href="{{ route('bahan-baku.index') }}" class="btn btn-theme primary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            </div>
        </div>

        {{-- Detail Info --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Stok Terkini</h5>
                        <p class="card-text fs-2 fw-bold {{ $bahanBaku->stok_terkini <= $bahanBaku->stok_minimum ? 'text-danger' : 'text-success' }}">
                            {{ $bahanBaku->stok_terkini }} <small class="fs-5">{{ $bahanBaku->satuan_label }}</small>
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                 <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Kategori</h5>
                        <p class="card-text">{{ $bahanBaku->kategori }}</p>
                        <h5 class="card-title mt-3">Stok Minimum</h5>
                        <p class="card-text">{{ $bahanBaku->stok_minimum }} {{ $bahanBaku->satuan_label }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Riwayat Stok --}}
        <h4 class="fw-bold mt-5 mb-3">Riwayat Mutasi Stok</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Kuantitas</th>
                        <th>Dicatat Oleh</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                    <tr>
                        <td>{{ $item->tanggal->format('d M Y, H:i') }}</td>
                        <td><span class="badge bg-{{ $item->tipe_mutasi == 'masuk' ? 'success' : 'warning' }}">{{ Str::ucfirst($item->tipe_mutasi) }}</span></td>
                        <td class="{{ $item->kuantitas > 0 ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $item->kuantitas }} {{ $bahanBaku->satuan_label }}
                        </td>
                        <td>{{ $item->user->nama_lengkap ?? 'N/A' }}</td>
                        <td>{{ $item->keterangan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada riwayat stok untuk item ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end mt-3">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>
@endsection
