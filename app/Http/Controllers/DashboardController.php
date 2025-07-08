<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\RiwayatStok;
use App\Models\User;
use App\Models\Presensi;
use App\Models\JadwalShift;
use App\Models\GajiLembur;
use App\Models\GajiPokok;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $viewData = [];

        if ($user->role === 'admin') {
            // --- DATA UNTUK ADMIN ---
            
            // Data Inventaris
            $viewData['jumlahBahanBaku'] = BahanBaku::count();
            $viewData['totalKerugianBulanIni'] = $this->hitungTotalKerugianBulanIni();
            $viewData['stokKritis'] = BahanBaku::whereRaw('stok_terkini <= stok_minimum')->where('stok_terkini', '>', 0)->limit(5)->get();
            $viewData['stokHabis'] = BahanBaku::where('stok_terkini', '=', 0)->limit(5)->get();
            $viewData['aktivitasHariIni'] = RiwayatStok::whereDate('created_at', today())->selectRaw('tipe_mutasi, count(*) as total')->groupBy('tipe_mutasi')->pluck('total', 'tipe_mutasi');

            // Data SDM & Penggajian (Integrasi)
            $viewData['jumlahPegawai'] = User::where('role', 'pegawai')->count();
            $viewData['pendingApprovalCount'] = Presensi::where('status_approval', Presensi::STATUS_APPROVAL_PENDING)->count();
            $presensiHariIni = Presensi::whereDate('tgl_presensi', today())->get();
            $viewData['hadirHariIni'] = $presensiHariIni->whereNotNull('jam_masuk')->count();
            $viewData['terlambatHariIni'] = $presensiHariIni->where('menit_terlambat', '>', 0)->count();
            $viewData['jadwalHariIni'] = JadwalShift::with(['user', 'shift'])->whereDate('tanggal', today())->get();
            $viewData['pegawaiDijadwalkan'] = $viewData['jadwalHariIni']->count();
            $viewData['totalLemburBelumDibayar'] = GajiLembur::where('status_pembayaran', 0)->whereYear('tgl_lembur', now()->year)->whereMonth('tgl_lembur', now()->month)->sum('total_gaji_lembur');
            $queryGajiPokokBulanIni = GajiPokok::whereYear('periode_start', now()->year)->whereMonth('periode_start', now()->month);
            $viewData['totalGajiPokokBulanIni'] = (clone $queryGajiPokokBulanIni)->sum('total_gaji_pokok');
            $viewData['totalGajiPokokBelumDibayar'] = (clone $queryGajiPokokBulanIni)->where('status_pembayaran', GajiPokok::STATUS_UNPAID)->sum('total_gaji_pokok');

        } else {
            // --- DATA UNTUK PEGAWAI ---
            $userId = $user->id;

        // 1. Cari jadwal aktif hari ini
        // Ambil semua ID jadwal milik user hari ini
        $jadwalIdsHariIni = JadwalShift::where('users_id', $userId)
            ->whereDate('tanggal', today())
            ->pluck('id');

        // Dari ID di atas, cari mana yang sudah selesai (sudah checkout)
        $completedJadwalIds = Presensi::whereIn('jadwal_shift_id', $jadwalIdsHariIni)
            ->whereNotNull('jam_keluar')
            ->pluck('jadwal_shift_id');

        // Cari jadwal aktif dengan urutan berdasarkan JAM SHIFT (yang belum selesai)
        $viewData['jadwalAktifHariIni'] = JadwalShift::with('shift')
            // 1. Pilih kolom dari tabel utama untuk menghindari konflik
            ->select('jadwal_shift.*') 
            // 2. Gabungkan dengan tabel shift
            ->join('shift', 'jadwal_shift.shift_id', '=', 'shift.id') 
            ->where('jadwal_shift.users_id', $userId)
            ->whereDate('jadwal_shift.tanggal', today())
            ->whereNotIn('jadwal_shift.id', $completedJadwalIds)
            // 3. Urutkan berdasarkan jam mulai shift
            ->orderBy('shift.jam_mulai', 'asc') 
            ->first();

        // Ambil semua jadwal berikutnya
        $queryJadwalBerikutnya = JadwalShift::with('shift')
            ->select('jadwal_shift.*') // Pilih kolom
            ->join('shift', 'jadwal_shift.shift_id', '=', 'shift.id') // Gabungkan tabel
            ->where('jadwal_shift.users_id', $userId)
            ->where('jadwal_shift.tanggal', '>=', today())
            ->orderBy('jadwal_shift.tanggal', 'asc') // Urutkan berdasarkan tanggal dulu
            ->orderBy('shift.jam_mulai', 'asc');   // Lalu urutkan berdasarkan jam

        // Buat daftar ID yang harus dikecualikan
        // Isinya adalah jadwal yang sudah selesai DAN jadwal yang sedang aktif
        $excludeIds = $completedJadwalIds->toArray();
        if ($viewData['jadwalAktifHariIni']) {
            $excludeIds[] = $viewData['jadwalAktifHariIni']->id;
        }

        // Terapkan filter pengecualian jika ada
        if (!empty($excludeIds)) {
            $queryJadwalBerikutnya->whereNotIn('jadwal_shift.id', $excludeIds);
        }

            $viewData['semuaJadwalBerikutnya'] = $queryJadwalBerikutnya->limit(10)->get();

            // Data Gaji
            $viewData['gajiLemburBelumDibayar'] = GajiLembur::where('users_id', $userId)->where('status_pembayaran', 0)->sum('total_gaji_lembur');
            $viewData['gajiPokokBelumDibayar'] = GajiPokok::where('users_id', $userId)->where('status_pembayaran', GajiPokok::STATUS_UNPAID)->sum('total_gaji_pokok');
        
            // Data Inventaris
            $viewData['jumlahBahanBaku'] = BahanBaku::count();
            $viewData['totalKerugianBulanIni'] = $this->hitungTotalKerugianBulanIni();
            $viewData['stokKritis'] = BahanBaku::whereRaw('stok_terkini <= stok_minimum')->where('stok_terkini', '>', 0)->limit(5)->get();
            $viewData['stokHabis'] = BahanBaku::where('stok_terkini', '=', 0)->limit(5)->get();
            $viewData['aktivitasHariIni'] = RiwayatStok::whereDate('created_at', today())->selectRaw('tipe_mutasi, count(*) as total')->groupBy('tipe_mutasi')->pluck('total', 'tipe_mutasi');

        }

        return view('dashboard.index', $viewData);
    }

    /**
     * Helper function untuk menghitung total kerugian bulan ini.
     */
    private function hitungTotalKerugianBulanIni(): float
    {
        $itemsRusak = RiwayatStok::where('tipe_mutasi', 'rusak')
            ->whereYear('tanggal', now()->year)
            ->whereMonth('tanggal', now()->month)
            ->get();

        $totalKerugian = 0;
        foreach ($itemsRusak as $item) {
            $hargaBeliTerakhir = RiwayatStok::where('bahan_baku_id', $item->bahan_baku_id)
                ->where('tipe_mutasi', 'masuk')
                ->where('tanggal', '<=', $item->tanggal)
                ->latest('tanggal')
                ->value('harga_satuan');

            $hargaBeliTerakhir = $hargaBeliTerakhir ?? 0;
            $nilaiKerugian = abs($item->kuantitas) * $hargaBeliTerakhir;
            $totalKerugian += $nilaiKerugian;
        }

        return $totalKerugian;
    }
}
