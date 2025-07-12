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

    // Filter berdasarkan tipe lembur - MENGGUNAKAN SCOPE
// Statistik berdasarkan filter yang sama
$baseQuery = GajiLembur::query();
// Terapkan filter yang sama seperti query utama
        // --- FILTER LOGIC ---
        if ($request->filled('tipe_lembur')) {
            $query->where('tipe_lembur', $request->tipe_lembur);
            $baseQuery->where('tipe_lembur', $request->tipe_lembur);
        }   
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
            $baseQuery->where('status_pembayaran', $request->status_pembayaran);
        }
        if ($request->filled('user_id')) {
            $query->where('users_id', $request->user_id);
            $baseQuery->where('users_id', $request->user_id);
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tgl_lembur', '>=', $request->tanggal_dari);
            $baseQuery->whereDate('tgl_lembur', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tgl_lembur', '<=', $request->tanggal_sampai);
            $baseQuery->whereDate('tgl_lembur', '<=', $request->tanggal_sampai);
        }

        // --- STATISTICS CALCULATION ---
        $totalUnpaid = (clone $baseQuery)->where('status_pembayaran', 0)->sum('total_gaji_lembur');
        $totalPaid = (clone $baseQuery)->where('status_pembayaran', 1)->sum('total_gaji_lembur');
        $countUnpaid = (clone $baseQuery)->where('status_pembayaran', 0)->count();

        // Statistik berdasarkan tipe lembur yang spesifik
        $shiftLemburCount = (clone $baseQuery)->where('tipe_lembur', 'shift_lembur')->count();
        $overtimeCount = (clone $baseQuery)->where('tipe_lembur', 'overtime')->count();
        $shiftLemburOvertimeCount = (clone $baseQuery)->where('tipe_lembur', 'shift_lembur_overtime')->count();

        $gajiLembur = $query->paginate(20)->appends($request->query());
        $users = User::where('role', 'pegawai')->orderBy('nama_lengkap')->get();

        return view('dashboard.gaji-lembur.admin.index', compact(
            'gajiLembur',
            'totalUnpaid',
            'totalPaid',
            'countUnpaid',
            'shiftLemburCount',
            'overtimeCount',
            'shiftLemburOvertimeCount',
            'users'
        ));
    }

    public function detailPegawai(Request $request, $userId)
    {
        $pegawai = User::findOrFail($userId);
        
        $tanggalMulai = $request->get('tanggal_mulai');
        $tanggalSelesai = $request->get('tanggal_selesai');
        $statusPembayaran = $request->get('status_pembayaran'); 
        
        // Query gaji lembur dengan pagination
            $gajiLemburQuery = GajiLembur::with(['presensi.jadwalShift.shift']) // <-- Eager load relasi
            ->where('users_id', $userId)
            ->when($tanggalMulai, function($query) use ($tanggalMulai) {
                return $query->where('tgl_lembur', '>=', $tanggalMulai);
            })
            ->when($tanggalSelesai, function($query) use ($tanggalSelesai) {
                return $query->where('tgl_lembur', '<=', $tanggalSelesai);
            })
            ->when($statusPembayaran !== null, function($query) use ($statusPembayaran) {
                return $query->where('status_pembayaran', $statusPembayaran);
            })
            ->orderBy('tgl_lembur', 'DESC');
        
        $gajiLembur = $gajiLemburQuery->paginate(15);
        
        // Statistik
        $statistik = GajiLembur::where('users_id', $userId)
            ->when($tanggalMulai, function($query) use ($tanggalMulai) {
                return $query->where('tgl_lembur', '>=', $tanggalMulai);
            })
            ->when($tanggalSelesai, function($query) use ($tanggalSelesai) {
                return $query->where('tgl_lembur', '<=', $tanggalSelesai);
            })
            ->when($statusPembayaran !== null, function($query) use ($statusPembayaran) {
                return $query->where('status_pembayaran', $statusPembayaran);
            })
            ->selectRaw('
                COUNT(DISTINCT DATE(tgl_lembur)) as total_hari,
                SUM(total_jam_lembur) as total_jam,
                SUM(CASE WHEN status_pembayaran = 1 THEN total_gaji_lembur ELSE 0 END) as total_sudah_dibayar,
                SUM(CASE WHEN status_pembayaran != 1 THEN total_gaji_lembur ELSE 0 END) as total_belum_dibayar
            ')
            ->first();
        
        return view('dashboard.gaji-lembur.detail-pegawai', compact(
            'pegawai',
            'gajiLembur',
            'statistik',
            'tanggalMulai',
            'tanggalSelesai'
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
    $tanggalMulai = $request->get('tanggal_mulai');
    $tanggalSelesai = $request->get('tanggal_selesai');
    $statusPembayaran = $request->get('status_pembayaran');
    
    // Query laporan per pegawai dengan agregasi
    $laporanPerPegawai = DB::table('gaji_lembur')
        ->join('users', 'gaji_lembur.users_id', '=', 'users.id')
        ->select([
            'users.id as user_id',
            'users.nama_lengkap',
            'users.jabatan',
            DB::raw('COUNT(DISTINCT DATE(gaji_lembur.tgl_lembur)) as total_hari_lembur'),
            DB::raw('SUM(gaji_lembur.total_jam_lembur) as total_jam'),
            DB::raw('SUM(gaji_lembur.total_gaji_lembur) as total_gaji'),
            DB::raw('SUM(CASE WHEN gaji_lembur.status_pembayaran = 1 THEN gaji_lembur.total_gaji_lembur ELSE 0 END) as total_sudah_dibayar'),
            DB::raw('SUM(CASE WHEN gaji_lembur.status_pembayaran != 1 THEN gaji_lembur.total_gaji_lembur ELSE 0 END) as total_belum_dibayar')
        ])
        ->when($tanggalMulai, function($query) use ($tanggalMulai) {
            return $query->where('gaji_lembur.tgl_lembur', '>=', $tanggalMulai);
        })
        ->when($tanggalSelesai, function($query) use ($tanggalSelesai) {
            return $query->where('gaji_lembur.tgl_lembur', '<=', $tanggalSelesai);
        })
        ->when($statusPembayaran !== null, function($query) use ($statusPembayaran) {
            return $query->where('gaji_lembur.status_pembayaran', $statusPembayaran);
        })
        ->groupBy('users.id', 'users.nama_lengkap', 'users.jabatan')
        ->orderBy('total_gaji', 'DESC')
        ->get();
    
    // Total keseluruhan
    $totalKeseluruhan = DB::table('gaji_lembur')
        ->select([
            DB::raw('COUNT(*) as total_record'),
            DB::raw('SUM(total_jam_lembur) as total_jam'),
            DB::raw('SUM(total_gaji_lembur) as total_gaji'),
            DB::raw('SUM(CASE WHEN status_pembayaran = 1 THEN total_gaji_lembur ELSE 0 END) as total_sudah_dibayar'),
            DB::raw('SUM(CASE WHEN status_pembayaran != 1 THEN total_gaji_lembur ELSE 0 END) as total_belum_dibayar')
        ])
        ->when($tanggalMulai, function($query) use ($tanggalMulai) {
            return $query->where('tgl_lembur', '>=', $tanggalMulai);
        })
        ->when($tanggalSelesai, function($query) use ($tanggalSelesai) {
            return $query->where('tgl_lembur', '<=', $tanggalSelesai);
        })
        ->when($statusPembayaran !== null, function($query) use ($statusPembayaran) {
            return $query->where('status_pembayaran', $statusPembayaran);
        })
        ->first();
    
    $statusPembayaranLabel = 'Semua Status'; // Default label
    if ($statusPembayaran !== null && $statusPembayaran !== '') {
        $gajiLemburModel = new GajiLembur();
        $gajiLemburModel->status_pembayaran = $statusPembayaran;
        // Tidak perlu str_replace jika hanya untuk tampilan di view
        $statusPembayaranLabel = $gajiLemburModel->status_pembayaran_label; 
    }

    return view('dashboard.gaji-lembur.laporan', compact(
        'laporanPerPegawai', 
        'totalKeseluruhan',
        'tanggalMulai',
        'tanggalSelesai',
        'statusPembayaranLabel'
    ));

    }
}