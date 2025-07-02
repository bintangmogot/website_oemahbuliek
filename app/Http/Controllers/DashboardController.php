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
            $viewData['jadwalHariIni'] = JadwalShift::with('shift')->where('users_id', $userId)->whereDate('tanggal', today())->first();
            if (!empty($viewData['jadwalHariIni'])) {
                $viewData['presensiHariIni'] = Presensi::where('jadwal_shift_id', $viewData['jadwalHariIni']->id)->first();
            }
            $viewData['jadwalBerikutnya'] = JadwalShift::with('shift')->where('users_id', $userId)->where('tanggal', '>', today())->orderBy('tanggal', 'asc')->first();
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
