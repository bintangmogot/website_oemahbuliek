<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Notifications\StokMinimum;
use Illuminate\Support\Facades\Notification;

class RiwayatStokController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatStok::with(['bahanBaku', 'user'])->latest();

        // Filtering logic
        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }
        if ($request->filled('tipe_mutasi')) {
            $query->where('tipe_mutasi', $request->tipe_mutasi);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        // FIX: Menghapus withQueryString(). Penanganan query string akan dilakukan di view.
        $riwayatStok = $query->paginate(15);
        
        $bahanBakus = BahanBaku::orderBy('nama')->get();

        // Summary Cards Data
        $statsQuery = RiwayatStok::query();
        if ($request->filled('tanggal_dari')) $statsQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
        if ($request->filled('tanggal_sampai')) $statsQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
        
        $totalMasuk = (clone $statsQuery)->where('tipe_mutasi', 'masuk')->count();
        $totalProduksi = (clone $statsQuery)->where('tipe_mutasi', 'produksi')->count();
        $totalRusak = (clone $statsQuery)->where('tipe_mutasi', 'rusak')->count();
        
        return view('dashboard.inventaris.riwayat_stok.index', compact('riwayatStok', 'bahanBakus', 'totalMasuk', 'totalProduksi', 'totalRusak'));
    }

    public function create()
    {
        $bahanBakus = BahanBaku::orderBy('nama')->get();
        return view('dashboard.inventaris.riwayat_stok.create', compact('bahanBakus'));
    }

    public function store(Request $request)
    {
        // Gunakan Validator manual untuk kontrol penuh atas respons
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'tipe_mutasi' => 'required|in:masuk,produksi,rusak,penyesuaian',
            'kuantitas' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'total_harga' => 'nullable|required_if:tipe_mutasi,masuk|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        // Jika validasi gagal, kembalikan error sebagai JSON
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $bahanBaku = BahanBaku::findOrFail($request->bahan_baku_id);
            $kuantitas = (int)$request->kuantitas;
            $tipeMutasi = $request->tipe_mutasi;

            // Validasi stok jika keluar (custom validation)
            if (in_array($tipeMutasi, ['produksi', 'rusak', 'penyesuaian'])) {
                if ($bahanBaku->stok_terkini < $kuantitas) {
                    // Kembalikan error spesifik sebagai JSON
                    return response()->json([
                        'message' => 'Stok tidak mencukupi.',
                        'errors' => ['kuantitas' => ['Stok tidak mencukupi. Stok saat ini: ' . $bahanBaku->stok_terkini . ' ' . $bahanBaku->satuan_label]]
                    ], 422);
                }
            }

            DB::transaction(function () use ($request, $bahanBaku, $kuantitas, $tipeMutasi) {
                $hargaSatuan = null;
                $kuantitasUntukRiwayat = $kuantitas;

                if ($tipeMutasi === 'masuk') {
                    if ($kuantitas > 0) {
                        $hargaSatuan = $request->total_harga / $kuantitas;
                    }
                    $bahanBaku->stok_terkini += $kuantitas;
                } 
                elseif (in_array($tipeMutasi, ['produksi', 'rusak', 'penyesuaian'])) {
                    $kuantitasUntukRiwayat = -$kuantitas; 
                    $bahanBaku->stok_terkini -= $kuantitas;
                }

                RiwayatStok::create([
                    'bahan_baku_id' => $bahanBaku->id,
                    'user_id' => Auth::id(),
                    'tanggal' => $request->tanggal,
                    'tipe_mutasi' => $tipeMutasi,
                    'kuantitas' => $kuantitasUntukRiwayat,
                    'harga_satuan' => $hargaSatuan,
                    'keterangan' => $request->keterangan,
                ]);

                $bahanBaku->save();

                if (in_array($request->tipe_mutasi, ['produksi', 'rusak', 'penyesuaian'])) {
                    if ($bahanBaku->stok_terkini <= $bahanBaku->stok_minimum) {
                        $penerima = User::whereIn('role', ['admin', 'pegawai'])->get();
                        Notification::send($penerima, new StokMinimum($bahanBaku));
                    }
                }
            });

        } catch (\Exception $e) {
            // Jika ada error lain, kembalikan sebagai JSON
            return response()->json(['message' => 'Terjadi error saat menyimpan data: ' . $e->getMessage()], 500);
        }

        // Jika berhasil, kembalikan pesan sukses sebagai JSON
        return response()->json(['status' => 'success', 'message' => 'Transaksi berhasil dicatat!']);
    }
}
