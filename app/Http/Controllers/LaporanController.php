<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * LAPORAN 1: Menampilkan laporan kerugian bahan baku.
     */
    public function kerugianBahanBaku(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        // 1. Buat query dasar untuk semua data rusak
        $query = RiwayatStok::with('bahanBaku', 'user')
            ->where('tipe_mutasi', 'rusak');

        // --- Terapkan filter tanggal hanya jika diisi ---
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        }

        // --- Hitung total kerugian dari SEMUA data yang cocok dengan filter (SEBELUM pagination) ---
        $semuaItemRusak = (clone $query)->get();
        $totalKerugian = 0;

        foreach ($semuaItemRusak as $item) {
            $hargaBeliTerakhir = RiwayatStok::where('bahan_baku_id', $item->bahan_baku_id)
                ->where('tipe_mutasi', 'masuk')
                ->where('tanggal', '<=', $item->tanggal)
                ->latest('tanggal')
                ->value('harga_satuan');

            $totalKerugian += abs($item->kuantitas) * ($hargaBeliTerakhir ?? 0);
        }

        // 2. Lakukan pagination pada query yang sama untuk ditampilkan di tabel
        $itemsRusak = $query->latest()->paginate(20)->appends($request->query());
        
        // Proses ulang item yang dipaginasi untuk menambahkan properti yang akan ditampilkan
        foreach ($itemsRusak as $item) {
             $hargaBeliTerakhir = RiwayatStok::where('bahan_baku_id', $item->bahan_baku_id)
                ->where('tipe_mutasi', 'masuk')
                ->where('tanggal', '<=', $item->tanggal)
                ->latest('tanggal')
                ->value('harga_satuan');
            $item->harga_saat_rusak = $hargaBeliTerakhir ?? 0;
            $item->nilai_kerugian = abs($item->kuantitas) * $item->harga_saat_rusak;
        }

        return view('dashboard.laporan.kerugian', compact('itemsRusak', 'totalKerugian'));
    }

    /**
     * LAPORAN 2:Menampilkan bahan baku yang paling banyak digunakan.
     */
    public function penggunaanBahan(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        $query = RiwayatStok::where('tipe_mutasi', 'produksi')
            ->select(
                'bahan_baku_id',
                DB::raw('SUM(ABS(kuantitas)) as total_keluar')
            )
            ->with('bahanBaku')
            ->groupBy('bahan_baku_id')
            ->orderBy('total_keluar', 'desc');

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        $bahanTerlaris = $query->paginate(20)->appends($request->query());

        return view('dashboard.laporan.penggunaan', compact('bahanTerlaris'));
    }

    /**
     * LAPORAN 3: Menampilkan bahan baku yang tidak terpakai (stok mati).
     */
    public function stokMati(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        // --- PERBAIKAN: Definisikan variabel tanggal ---
        $tanggalDari = $request->input('tanggal_dari');
        $tanggalSampai = $request->input('tanggal_sampai');

        $queryBahanDigunakan = RiwayatStok::where('tipe_mutasi', 'produksi');
        
        if ($tanggalDari && $tanggalSampai) {
            $queryBahanDigunakan->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
        }
        $bahanDigunakanIds = $queryBahanDigunakan->distinct()->pluck('bahan_baku_id');

        $stokMati = BahanBaku::whereNotIn('id', $bahanDigunakanIds)
            ->where('stok_terkini', '>', 0)
            ->addSelect(['terakhir_digunakan' => RiwayatStok::select('tanggal')
                ->whereColumn('bahan_baku_id', 'bahan_baku.id')
                ->where('tipe_mutasi', 'produksi')
                ->latest('tanggal')
                ->limit(1)
            ])
            ->orderBy('nama')
            ->paginate(20)->appends($request->query());

        // --- PERBAIKAN: Kirim variabel tanggal ke view ---
        return view('dashboard.laporan.stok-mati', compact('stokMati', 'tanggalDari', 'tanggalSampai'));
    }

    /**
     * LAPORAN 4: Menampilkan bahan baku yang paling banyak masuk (pembelian).
     */
    public function penerimaanBahan(Request $request)
    {
        $request->validate([
            'tanggal_dari' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_dari',
        ]);

        $query = RiwayatStok::where('tipe_mutasi', 'masuk')
            ->select(
                'bahan_baku_id',
                DB::raw('SUM(kuantitas) as total_masuk')
            )
            ->with('bahanBaku')
            ->groupBy('bahan_baku_id')
            ->orderBy('total_masuk', 'desc');

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }

        $bahanPalingMasuk = $query->paginate(20)->appends($request->query());

        return view('dashboard.laporan.penerimaan', compact('bahanPalingMasuk'));
    }
}