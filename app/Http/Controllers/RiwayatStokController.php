<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\User;
use App\Notifications\StokMinimum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class RiwayatStokController extends Controller
{
    /**
     * Menampilkan halaman utama riwayat stok dengan filter.
     */
    public function index(Request $request)
    {
        $query = RiwayatStok::with(['bahanBaku', 'user'])->latest();

        // Logika penyaringan (filtering)
        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }
        if ($request->filled('tipe_mutasi')) {
            $query->where('tipe_mutasi', $request->tipe_mutasi);
        }
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_dari);
            $query->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }

        $riwayatStok = $query->paginate(15)->appends($request->except('page'));
        
        $bahanBakus = BahanBaku::orderBy('nama')->get();

        $statsQuery = RiwayatStok::query();
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $statsQuery->whereDate('tanggal', '>=', $request->tanggal_dari);
            $statsQuery->whereDate('tanggal', '<=', $request->tanggal_sampai);
        }
        
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
        $validator = Validator::make($request->all(), [
            'bahan_baku_id' => 'required|exists:bahan_baku,id',
            'tipe_mutasi' => 'required|in:masuk,produksi,rusak,penyesuaian',
            'kuantitas' => 'required|integer|min:1',
            'tanggal' => 'required|date|before_or_equal:now', 
            'total_harga' => 'nullable|required_if:tipe_mutasi,masuk|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();
        $bahanBaku = BahanBaku::findOrFail($request->bahan_baku_id);
        $kuantitas = (int)$request->kuantitas;
        $tipeMutasi = $request->tipe_mutasi;

        // Gunakan strtolower() untuk membuat pengecekan tidak case-sensitive
        if (strtolower($user->role) === 'admin') {
            // Jika Admin, transaksi langsung disetujui dan stok di-update
            return $this->prosesTransaksiDisetujui($request, $bahanBaku, $kuantitas, $tipeMutasi, $user);
        } else {
            // Jika Pegawai, transaksi dibuat sebagai 'pending' dan stok TIDAK di-update
            return $this->buatTransaksiPending($request, $bahanBaku, $kuantitas, $tipeMutasi, $user);
        }
    }

    private function prosesTransaksiDisetujui($request, $bahanBaku, $kuantitas, $tipeMutasi, $user)
    {
        if (in_array($tipeMutasi, ['produksi', 'rusak', 'penyesuaian'])) {
            if ($bahanBaku->stok_terkini < $kuantitas) {
                return response()->json(['message' => 'Stok tidak mencukupi.'], 422);
            }
        }

        DB::transaction(function () use ($request, $bahanBaku, $kuantitas, $tipeMutasi, $user) {
            $hargaSatuan = null;
            $kuantitasUntukRiwayat = $kuantitas;

            if ($tipeMutasi === 'masuk') {
                if ($kuantitas > 0) $hargaSatuan = $request->total_harga / $kuantitas;
                $bahanBaku->stok_terkini += $kuantitas;
            } else {
                $kuantitasUntukRiwayat = -$kuantitas;
                $bahanBaku->stok_terkini -= $kuantitas;
            }

            RiwayatStok::create([
                'bahan_baku_id' => $bahanBaku->id, 'user_id' => $user->id,
                'tanggal' => $request->tanggal, 'tipe_mutasi' => $tipeMutasi,
                'kuantitas' => $kuantitasUntukRiwayat, 'harga_satuan' => $hargaSatuan,
                'keterangan' => $request->keterangan, 'status' => 'approved',
            ]);

            $bahanBaku->save();

            if ($bahanBaku->stok_terkini <= $bahanBaku->stok_minimum) {
                $penerima = User::whereIn('role', ['admin', 'pegawai'])->get();
                Notification::send($penerima, new StokMinimum($bahanBaku));
            }
        });

        return response()->json(['message' => 'Transaksi berhasil dicatat dan disetujui.']);
    }

    private function buatTransaksiPending($request, $bahanBaku, $kuantitas, $tipeMutasi, $user)
    {
        $kuantitasUntukRiwayat = in_array($tipeMutasi, ['produksi', 'rusak', 'penyesuaian']) ? -$kuantitas : $kuantitas;
        
        RiwayatStok::create([
            'bahan_baku_id' => $bahanBaku->id, 'user_id' => $user->id,
            'tanggal' => $request->tanggal, 'tipe_mutasi' => $tipeMutasi,
            'kuantitas' => $kuantitasUntukRiwayat,
            'harga_satuan' => ($tipeMutasi === 'masuk' && $kuantitas > 0) ? ($request->total_harga / $kuantitas) : null,
            'keterangan' => $request->keterangan, 'status' => 'pending',
        ]);
        
        return response()->json(['message' => 'Transaksi telah diajukan dan menunggu persetujuan Admin.']);
    }

    public function indexPending(Request $request)
    {
        $query = RiwayatStok::with(['user', 'bahanBaku'])
            ->where('status', 'pending')
            ->whereHas('user', function ($q) {
            $q->where('role', 'pegawai');
            })
            ->latest();

        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tanggal', [$request->tanggal_dari, $request->tanggal_sampai]);
        }
        
        if ($request->filled('bahan_baku_id')) {
            $query->where('bahan_baku_id', $request->bahan_baku_id);
        }

        if ($request->filled('tipe_mutasi')) {
            $query->where('tipe_mutasi', $request->tipe_mutasi);
        }

        $pendingTransactions = $query->paginate(15)->appends($request->query());
        
        $bahanBakus = BahanBaku::orderBy('nama')->get();
            
        return view('dashboard.inventaris.approval.index', compact('pendingTransactions', 'bahanBakus'));
    }

    public function approve(RiwayatStok $riwayatStok)
    {
        if ($riwayatStok->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($riwayatStok) {
            $bahanBaku = $riwayatStok->bahanBaku;
            
            $bahanBaku->stok_terkini += $riwayatStok->kuantitas;
            $bahanBaku->save();

            $riwayatStok->status = 'approved';
            $riwayatStok->save();

            if ($bahanBaku->stok_terkini <= $bahanBaku->stok_minimum) {
                $penerima = User::whereIn('role', ['admin', 'pegawai'])->get();
                Notification::send($penerima, new StokMinimum($bahanBaku));
            }
        });

        return back()->with('status', 'Transaksi berhasil disetujui dan stok telah diperbarui.');
    }

    public function reject(RiwayatStok $riwayatStok)
    {
        if ($riwayatStok->status !== 'pending') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $riwayatStok->delete();

        return back()->with('status', 'Pengajuan transaksi berhasil ditolak dan dihapus.');
    }
}