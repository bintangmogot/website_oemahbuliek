<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BahanBakuController extends Controller
{

public function index(Request $request)
{
    // Mulai dengan satu query builder
    $query = BahanBaku::query();

    // Terapkan filter pencarian jika ada
    $query->when($request->filled('search'), function ($q) use ($request) {
        return $q->where('nama', 'like', '%' . $request->search . '%');
    });

    // Terapkan filter kategori jika ada
    $query->when($request->filled('kategori'), function ($q) use ($request) {
        return $q->where('kategori', $request->kategori);
    });

    // Terapkan filter status stok jika ada, menggunakan Query Scope yang sudah dibuat di model
    // Nama input di form adalah 'stok_terkini'
    $query->when($request->filled('stok_terkini'), function ($q) use ($request) {
        // Memanggil scopeWhereStokLabel()
        return $q->whereStokLabel($request->stok_terkini);
    });

    // Lakukan paginasi pada query yang sudah difilter
    //  menjaga parameter filter saat berpindah halaman
    $bahanBakus = $query->paginate(10)->appends($request->except('page'));
    
    return view('dashboard.inventaris.bahan_baku.index', [
        'items' => $bahanBakus
    ]);
}

    public function create()
    {
        return view('dashboard.inventaris.bahan_baku.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:Bahan Makanan,Bumbu,Bahan Minuman',
            'satuan' => 'required|in:0,1',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        BahanBaku::create($request->all());

        return redirect()->route('bahan-baku.index')->with('status', 'Bahan baku berhasil ditambahkan.');
    }

    public function show(BahanBaku $bahanBaku)
    {
        // Untuk detail, kita bisa tampilkan riwayat stoknya
        $riwayat = $bahanBaku->riwayatStok()->with('user')->latest()->paginate(10);
        return view('dashboard.inventaris.bahan_baku.show', compact('bahanBaku', 'riwayat'));
    }

    public function edit(BahanBaku $bahanBaku)
    {
        return view('dashboard.inventaris.bahan_baku.edit', ['item' => $bahanBaku]);
    }

    public function update(Request $request, BahanBaku $bahanBaku)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'kategori' => 'required|in:Bahan Makanan,Bumbu,Bahan Minuman',
            'satuan' => 'required|in:0,1',
            'stok_minimum' => 'required|integer|min:0',
        ]);

        $bahanBaku->update($request->all());

        return redirect()->route('bahan-baku.index')->with('status', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        // Hanya admin yang bisa sampai ke method ini karena proteksi di route
        try {
            $bahanBaku->delete();
            return redirect()->route('bahan-baku.index')->with('status', 'Bahan baku berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('bahan-baku.index')->with('error', 'Gagal menghapus bahan baku. Mungkin masih terkait dengan data lain.');
        }
    }
}