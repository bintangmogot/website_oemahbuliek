<?php

namespace App\Http\Controllers;

use App\Models\GajiPokok;
use App\Models\PengaturanGaji;
use App\Models\User;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GajiPokokController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only([
            'adminIndex', 'adminShow', 'adminSummary', 'updatePembayaran', 'generateGajiPokok'
        ]);
        $this->middleware('role:pegawai')->only([
            'pegawaiIndex', 'pegawaiDetail'
        ]);
    }

/**
 * Helper method untuk mengambil data presensi yang sudah difilter
 */
private function getPresensiData($userId, $startDate, $endDate)
{
    return Presensi::where('users_id', $userId)
                   ->whereBetween('tgl_presensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                   ->where('status_approval', Presensi::STATUS_APPROVAL_APPROVED)
                   ->whereHas('jadwalShift.shift', function($query) {
                       $query->normal()||$query->lembur();
                   })
                   ->with(['jadwalShift.shift'])
                   ->orderBy('tgl_presensi', 'asc')
                   ->get();
}

private function getDetailPerHari($presensiData, $gajiPokok = null)
    {
        $detailPerHari = [];
        
        foreach ($presensiData as $presensi) {
        $jamKerjaEfektif = $presensi->jam_kerja_efektif; //pakai accessors yang asalnya dari model Presensi
        $menitTerlambat = $presensi->menit_terlambat ?? 0;
            
            $detail = [
                'tanggal' => $presensi->tgl_presensi,
                'jam_masuk' => $presensi->jam_masuk,
                'jam_keluar' => $presensi->jam_keluar,
                'jam_kerja_efektif_menit' => $jamKerjaEfektif,
                'jam_kerja_efektif_formatted' => $presensi->jam_kerja_efektif_formatted, //pakai accessors yang asalnya dari model Presensi
                'menit_terlambat' => $menitTerlambat,
                'shift' => $presensi->jadwalShift->shift->nama_shift ?? '-'
            ];

        // Gunakan tarif yang sudah di-snapshot di gaji_pokok
        if ($gajiPokok && $gajiPokok->tarif_per_jam && $gajiPokok->tarif_potongan_per_menit) {
            $detail['gaji_per_hari'] = ($jamKerjaEfektif / 60) * $gajiPokok->tarif_per_jam;
            $detail['potongan_per_hari'] = $menitTerlambat * $gajiPokok->tarif_potongan_per_menit;
        }
            
            $detailPerHari[] = $detail;
        }
        
        return $detailPerHari;
    }

/**
 * Admin: Detail realtime gaji pokok (belum tersimpan di database)
 */
public function adminDetailRealtime(Request $request)
{
    
    $userId = $request->user_id;
    $startDate = $request->filled('start_date') ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
    $endDate = $request->filled('end_date') ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

    $user = User::where('id', $request->user_id)
    ->where('role', 'pegawai') 
    ->firstOrFail();
    
    $gajiData = $this->calculateGajiPokokWithSnapshot($userId, $startDate, $endDate);

    if (!$gajiData) {
        return redirect()->back()->with('error', 'Tidak ada data presensi untuk periode yang dipilih.');
    }

    // Tambahkan status pembayaran default untuk realtime
    $gajiData['status_pembayaran'] = GajiPokok::STATUS_UNPAID;
    $gajiData['tgl_bayar'] = null;
    $gajiData['gaji_pokok_id'] = null;
    $gajiData['is_realtime'] = true;

    // GUNAKAN HELPER METHODS
    $presensiData = $this->getPresensiData($userId, $startDate, $endDate);
    $detailPerHari = $this->getDetailPerHari($presensiData);

    $totalHariKerja = count($detailPerHari);

    return view('dashboard.gaji-pokok.admin.detail', compact(
        'user',
        'gajiData',
        'detailPerHari', 
        'totalHariKerja',
        'startDate',
        'endDate'
    ));
}

    /**
     * Admin: Generate/Simpan gaji pokok untuk periode tertentu
     */
    public function generateGajiPokok(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_ids' => 'array', // jika kosong, generate untuk semua pegawai
            'user_ids.*' => 'exists:users,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $periodeBulan = $startDate->format('Y-m-01');
        
        // Ambil user yang akan digenerate
        $userIds = $request->user_ids ?? User::where('role', 'pegawai')->pluck('id')->toArray();
        
        $successCount = 0;
        $errorMessages = [];

        DB::beginTransaction();
        try {
            foreach ($userIds as $userId) {
                // Cek apakah sudah ada record untuk periode ini
                $existing = GajiPokok::where('users_id', $userId)
                    ->where('periode_start', $startDate->format('Y-m-d'))
                    ->where('periode_end', $endDate->format('Y-m-d'))
                    ->first();

                if ($existing) {
                    $errorMessages[] = "Gaji untuk periode {$startDate->format('d/m/Y')} - {$endDate->format('d/m/Y')} sudah ada untuk " . User::find($userId)->name;
                    continue;
                }

                // Hitung gaji dengan snapshot tarif saat ini
                $gajiData = $this->calculateGajiPokokWithSnapshot($userId, $startDate, $endDate);
                
                if (!$gajiData) {
                    $errorMessages[] = "Tidak ada data presensi untuk " . User::find($userId)->name;
                    continue;
                }

                // Simpan ke tabel gaji_pokok
                GajiPokok::create([
                    'users_id' => $userId,
                    'periode_bulan' => $periodeBulan,
                    'periode_start' => $startDate->format('Y-m-d'),
                    'periode_end' => $endDate->format('Y-m-d'),
                    'tarif_per_jam' => $gajiData['tarif_per_jam'],
                    'tarif_potongan_per_menit' => $gajiData['tarif_potongan_per_menit'],
                    'jumlah_jam_kerja' => $gajiData['total_jam_kerja'],
                    'total_menit_terlambat' => $gajiData['total_menit_terlambat'],
                    'gaji_kotor' => $gajiData['gaji_kotor'],
                    'total_potongan' => $gajiData['total_potongan'],
                    'total_gaji_pokok' => $gajiData['total_gaji_pokok'],
                    'status_pembayaran' => GajiPokok::STATUS_UNPAID,
                    'created_by' => Auth::id()
                ]);

                $successCount++;
            }

            DB::commit();
            
            if ($successCount > 0) {
                $message = "Berhasil generate gaji untuk {$successCount} pegawai";
                if (!empty($errorMessages)) {
                    $message .= ". Peringatan: " . implode(', ', $errorMessages);
                }
                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'Tidak ada gaji yang berhasil digenerate. ' . implode(', ', $errorMessages));
            }

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

public function generateFromRealtime(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date'
    ]);

    try {
        $userId = $request->user_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Cek apakah sudah ada data untuk periode ini
        $existing = GajiPokok::where('users_id', $userId)
            ->where('periode_start', $startDate->format('Y-m-d'))
            ->where('periode_end', $endDate->format('Y-m-d'))
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Data gaji untuk periode ini sudah ada!');
        }

        // Hitung gaji
        $gajiData = $this->calculateGajiPokokWithSnapshot($userId, $startDate, $endDate);
        
        if (!$gajiData) {
            return redirect()->back()->with('error', 'Tidak ada data presensi untuk periode ini!');
        }

        // Simpan ke database
        GajiPokok::create([
            'users_id' => $userId,
            'periode_start' => $gajiData['periode_start'],
            'periode_end' => $gajiData['periode_end'],
            'periode_bulan' => $startDate->format('Y-m'),
            'tarif_per_jam' => $gajiData['tarif_per_jam'],
            'tarif_potongan_per_menit' => $gajiData['tarif_potongan_per_menit'],
            'jumlah_jam_kerja' => $gajiData['total_jam_kerja'],
            'total_menit_terlambat' => $gajiData['total_menit_terlambat'],
            'gaji_kotor' => $gajiData['gaji_kotor'],
            'total_potongan' => $gajiData['total_potongan'],
            'total_gaji_pokok' => $gajiData['total_gaji_pokok'],
            'status_pembayaran' => GajiPokok::STATUS_UNPAID,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->back()->with('success', 'Data gaji berhasil disimpan!');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

public function adminIndex(Request $request)
{
    // Set default date range (current month or customize as needed)
    $startDate = $request->filled('start_date') 
        ? Carbon::parse($request->start_date) 
        : Carbon::now()->startOfMonth();
    
    $endDate = $request->filled('end_date') 
        ? Carbon::parse($request->end_date) 
        : Carbon::now()->endOfMonth();

    // 1. Ambil data yang sudah digenerate dari database
    $queryGenerated = GajiPokok::with('user')
        ->whereNotNull('periode_start')
        ->where('periode_start', '>=', $startDate->format('Y-m-d'))
        ->where('periode_end', '<=', $endDate->format('Y-m-d'))
        ->orderBy('periode_start', 'desc')
        ->orderBy('created_at', 'desc');

    // Filter berdasarkan pegawai untuk data generated
    if ($request->filled('user_id')) {
        $queryGenerated->where('users_id', $request->user_id);
    }

    // Filter berdasarkan status pembayaran untuk data generated
    if ($request->filled('status_pembayaran')) {
        $queryGenerated->where('status_pembayaran', $request->status_pembayaran);
    }

    $generatedData = $queryGenerated->get();
    
    // 2. Ambil data realtime yang belum digenerate
    $realtimeData = collect();
    
    // Cari pegawai yang belum ada di generated data untuk periode yang diminta
    $usersWithGenerated = $generatedData->pluck('users_id')->unique();
    $allUsers = User::where('role', 'pegawai');
    
    if ($request->filled('user_id')) {
        $allUsers->where('id', $request->user_id);
    }
    
    $allUsers = $allUsers->get();
    
foreach ($allUsers as $user) {
    // Skip jika user sudah ada di generated data untuk periode ini
    $hasGenerated = $generatedData->where('users_id', $user->id)->isNotEmpty();
    
    if (!$hasGenerated) {
        // Hitung gaji realtime
        $gajiData = $this->calculateGajiPokokWithSnapshot($user->id, $startDate, $endDate);
        
        if ($gajiData) {
            // Buat object seperti model GajiPokok untuk konsistensi
            $realtimeGaji = (object) [
                'id' => 'realtime_' . $user->id, // Unique identifier untuk realtime
                'users_id' => $user->id,
                'user' => $user,
                'periode_start' => $gajiData['periode_start'],
                'periode_end' => $gajiData['periode_end'],
                'jumlah_jam_kerja' => $gajiData['total_jam_kerja'],
                'total_menit_terlambat' => $gajiData['total_menit_terlambat'],
                'gaji_kotor' => $gajiData['gaji_kotor'],
                'total_potongan' => $gajiData['total_potongan'],
                'total_gaji_pokok' => $gajiData['total_gaji_pokok'],
                'status_pembayaran' => GajiPokok::STATUS_UNPAID,
                'tgl_bayar' => null,
                'is_realtime' => true, // Flag untuk membedakan
                'tarif_per_jam' => $gajiData['tarif_per_jam'], 
                'tarif_potongan_per_menit' => $gajiData['tarif_potongan_per_menit'] 
            ];
            
            // Filter status pembayaran untuk realtime (hanya unpaid)
            if (!$request->filled('status_pembayaran') || 
                $request->status_pembayaran == GajiPokok::STATUS_UNPAID) {
                $realtimeData->push($realtimeGaji);
            }
        }
    }

    }
    
    // 3. Gabungkan dan sort data
    $allGajiData = $generatedData->concat($realtimeData)
        ->sortByDesc(function($item) {
            return $item->periode_start;
        });

    // *** Hitung total dari semua data sebelum pagination ***
    $totalSummary = [
        'total_records' => $allGajiData->count(),
        'total_jam_kerja' => $allGajiData->sum('jumlah_jam_kerja'),
        'total_menit_terlambat' => $allGajiData->sum(function($item) {
            return $item->total_menit_terlambat ?? 0; }),
        'total_gaji_kotor' => $allGajiData->sum('gaji_kotor'),
        'total_potongan' => $allGajiData->sum('total_potongan'),
        'total_gaji_bersih' => $allGajiData->sum('total_gaji_pokok'),
        'rata_rata_gaji' => $allGajiData->count() > 0 ? $allGajiData->avg('total_gaji_pokok') : 0
    ];

    // 4. Manual pagination
    $perPage = 20;
    $currentPage = $request->get('page', 1);
    $offset = ($currentPage - 1) * $perPage;
    
    $paginatedData = $allGajiData->slice($offset, $perPage);
    
    // Create paginator
    $gajiPokokData = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedData,
        $allGajiData->count(),
        $perPage,
        $currentPage,
        [
            'path' => $request->url(),
            'pageName' => 'page',
            'query' => $request->query()
        ]
    );

    // Data untuk dropdown filter
    $usersForFilter = User::where('role', 'pegawai')->get();
    $statusOptions = [
        GajiPokok::STATUS_UNPAID => 'Belum Dibayar',
        GajiPokok::STATUS_PAID => 'Sudah Dibayar',
    ];

    return view('dashboard.gaji-pokok.admin.index', compact(
        'gajiPokokData', 
        'usersForFilter', 
        'statusOptions',
        'startDate',
        'endDate',
        'totalSummary'
    ));
}

/**
 * Admin: Halaman khusus untuk data gaji pokok yang sudah di-generate/direcord
 */
public function adminGenerated(Request $request)
{
    // Set default date range (current month or customize as needed)
    $startDate = $request->filled('start_date') 
        ? Carbon::parse($request->start_date) 
        : Carbon::now()->startOfMonth();
    
    $endDate = $request->filled('end_date') 
        ? Carbon::parse($request->end_date) 
        : Carbon::now()->endOfMonth();

    // Query hanya data yang sudah digenerate (tersimpan di database)
    $query = GajiPokok::with('user')
        ->whereNotNull('periode_start')
        ->where('periode_start', '>=', $startDate->format('Y-m-d'))
        ->where('periode_end', '<=', $endDate->format('Y-m-d'))
        ->orderBy('periode_start', 'desc')
        ->orderBy('created_at', 'desc');

    // Filter berdasarkan pegawai
    if ($request->filled('user_id')) {
        $query->where('users_id', $request->user_id);
    }

    // Filter berdasarkan status pembayaran
    if ($request->filled('status_pembayaran')) {
        $query->where('status_pembayaran', $request->status_pembayaran);
    }

    // Filter berdasarkan bulan periode
        if ($request->filled('periode_bulan')) {
            $periodeDate = Carbon::parse($request->periode_bulan);
            $query->whereYear('periode_bulan', $periodeDate->year)
                ->whereMonth('periode_bulan', $periodeDate->month);
        }

    $gajiPokokData = $query->paginate(15);

    // Hitung summary dari semua data yang sudah difilter (tanpa pagination)
    $allData = $query->get();
    $totalSummary = [
        'total_records' => $allData->count(),
        'total_jam_kerja' => $allData->sum('jumlah_jam_kerja'),
        'total_menit_terlambat' => $allData->sum('total_menit_terlambat'),
        'total_gaji_kotor' => $allData->sum('gaji_kotor'),
        'total_potongan' => $allData->sum('total_potongan'),
        'total_gaji_bersih' => $allData->sum('total_gaji_pokok'),
        'rata_rata_gaji' => $allData->count() > 0 ? $allData->avg('total_gaji_pokok') : 0,
        'belum_dibayar' => [
            'jumlah' => $allData->where('status_pembayaran', GajiPokok::STATUS_UNPAID)->count(),
            'total' => $allData->where('status_pembayaran', GajiPokok::STATUS_UNPAID)->sum('total_gaji_pokok')
        ],
        'sudah_dibayar' => [
            'jumlah' => $allData->where('status_pembayaran', GajiPokok::STATUS_PAID)->count(),
            'total' => $allData->where('status_pembayaran', GajiPokok::STATUS_PAID)->sum('total_gaji_pokok')
        ]
    ];

    // Data untuk dropdown filter
    $usersForFilter = User::where('role', 'pegawai')->get();
    $statusOptions = [
        GajiPokok::STATUS_UNPAID => 'Belum Dibayar',
        GajiPokok::STATUS_PAID => 'Sudah Dibayar',
    ];

    // Ambil daftar bulan periode yang tersedia
        $periodeBulanOptions = GajiPokok::selectRaw('DISTINCT DATE_FORMAT(periode_bulan, "%Y-%m-01") as periode_month')
        ->whereNotNull('periode_bulan')
        ->orderBy('periode_month', 'desc')
        ->pluck('periode_month')
        ->map(function($periode) {
            return [
                'value' => $periode,
                'label' => Carbon::parse($periode)->format('F Y')
            ];
        });

    return view('dashboard.gaji-pokok.admin.generated', compact(
        'gajiPokokData', 
        'usersForFilter', 
        'statusOptions',
        'periodeBulanOptions',
        'startDate',
        'endDate',
        'totalSummary'
    ));
}

    /**
     * Admin: Detail gaji pokok dari database
     */
public function adminShow($id)
{
    // Load dengan relasi pengaturanGaji
    $gajiPokok = GajiPokok::with(['user.pengaturanGaji'])->findOrFail($id);
    
    // Pastikan ada periode_start dan periode_end
    if (!$gajiPokok->periode_start || !$gajiPokok->periode_end) {
        return redirect()->back()->with('error', 'Data gaji tidak valid - periode tidak ditemukan');
    }
    
    // GUNAKAN HELPER METHODS
    $startDate = Carbon::parse($gajiPokok->periode_start);
    $endDate = Carbon::parse($gajiPokok->periode_end);
    $presensiData = $this->getPresensiData($gajiPokok->users_id, $startDate, $endDate);
    $detailPerHari = $this->getDetailPerHari($presensiData, $gajiPokok);

    // Ambil tarif dari pengaturan gaji user
    $user = $gajiPokok->user;

    // GUNAKAN TARIF YANG TERSIMPAN, bukan dari pengaturan aktif
    $tarifPerJam = $gajiPokok->tarif_per_jam ?? 15000;
    $tarifPotonganPerMenit = $gajiPokok->tarif_potongan_per_menit ?? 500;
    
    // if ($user && $user->pengaturanGaji) {
    //     $tarifPerJam = $user->pengaturanGaji->tarif_kerja_per_jam ?? 15000;
    //     $tarifPotonganPerMenit = $user->pengaturanGaji->potongan_terlambat_per_menit ?? 500;
    // }

    // Ringkasan perhitungan dengan tarif yang benar
    $ringkasanPerhitungan = [
        'total_hari_kerja' => count($detailPerHari),
        'total_jam_kerja_menit' => $presensiData->sum('jam_kerja_efektif'),
        'total_jam_kerja_formatted' => floor($presensiData->sum('jam_kerja_efektif') / 60) . ' jam ' . ($presensiData->sum('jam_kerja_efektif') % 60) . ' menit',
        'total_menit_terlambat' => $presensiData->sum('menit_terlambat'),
        'tarif_per_jam' => $tarifPerJam, // Gunakan tarif dari pengaturan gaji
        'tarif_potongan_per_menit' => $tarifPotonganPerMenit // Gunakan tarif dari pengaturan gaji
    ];

    $statusOptions = [
        GajiPokok::STATUS_UNPAID => 'Belum Dibayar',
        GajiPokok::STATUS_PAID => 'Sudah Dibayar',
    ];

    return view('dashboard.gaji-pokok.admin.show', compact(
        'gajiPokok', 
        'user',
        'presensiData',
        'detailPerHari',
        'ringkasanPerhitungan',
        'statusOptions'
    ));
}

/**
 * Admin: Ringkasan pembayaran dari database
 */
public function adminSummary(Request $request)
{
    $query = GajiPokok::whereNotNull('periode_start'); // hanya yang sudah digenerate

    // Filter berdasarkan rentang tanggal
    if ($request->filled('start_date')) {
        $query->where('periode_start', '>=', $request->start_date);
    }
    if ($request->filled('end_date')) {
        $query->where('periode_end', '<=', $request->end_date);
    }

    // Filter berdasarkan bulan periode
    if ($request->filled('periode_bulan')) {
        $periodeDate = Carbon::parse($request->periode_bulan);
        $query->whereYear('periode_start', $periodeDate->year)
              ->whereMonth('periode_start', $periodeDate->month);
    }

        // Data untuk dropdown filter bulan
    $periodeBulanOptions = GajiPokok::selectRaw('DISTINCT DATE_FORMAT(periode_start, "%Y-%m-01") as periode_month')
        ->whereNotNull('periode_start')
        ->orderBy('periode_month', 'desc')
        ->pluck('periode_month')
        ->map(function($periode) {
            return [
                'value' => $periode,
                'label' => Carbon::parse($periode)->format('F Y')
            ];
        });

    // Hitung total gaji kotor dan potongan
    $totalGajiKotor = $query->sum('gaji_kotor');
    $totalPotongan = $query->sum('total_potongan');
    $totalGajiBersih = $query->sum('total_gaji_pokok'); // atau bisa dihitung: $totalGajiKotor - $totalPotongan
    
    // Query untuk belum dibayar
    $belumDibayar = (clone $query)->where('status_pembayaran', GajiPokok::STATUS_UNPAID);
    $totalBelumDibayar = $belumDibayar->sum('total_gaji_pokok');
    $jumlahBelumDibayar = $belumDibayar->count();
    
    // Query untuk sudah dibayar  
    $sudahDibayar = (clone $query)->where('status_pembayaran', GajiPokok::STATUS_PAID);
    $totalSudahDibayar = $sudahDibayar->sum('total_gaji_pokok');
    $jumlahSudahDibayar = $sudahDibayar->count();

    $ringkasan = [
        // Data dasar
        'total_gaji_kotor' => $totalGajiKotor,
        'total_potongan' => $totalPotongan,
        'total_gaji_bersih' => $totalGajiBersih,
        'total_pegawai' => $query->distinct('users_id')->count(),
        
        // Status pembayaran
        'belum_dibayar' => [
            'total' => $totalBelumDibayar,
            'jumlah' => $jumlahBelumDibayar
        ],
        'sudah_dibayar' => [
            'total' => $totalSudahDibayar,
            'jumlah' => $jumlahSudahDibayar
        ],
        
        // Total transaksi
        'total_transaksi' => $jumlahBelumDibayar + $jumlahSudahDibayar
    ];

    return view('dashboard.gaji-pokok.admin.summary', compact('ringkasan', 'periodeBulanOptions'));
}

    /**
     * Pegawai: List gaji pokok sendiri dari database
     */
    public function pegawaiIndex(Request $request)
    {
        $userId = Auth::id();
        
        $query = GajiPokok::where('users_id', $userId)
            ->whereNotNull('periode_start') // hanya yang sudah digenerate
            ->orderBy('periode_start', 'desc');

        // Filter berdasarkan rentang tanggal
        if ($request->filled('start_date')) {
            $query->where('periode_start', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('periode_end', '<=', $request->end_date);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('status_pembayaran')) {
            $query->where('status_pembayaran', $request->status_pembayaran);
        }

        $gajiPokokData = $query->paginate(10);

        // Ringkasan untuk pegawai
        $ringkasan = [
            'belum_dibayar' => GajiPokok::where('users_id', $userId)
                ->whereNotNull('periode_start')
                ->where('status_pembayaran', GajiPokok::STATUS_UNPAID)
                ->sum('total_gaji_pokok'),
            'sudah_dibayar' => GajiPokok::where('users_id', $userId)
                ->whereNotNull('periode_start')
                ->where('status_pembayaran', GajiPokok::STATUS_PAID)
                ->sum('total_gaji_pokok')
        ];

        $statusOptions = [
            GajiPokok::STATUS_UNPAID => 'Belum Dibayar',
            GajiPokok::STATUS_PAID => 'Sudah Dibayar',
        ];

        return view('dashboard.gaji-pokok.pegawai.index-gaji-pokok', compact(
            'gajiPokokData',
            'ringkasan',
            'statusOptions'
        ));
    }

    /**
     * Pegawai: Detail gaji pokok sendiri dari database
     */
    public function pegawaiDetail($id)
    {
        $userId = Auth::id();
        $gajiPokok = GajiPokok::where('users_id', $userId)->findOrFail($id);

        // Pastikan ada periode_start dan periode_end
        if (!$gajiPokok->periode_start || !$gajiPokok->periode_end) {
            return redirect()->back()->with('error', 'Data gaji tidak valid - periode tidak ditemukan');
        }

        // PAKAI HELPER METHOD
    $startDate = Carbon::parse($gajiPokok->periode_start);
    $endDate = Carbon::parse($gajiPokok->periode_end);
    $presensiData = $this->getPresensiData($userId, $startDate, $endDate);
    $detailPerHari = $this->getDetailPerHari($presensiData);

        return view('dashboard.gaji-pokok.pegawai.detail-gaji-pokok', compact(
            'gajiPokok',
            'detailPerHari'
        ));
    }

    /**
     * Admin: Update status pembayaran
     */
    public function updatePembayaran(Request $request)
    {
        $request->validate([
            'gaji_pokok_id' => 'required|exists:gaji_pokok,id',
            'status_pembayaran' => 'required|in:0,1',
            'tgl_bayar' => 'nullable|date'
        ]);

        $gajiPokok = GajiPokok::findOrFail($request->gaji_pokok_id);

        $updateData = [
            'status_pembayaran' => $request->status_pembayaran,
        ];

        if ($request->status_pembayaran != GajiPokok::STATUS_UNPAID) {
            $updateData['tgl_bayar'] = $request->tgl_bayar ?? now()->toDateString();
        } else {
            $updateData['tgl_bayar'] = null;
        }

        $gajiPokok->update($updateData);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diupdate');
    }

    /**
     * Helper method untuk menghitung gaji pokok dengan snapshot tarif
     */
    private function calculateGajiPokokWithSnapshot($userId, $startDate, $endDate)
    {
        // Ambil pengaturan gaji user saat ini (akan di-snapshot)
        // Ambil user dengan pengaturan gaji langsung
        $user = User::with('pengaturanGaji')->findOrFail($userId);

        // Cek apakah user punya pengaturan_gaji_id dan relasi pengaturanGaji
        if (!$user->pengaturan_gaji_id || !$user->pengaturanGaji) {
            // Gunakan default jika tidak ada pengaturan
            $tarifPerJam = 15000;
            $tarifPotonganPerMenit = 500;
        } else {
            $pengaturanGaji = $user->pengaturanGaji;
            $tarifPerJam = $pengaturanGaji->tarif_kerja_per_jam ?? 15000;
            $tarifPotonganPerMenit = $pengaturanGaji->potongan_terlambat_per_menit ?? 500;
        }

        // Ambil semua presensi yang disetujui untuk shift normal dalam rentang tanggal tersebut
        $presensiData = Presensi::where('users_id', $userId)
            ->whereBetween('tgl_presensi', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->where('status_approval', Presensi::STATUS_APPROVAL_APPROVED)
            ->whereHas('jadwalShift.shift', function($query) {
                $query->normal();
            })
            ->get();

        if ($presensiData->isEmpty()) {
            return null;
        }

        // Hitung total jam kerja dan total keterlambatan
        $totalJamKerjaMenit = 0;
        $totalMenitTerlambat = 0;
        $totalHariKerja = $presensiData->count();

        foreach ($presensiData as $presensi) {
            $totalJamKerjaMenit += $presensi->jam_kerja_efektif ?: 0;
            $totalMenitTerlambat += $presensi->menit_terlambat ?: 0;
        }

        // Convert menit ke jam untuk jam kerja
        $totalJamKerjaDecimal = round($totalJamKerjaMenit / 60, 2);

        // Hitung gaji pokok
        $gajiKotor = $totalJamKerjaDecimal * $tarifPerJam;
        $totalPotongan = $totalMenitTerlambat * $tarifPotonganPerMenit;
        $gajiBersih = $gajiKotor - $totalPotongan;

        return [
            'user_id' => $userId,
            'user' => $user,
            'tarif_per_jam' => $tarifPerJam,
            'tarif_potongan_per_menit' => $tarifPotonganPerMenit,
            'periode_start' => $startDate->format('Y-m-d'),
            'periode_end' => $endDate->format('Y-m-d'),
            'total_hari_kerja' => $totalHariKerja,
            'total_jam_kerja' => $totalJamKerjaDecimal,
            'total_menit_terlambat' => $totalMenitTerlambat,
            'gaji_kotor' => $gajiKotor,
            'total_potongan' => $totalPotongan,
            'total_gaji_pokok' => max(0, $gajiBersih)
        ];
    }

    /**
     * Helper method untuk preview perhitungan (tanpa menyimpan)
     */
    public function previewGaji(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'user_id' => 'required|exists:users,id'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $userId = $request->user_id;

        $gajiData = $this->calculateGajiPokokWithSnapshot($userId, $startDate, $endDate);

        if (!$gajiData) {
            return response()->json(['error' => 'Tidak ada data presensi untuk periode tersebut'], 400);
        }

        return response()->json($gajiData);
    }

}