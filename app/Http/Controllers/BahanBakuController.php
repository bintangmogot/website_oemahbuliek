<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $query = BahanBaku::query();

        // START: Logika Filter dan Pencarian
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        // END: Logika Filter dan Pencarian

        $bahanBakus = $query->paginate(10);
        
        // Menggunakan appends() di view akan lebih konsisten
        // $bahanBakus->appends($request->query());

        return view('dashboard.inventaris.bahan_baku.index', ['items' => $bahanBakus]);
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