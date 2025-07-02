<?php

namespace App\Http\Controllers;

use App\Models\RiwayatStok;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

        // 1. Ambil semua data bahan rusak sesuai filter tanggal
        $query = RiwayatStok::with('bahanBaku', 'user')
            ->where('tipe_mutasi', 'rusak')
            ->latest();

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $itemsRusak = $query->paginate(20);
        $totalKerugian = 0;

        // 2. Proses setiap item untuk menghitung nilai kerugiannya
        foreach ($itemsRusak as $item) {
            // Cari harga beli terakhir untuk bahan ini sebelum atau pada tanggal rusak
            $hargaBeliTerakhir = RiwayatStok::where('bahan_baku_id', $item->bahan_baku_id)
                ->where('tipe_mutasi', 'masuk')
                ->where('tanggal', '<=', $item->tanggal)
                ->latest('tanggal')
                ->value('harga_satuan'); // Ambil hanya nilai harga_satuan

            // Jika tidak ditemukan harga beli (misal, data stok awal), anggap harga 0
            $hargaBeliTerakhir = $hargaBeliTerakhir ?? 0;

            // Hitung nilai kerugian (kuantitas disimpan sebagai negatif, jadi kita gunakan abs())
            $nilaiKerugian = abs($item->kuantitas) * $hargaBeliTerakhir;

            // Tambahkan properti baru ke item untuk ditampilkan di view
            $item->harga_saat_rusak = $hargaBeliTerakhir;
            $item->nilai_kerugian = $nilaiKerugian;

            // Akumulasi total kerugian
            $totalKerugian += $nilaiKerugian;
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
                DB::raw('SUM(ABS(kuantitas)) as total_keluar') // Jumlahkan total keluar (gunakan ABS karena kuantitas negatif)
            )
            ->with('bahanBaku') // Eager load untuk mendapatkan nama bahan
            ->groupBy('bahan_baku_id')
            ->orderBy('total_keluar', 'desc'); // Urutkan dari yang paling banyak keluar

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $bahanTerlaris = $query->paginate(20);

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

        // Tentukan rentang tanggal, default 30 hari terakhir jika tidak diisi
        $tanggalDari = $request->input('tanggal_dari', now()->subDays(30)->toDateString());
        $tanggalSampai = $request->input('tanggal_sampai', now()->toDateString());

        // 1. Ambil semua ID bahan baku yang DIGUNAKAN pada periode tersebut
        $bahanDigunakanIds = RiwayatStok::where('tipe_mutasi', 'produksi')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->distinct()
            ->pluck('bahan_baku_id');

        // 2. Cari bahan baku yang TIDAK ADA di daftar ID di atas, dan masih punya stok
        $stokMati = BahanBaku::whereNotIn('id', $bahanDigunakanIds)
            ->where('stok_terkini', '>', 0) // Hanya tampilkan yang masih ada stoknya
            ->orderBy('nama')
            ->paginate(20);

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
                DB::raw('SUM(kuantitas) as total_masuk') // Jumlahkan total masuk (kuantitas sudah positif)
            )
            ->with('bahanBaku') // Eager load untuk mendapatkan nama bahan
            ->groupBy('bahan_baku_id')
            ->orderBy('total_masuk', 'desc'); // Urutkan dari yang paling banyak masuk

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $bahanPalingMasuk = $query->paginate(20);

        return view('dashboard.laporan.penerimaan', compact('bahanPalingMasuk'));
    }

}