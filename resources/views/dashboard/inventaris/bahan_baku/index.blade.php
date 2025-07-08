@extends('layouts.app')
@section('title', 'Data Bahan Baku')

@section('content')
<div class="container pt-5">
@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif
    <div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0">📦 Filter Bahan Baku</h3>
            <button class="btn btn-theme primary" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="bi bi-funnel"></i> Tampilkan/Sembunyikan Filter
            </button>
        </div>
        <div class="collapse show mt-3" id="filterCollapse">
            <div class="card card-body">
                <form method="GET" action="{{ route('bahan-baku.index') }}">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="search" class="form-label">Cari Nama Bahan</label>
                            <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Contoh: Bawang, Ayam, Gula...">
                        </div>
                        <div class="col-md-4">
                            <label for="kategori" class="form-label">Filter Kategori</label>
                            <select name="kategori" id="kategori" class="form-select">
                                <option value="">Semua Kategori</option>
                                <option value="Bahan Makanan" {{ request('kategori') == 'Bahan Makanan' ? 'selected' : '' }}>Bahan Makanan</option>
                                <option value="Bumbu" {{ request('kategori') == 'Bumbu' ? 'selected' : '' }}>Bumbu</option>
                                <option value="Bahan Minuman" {{ request('kategori') == 'Bahan Minuman' ? 'selected' : '' }}>Bahan Minuman</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-theme info w-100"><i class="fas fa-search"></i> Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

  <x-index-table
    title="📦 Daftar Bahan Baku"
    createRoute="bahan-baku.create"
    createLabel="Tambah Bahan Baku"
    :columns="[
        ['label' => 'Nama Bahan', 'field' => 'nama'],
        ['label' => 'Kategori', 'field' => 'kategori'],
        ['label' => 'Stok Terkini', 'custom' => function($item) {
            $class = $item->stok_terkini <= $item->stok_minimum ? 'text-danger fw-bold' : '';
            return '<span class=`'.$class.'`>'. $item->stok_terkini . ' ' . $item->satuan_label . '</span>';
        }],
        ['label' => 'Stok Minimum', 'custom' => function($item) {
            return $item->stok_minimum . ' ' . $item->satuan_label;
        }],
    ]"
    :items="$items"
    :showActions="true"
    :routes="[
        'show' => 'bahan-baku.show',
        'edit' => 'bahan-baku.edit',
        (auth()->user()->role === 'admin' ? ['destroy' => 'bahan-baku.destroy'] : []),
    ]"
    routeKey="bahan_baku"
  />
@endsection