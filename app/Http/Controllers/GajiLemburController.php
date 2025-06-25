<?php

namespace App\Http\Controllers;

use App\Models\GajiLembur;
use App\Models\Presensi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GajiLemburController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['index', 'approve', 'reject', 'updatePayment']);
        $this->middleware('role:pegawai')->only(['pegawaiIndex']);
    }

    /**
     * Tampilan untuk Admin - Lihat semua data gaji lembur
     */
    public function index(Request $request)
    {
        $query = GajiLembur::with(['user', 'presensi.jadwalShift.shift'])
            ->orderBy('tgl_lembur', 'desc')
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->byStatusPembayaran($request->status_pembayaran);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_dari') && $request->filled('tanggal_sampai')) {
            $query->whereBetween('tgl_lembur', [$request->tanggal_dari, $request->tanggal_sampai]);
        } elseif ($request->filled('tanggal_dari')) {
            $query->whereDate('tgl_lembur', '>=', $request->tanggal_dari);
        } elseif ($request->filled('tanggal_sampai')) {
            $query->whereDate('tgl_lembur', '<=', $request->tanggal_sampai);
        }

        // Filter berdasarkan bulan dan tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->byBulanTahun($request->bulan, $request->tahun);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        $gajiLembur = $query->paginate(20);
        
        // Statistik
        $totalUnpaid = GajiLembur::unpaid()->sum('total_gaji_lembur');
        $totalPaid = GajiLembur::paid()->sum('total_gaji_lembur');
        $countUnpaid = GajiLembur::unpaid()->count();

        // Data untuk filter
        $users = User::where('role', 'pegawai')->get(['id', 'nama_lengkap']);

        return view('dashboard.gaji-lembur.admin.index', compact(
            'gajiLembur', 
            'totalUnpaid', 
            'totalPaid', 
            'countUnpaid',
            'users'
        ));
    }

    /**
     * Tampilan untuk Pegawai - Lihat gaji lembur sendiri
     */
    public function pegawaiIndex(Request $request)
    {
        $userId = Auth::id();
        
        $query = GajiLembur::with(['presensi.jadwalShift.shift'])
            ->byUser($userId)
            ->orderBy('tgl_lembur', 'desc');

        // Filter berdasarkan bulan dan tahun
        if ($request->filled('bulan') && $request->filled('tahun')) {
            $query->byBulanTahun($request->bulan, $request->tahun);
        } else {
            // Default bulan ini
            $query->byBulanTahun(Carbon::now()->month, Carbon::now()->year);
        }

        $gajiLembur = $query->paginate(15);
        
        // Statistik untuk pegawai
        $bulanIni = Carbon::now();
        $totalLemburBulanIni = GajiLembur::byUser($userId)
            ->byBulanTahun($bulanIni->month, $bulanIni->year)
            ->sum('total_gaji_lembur');
        
        $totalJamLemburBulanIni = GajiLembur::byUser($userId)
            ->byBulanTahun($bulanIni->month, $bulanIni->year)
            ->sum('total_jam_lembur');

        $totalBelumDibayar = GajiLembur::byUser($userId)->unpaid()->sum('total_gaji_lembur');

        return view('dashboard.gaji-lembur.pegawai.index', compact(
            'gajiLembur',
            'totalLemburBulanIni',
            'totalJamLemburBulanIni', 
            'totalBelumDibayar'
        ));
    }

    /**
     * Detail gaji lembur
     */
    public function show(GajiLembur $gajiLembur)
    {
        // Validasi akses pegawai
        if (Auth::user()->role === 'pegawai' && $gajiLembur->users_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $gajiLembur->load(['user', 'presensi.jadwalShift.shift']);

        return view('dashboard.gaji-lembur.show', compact('gajiLembur'));
    }

    /**
     * Update status pembayaran (Admin only)
     */
    public function updatePayment(Request $request, GajiLembur $gajiLembur)
    {
        $request->validate([
            'status_pembayaran' => 'required|in:0,1,2',
            'tgl_bayar' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'status_pembayaran' => $request->status_pembayaran
            ];

            // Set tanggal bayar jika status dibayar
            if ($request->status_pembayaran == GajiLembur::STATUS_PEMBAYARAN_PAID) {
                $updateData['tgl_bayar'] = $request->tgl_bayar ?? Carbon::now()->toDateString();
            } elseif ($request->status_pembayaran == GajiLembur::STATUS_PEMBAYARAN_UNPAID) {
                $updateData['tgl_bayar'] = null;
            }

            $gajiLembur->update($updateData);

            DB::commit();

            $message = match($request->status_pembayaran) {
                '1' => 'Gaji lembur berhasil ditandai sebagai sudah dibayar',
                '2' => 'Gaji lembur berhasil ditandai sebagai dibayar sebagian',
                '0' => 'Gaji lembur berhasil ditandai sebagai belum dibayar',
                default => 'Status pembayaran berhasil diupdate'
            };

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Batch update pembayaran untuk multiple records
     */
    public function batchUpdatePayment(Request $request)
    {
        $request->validate([
            'gaji_lembur_ids' => 'required|array',
            'gaji_lembur_ids.*' => 'exists:gaji_lembur,id',
            'status_pembayaran' => 'required|in:0,1,2',
            'tgl_bayar' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'status_pembayaran' => $request->status_pembayaran
            ];

            // Set tanggal bayar jika status dibayar
            if ($request->status_pembayaran == GajiLembur::STATUS_PEMBAYARAN_PAID) {
                $updateData['tgl_bayar'] = $request->tgl_bayar ?? Carbon::now()->toDateString();
            } elseif ($request->status_pembayaran == GajiLembur::STATUS_PEMBAYARAN_UNPAID) {
                $updateData['tgl_bayar'] = null;
            }

            $affected = GajiLembur::whereIn('id', $request->gaji_lembur_ids)->update($updateData);

            DB::commit();

            return redirect()->back()->with('success', "Berhasil mengupdate {$affected} record gaji lembur");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    /**
     * Laporan ringkas gaji lembur per periode
     */
    public function laporan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Ringkasan per pegawai
        $laporanPerPegawai = GajiLembur::select(
                'users_id',
                DB::raw('SUM(total_jam_lembur) as total_jam'),
                DB::raw('SUM(total_gaji_lembur) as total_gaji'),
                DB::raw('COUNT(*) as total_hari_lembur'),
                DB::raw('SUM(CASE WHEN status_pembayaran = 1 THEN total_gaji_lembur ELSE 0 END) as total_sudah_dibayar'),
                DB::raw('SUM(CASE WHEN status_pembayaran = 0 THEN total_gaji_lembur ELSE 0 END) as total_belum_dibayar')
            )
            ->with('user:id,nama_lengkap,jabatan')
            ->byBulanTahun($bulan, $tahun)
            ->groupBy('users_id')
            ->get();

        // Total keseluruhan
        $totalKeseluruhan = GajiLembur::byBulanTahun($bulan, $tahun)
            ->select(
                DB::raw('SUM(total_jam_lembur) as total_jam'),
                DB::raw('SUM(total_gaji_lembur) as total_gaji'),
                DB::raw('COUNT(*) as total_record'),
                DB::raw('SUM(CASE WHEN status_pembayaran = 1 THEN total_gaji_lembur ELSE 0 END) as total_sudah_dibayar'),
                DB::raw('SUM(CASE WHEN status_pembayaran = 0 THEN total_gaji_lembur ELSE 0 END) as total_belum_dibayar')
            )
            ->first();

        return view('dashboard.gaji-lembur.laporan', compact(
            'laporanPerPegawai',
            'totalKeseluruhan',
            'bulan',
            'tahun'
        ));
    }
}