<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\User;
use App\Models\JadwalShift;
use App\Models\GajiLembur;
use App\Models\GajiPokok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class PresensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin')->only(['adminIndex', 'approve', 'reject']);
        $this->middleware('role:pegawai')->only(['pegawaiIndex', 'show', 'checkIn', 'checkOut']);
        // Set timezone untuk seluruh controller
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');
    }

    /**
     * Tampilan untuk Admin - Lihat semua data presensi dan approval
     */
public function adminIndex(Request $request)
{
    $query = Presensi::with(['user', 'jadwalShift.shift'])
        ->orderBy('tgl_presensi', 'desc')
        ->orderBy('created_at', 'desc');

    // Filter berdasarkan status approval
    if ($request->filled('status_approval') && $request->status_approval !== '') {
        $query->where('status_approval', $request->status_approval);
    }

    // Filter berdasarkan tanggal
    if ($request->filled('start_date') && $request->start_date !== '') {
        $query->whereDate('tgl_presensi', '>=', $request->start_date);
    }

    if ($request->filled('end_date') && $request->end_date !== '') {
        $query->whereDate('tgl_presensi', '<=', $request->end_date);
    }

    // Filter berdasarkan user
    if ($request->filled('user_id') && $request->user_id !== '' && is_numeric($request->user_id)) {
        $query->where('users_id', $request->user_id);
    }

    // HITUNG SEMUA STATISTIK SEBELUM PAGINATION
    // Clone query untuk statistik agar tidak mengubah query utama
    $statsQuery = clone $query;
    
    $totalPresensi = $statsQuery->count();
    $approvedCount = $statsQuery->where('status_approval', 1)->count();
    $lemburApprovedCount = $statsQuery->where('status_lembur', 2)->count();
    
    // Untuk pending count, kita perlu query terpisah karena mungkin berbeda logika filter
    $pendingQuery = Presensi::where('status_approval', 0); // 0 = pending
    
    // Terapkan filter yang sama untuk pending count
    if ($request->filled('start_date') && $request->start_date !== '') {
        $pendingQuery->whereDate('tgl_presensi', '>=', $request->start_date);
    }
    if ($request->filled('end_date') && $request->end_date !== '') {
        $pendingQuery->whereDate('tgl_presensi', '<=', $request->end_date);
    }
    if ($request->filled('user_id') && $request->user_id !== '' && is_numeric($request->user_id)) {
        $pendingQuery->where('users_id', $request->user_id);
    }
    
    $pendingCount = $pendingQuery->count();

    // Paginate dengan append query parameters
    $presensi = $query->paginate(20);
    $presensi->appends($request->query());
    
    // Data untuk filter
    $users = User::where('role', 'pegawai')
        ->orderBy('nama_lengkap')
        ->get();

    return view('dashboard.presensi.admin.index', compact(
        'presensi', 
        'pendingCount', 
        'users', 
        'totalPresensi',
        'approvedCount',
        'lemburApprovedCount'
    ));
}

    /**
     * Tampilan untuk Pegawai - Lihat jadwal dan presensi sendiri
     */
    public function pegawaiIndex()
    {
        $userId = Auth::id();
        $today = Carbon::today();
        
        // Ambil jadwal shift pegawai (7 hari ke depan dari hari ini)
        $jadwalShifts = JadwalShift::with(['shift', 'user'])
            ->where('users_id', $userId)
            ->where('status', 1) // Hanya jadwal aktif
            ->where('tanggal', '>=', $today)
            ->orderBy('tanggal', 'asc')
            ->take(10)
            ->get();

        // Ambil riwayat presensi (7 hari terakhir)
        $riwayatPresensi = Presensi::with(['jadwalShift.shift'])
            ->where('users_id', $userId)
            ->orderBy('tgl_presensi', 'desc')
            ->take(7)
            ->get();

        return view('dashboard.presensi.pegawai.index', compact('jadwalShifts', 'riwayatPresensi'));
    }

    /**
     * Tampilkan halaman presensi untuk jadwal tertentu
     */
    public function show(JadwalShift $jadwalShift)
    {
        // Pastikan pegawai hanya bisa akses jadwal sendiri
        if (Auth::user()->role === 'pegawai' && $jadwalShift->users_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke jadwal ini.');
        }

        $jadwalShift->load(['shift', 'user']);
        
        // Cari atau buat record presensi
        $presensi = Presensi::firstOrCreate([
            'users_id' => $jadwalShift->users_id,
            'jadwal_shift_id' => $jadwalShift->id,
            'tgl_presensi' => $jadwalShift->tanggal,
        ]);

        $today = Carbon::today();
        $jadwalTanggal = Carbon::parse($jadwalShift->tanggal);
        $isToday = $today->isSameDay($jadwalTanggal);
        $isPastDate = $today->greaterThan($jadwalTanggal);

        return view('dashboard.presensi.pegawai.show', compact(
            'jadwalShift', 
            'presensi', 
            'isToday', 
            'isPastDate'
        ));
    }

    /**
     * Check In
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048',
            'presensi_id' => 'required|exists:presensi,id'
        ]);

        $presensi = Presensi::with(['jadwalShift.shift'])->findOrFail($request->presensi_id);
        
        // Validasi akses
        if ($presensi->users_id !== Auth::id()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        // Validasi apakah bisa check in
        if (!$presensi->canCheckIn()) {
            return response()->json(['error' => 'Tidak dapat check in pada waktu ini'], 400);
        }

        try {
            DB::beginTransaction();

            // Upload foto
            $foto = $request->file('foto');
            $namaFoto = 'checkin_' . $presensi->users_id . '_' . date('Y-m-d_H-i-s') . '.' . $foto->getClientOriginalExtension();
            $fotoPath = $foto->storeAs('presensi', $namaFoto, 'public');

            // Update presensi
            $jamMasuk = Carbon::now();
            
            // Hitung keterlambatan berdasarkan jam shift
            $shift = $presensi->jadwalShift->shift;
            $jamMulaiShift = Carbon::parse($shift->jam_mulai);
            $jamMulaiShift->setDate($jamMasuk->year, $jamMasuk->month, $jamMasuk->day);
            
            $menitTerlambat = 0;
            $isLate = false;
            
            if ($jamMasuk->greaterThan($jamMulaiShift)) {
            $totalMenitTerlambat = $jamMasuk->diffInMinutes($jamMulaiShift);
            $toleransi = $shift->toleransi_terlambat ?? 15; // Default 15 menit toleransi

            // Hitung keterlambatan setelah dikurangi toleransi
            $menitTerlambat = max(0, $totalMenitTerlambat - $toleransi);
            $isLate = $menitTerlambat > 0; // Late jika masih ada sisa setelah toleransi

                if ($menitTerlambat > ($toleransi)) { // Default 15 menit toleransi
                    if ($isLate) {
                        $response['warning'] = [
                            'title' => 'Peringatan: Terlambat!',
                            'message' => "Anda terlambat {$menitTerlambat} menit dari jadwal masuk ({$jamMulaiShift->format('H:i')}). Presensi Anda akan ditinjau oleh admin.",
                            'type' => 'late'
                        ];
                }
            }
        }
            
            // Tentukan status kehadiran dan approval
            $statusKehadiran = $isLate ? Presensi::STATUS_KEHADIRAN_LATE : Presensi::STATUS_KEHADIRAN_PRESENT;
            $statusApproval = Presensi::STATUS_APPROVAL_PENDING; // SEMUA CHECK IN HARUS PENDING

            // Cek apakah shift ini adalah shift lembur
            $isShiftLembur = $shift && $shift->is_shift_lembur == 1;
            
            // Jika shift lembur, SELALU set status_lembur = STATUS_LEMBUR_SHIFT (3)
            $statusLembur = $isShiftLembur ? Presensi::STATUS_LEMBUR_SHIFT : null;

            $updateData = [
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $fotoPath,
                'menit_terlambat' => $menitTerlambat,
                'status_kehadiran' => $statusKehadiran,
                'status_approval' => $statusApproval
            ];

            // Set status lembur jika shift lembur
            if ($statusLembur) {
                $updateData['status_lembur'] = $statusLembur;
            }

            $presensi->update($updateData);

            // Hitung jam kerja efektif jika sudah check out
            if ($presensi->jam_keluar) {
                $presensi->update(['jam_kerja_efektif' => $presensi->calculateEffectiveWorkHours()]);
            }

            DB::commit();

            // Prepare response dengan warning jika terlambat
            $response = [
                'success' => true,
                'message' => 'Check in berhasil!',
                'data' => [
                    'jam_masuk' => $jamMasuk->format('H:i'),
                    'foto_masuk' => Storage::url($fotoPath),
                    'status_kehadiran' => $presensi->status_kehadiran_label,
                    'status_approval' => $presensi->status_approval_label,
                    'menit_terlambat' => $menitTerlambat
                ]
            ];

            // Tambahkan warning jika terlambat
            if ($isLate) {
                $response['warning'] = [
                    'title' => 'Peringatan: Terlambat!',
                    'message' => "Anda terlambat {$menitTerlambat} menit dari jadwal masuk ({$jamMulaiShift->format('H:i')}). Presensi Anda akan ditinjau oleh admin.",
                    'type' => 'late'
                ];
            }

            // Tambahkan informasi shift lembur
            if ($isShiftLembur) {
                $response['info'] = [
                    'title' => 'Informasi: Shift Lembur',
                    'message' => "Anda bekerja pada shift lembur. Seluruh jam kerja akan dihitung sebagai lembur jika disetujui admin.",
                    'type' => 'shift_lembur'
                ];
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Check Out
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|max:2048',
            'presensi_id' => 'required|exists:presensi,id'
        ]);

        $presensi = Presensi::with(['jadwalShift.shift'])->findOrFail($request->presensi_id);
        
        // Validasi akses
        if ($presensi->users_id !== Auth::id()) {
            return response()->json(['error' => 'Tidak memiliki akses'], 403);
        }

        // Validasi apakah bisa check out
        if (!$presensi->canCheckOut()) {
            return response()->json(['error' => 'Tidak dapat check out. Pastikan sudah check in terlebih dahulu'], 400);
        }

        try {
            DB::beginTransaction();

            // Upload foto
            $foto = $request->file('foto');
            $namaFoto = 'checkout_' . $presensi->users_id . '_' . date('Y-m-d_H-i-s') . '.' . $foto->getClientOriginalExtension();
            $fotoPath = $foto->storeAs('presensi', $namaFoto, 'public');

            // Update presensi
            $jamKeluar = Carbon::now();
            $shift = $presensi->jadwalShift->shift;
            $jamSelesaiShift = Carbon::parse($shift->jam_selesai);
            $jamSelesaiShift->setDate($jamKeluar->year, $jamKeluar->month, $jamKeluar->day);

            $updateData = [
                'jam_keluar' => $jamKeluar,
                'foto_keluar' => $fotoPath,
                'status_approval' => Presensi::STATUS_APPROVAL_PENDING // SEMUA CHECK OUT HARUS PENDING
            ];

            $warnings = [];
            $menitOvertime = 0;
            $jamLemburTotal = 0; // Total jam untuk perhitungan lembur

            // LOGIKA UTAMA: Cek tipe shift untuk menentukan perhitungan lembur
            $isShiftLembur = $shift && $shift->is_shift_lembur == 1;

            // JIKA SHIFT LEMBUR: SELALU set status_lembur = STATUS_LEMBUR_SHIFT (3)
            if ($isShiftLembur) {
                $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_SHIFT;
                
                // Shift Lembur: Seluruh jam kerja efektif dihitung sebagai lembur
                $jamKerjaEfektifMenit = $presensi->calculateEffectiveWorkHours(); // Hasil dalam MENIT
                $jamLemburTotal = round($jamKerjaEfektifMenit / 60, 2); // KONVERSI ke jam (decimal)
                
                if ($jamLemburTotal > 0) {
                    // Buat/update record gaji lembur untuk shift lembur
                    GajiLembur::updateOrCreate(
                        [
                            'presensi_id' => $presensi->id,
                            'users_id' => $presensi->users_id,
                            'tgl_lembur' => $presensi->tgl_presensi,
                        ],
                        [
                            'tipe_lembur' => 'shift_lembur',
                            'total_jam_lembur' => $jamLemburTotal,
                            'total_gaji_lembur' => 0, // Akan dihitung nanti berdasarkan pengaturan gaji
                            'status_pembayaran' => 0,
                            'keterangan_lembur' => "Shift lembur - seluruh {$jamLemburTotal} jam kerja efektif"
                        ]
                    );
                    
                    $warnings[] = [
                        'title' => 'Informasi: Shift Lembur',
                        'message' => "Anda bekerja pada shift lembur selama " . number_format($jamLemburTotal, 2) . " jam. Seluruh jam kerja akan dihitung sebagai lembur jika disetujui admin.",
                        'type' => 'shift_lembur'
                    ];
                } else {
                    // Tetap set sebagai shift lembur meskipun jam kerja 0
                    // Hapus record gaji lembur jika tidak ada jam kerja
                    GajiLembur::where('presensi_id', $presensi->id)->delete();
                }

                // Untuk shift lembur, cek kondisi pulang untuk status kehadiran
                if ($jamKeluar->lessThan($jamSelesaiShift)) {
                    $menitPulangAwal = $jamSelesaiShift->diffInMinutes($jamKeluar);
                    $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_HALF_DAY;
                    
                    $warnings[] = [
                        'title' => 'Peringatan: Pulang Lebih Awal!',
                        'message' => "Anda pulang {$menitPulangAwal} menit lebih awal dari jadwal ({$jamSelesaiShift->format('H:i')}). Presensi Anda akan ditinjau oleh admin.",
                        'type' => 'early_checkout'
                    ];
                } else {
                    // Pertahankan status kehadiran yang sudah ada (present/late)
                    if (!isset($updateData['status_kehadiran'])) {
                        $updateData['status_kehadiran'] = $presensi->status_kehadiran ?: Presensi::STATUS_KEHADIRAN_PRESENT;
                    }
                }

                // TAMBAHAN: Jika ada overtime pada shift lembur, tetap status_lembur = STATUS_LEMBUR_SHIFT
                if ($jamKeluar->greaterThan($jamSelesaiShift)) {
                    $totalMenitOvertime = $jamKeluar->diffInMinutes($jamSelesaiShift);
                    $warnings[] = [
                        'title' => 'Informasi: Overtime pada Shift Lembur',
                        'message' => "Anda bekerja {$totalMenitOvertime} menit melebihi jadwal shift lembur. Seluruh jam kerja tetap dihitung sebagai shift lembur.",
                        'type' => 'shift_lembur_overtime'
                    ];
                }
            }
            // JIKA SHIFT NORMAL: Gunakan logika lembur overtime seperti sebelumnya
            else {
                // Cek apakah pulang lebih awal
                if ($jamKeluar->lessThan($jamSelesaiShift)) {
                    $menitPulangAwal = $jamSelesaiShift->diffInMinutes($jamKeluar);
                    
                    // Jika pulang lebih awal, ubah status kehadiran menjadi half day
                    $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_HALF_DAY;
                    $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO; // Tidak ada lembur jika pulang awal
                    
                    $warnings[] = [
                        'title' => 'Peringatan: Pulang Lebih Awal!',
                        'message' => "Anda pulang {$menitPulangAwal} menit lebih awal dari jadwal ({$jamSelesaiShift->format('H:i')}). Presensi Anda akan ditinjau oleh admin.",
                        'type' => 'early_checkout'
                    ];
                }
                // Cek apakah pulang tepat waktu atau overtime
                else {
                    // Shift Normal: Hanya overtime yang dihitung sebagai lembur
                    if ($jamKeluar->greaterThan($jamSelesaiShift)) {
                        $totalMenitOvertime = $jamKeluar->diffInMinutes($jamSelesaiShift);
                        $batasLemburMin = $shift->batas_lembur_min ?? 30; // Default 30 menit
                        // Hitung lembur setelah dikurangi batas minimum
                        $menitOvertime = max(0, $totalMenitOvertime - $batasLemburMin);

                        // Jika overtime melebihi batas minimum dan masih ada sisa setelah dikurangi, set status lembur
                        if ($totalMenitOvertime >= $batasLemburMin && $menitOvertime > 0) {
                            $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_OVERTIME;
                    
                            // Buat/update record gaji lembur
                            $jamLembur = round($menitOvertime / 60, 2); // Convert ke jam (decimal)
                    
                            GajiLembur::updateOrCreate(
                                [
                                    'presensi_id' => $presensi->id,
                                    'users_id' => $presensi->users_id,
                                    'tgl_lembur' => $presensi->tgl_presensi,
                                ],
                                [
                                    'pengaturan_gaji_id' => 1, // Sesuaikan dengan kebutuhan
                                    'tipe_lembur' => 'overtime',
                                    'total_jam_lembur' => $jamLembur,
                                    'total_gaji_lembur' => 0, // Akan dihitung nanti berdasarkan pengaturan gaji
                                    'status_pembayaran' => 0,
                                    'keterangan_lembur' => "Overtime {$menitOvertime} menit"
                                ]
                            );
                            $warnings[] = [
                                'title' => 'Informasi: Lembur Terdeteksi',
                                'message' => "Anda lembur selama {$menitOvertime} menit. Lembur akan dihitung jika disetujui admin.",
                                'type' => 'overtime'
                            ];
                        } else {
                            // Overtime kurang dari batas minimum, tidak dihitung lembur
                            $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO;
                            $menitOvertime = 0;
                            
                            // Hapus record gaji lembur jika ada (karena tidak memenuhi syarat)
                            GajiLembur::where('presensi_id', $presensi->id)->delete();

                            if ($totalMenitOvertime > 0) {
                                $warnings[] = [
                                    'title' => 'Informasi: Overtime Tidak Dihitung',
                                    'message' => "Anda pulang {$totalMenitOvertime} menit setelah jam kerja, namun belum mencapai batas minimum lembur ({$batasLemburMin} menit).",
                                    'type' => 'overtime_minimal'
                                ];
                            }
                        }
                    } else {
                        // Pulang tepat waktu pada shift normal
                        $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO;
                        $menitOvertime = 0;
                        // Hapus record gaji lembur jika ada
                        GajiLembur::where('presensi_id', $presensi->id)->delete();
                    }
                    
                    // Pertahankan status kehadiran yang sudah ada (present/late)
                    // Kecuali jika belum di-set, maka set sebagai present
                    if (!isset($updateData['status_kehadiran'])) {
                        $updateData['status_kehadiran'] = $presensi->status_kehadiran ?: Presensi::STATUS_KEHADIRAN_PRESENT;
                    }
                }
            }

            $presensi->update($updateData);
            // Hitung dan simpan jam kerja efektif
            $jamKerjaEfektif = $presensi->calculateEffectiveWorkHours();
            $presensi->update(['jam_kerja_efektif' => $jamKerjaEfektif]);

            // Jika ada lembur yang diapprove, hitung total gaji lembur
            if ($presensi->fresh()->status_lembur === Presensi::STATUS_LEMBUR_APPROVED) {
                $gajiLembur = GajiLembur::where('presensi_id', $presensi->id)->first();
                if ($gajiLembur) {
                    // Hitung gaji lembur jika ada pengaturan gaji
                    // Sesuaikan dengan struktur tabel pengaturan_gaji Anda
                    $gajiLembur->update([
                        'total_gaji_lembur' => $gajiLembur->total_jam_lembur * 50000, // Contoh tarif per jam
                    ]);
                }
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => 'Check out berhasil!',
                'data' => [
                    'jam_keluar' => $jamKeluar->format('H:i'),
                    'foto_keluar' => Storage::url($fotoPath),
                    'status_kehadiran' => $presensi->fresh()->status_kehadiran_label,
                    'status_lembur' => $presensi->fresh()->status_lembur_label,
                    'status_approval' => $presensi->fresh()->status_approval_label,
                    'menit_overtime' => $menitOvertime ?? 0,
                    'is_shift_lembur' => $isShiftLembur
                ]
            ];

            if (!empty($warnings)) {
                $response['warnings'] = $warnings;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Approve presensi (Admin only)
     */
    public function approve(Request $request, Presensi $presensi)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            $updateData = [
                'status_approval' => Presensi::STATUS_APPROVAL_APPROVED,
                'catatan_admin' => $request->catatan_admin
            ];

            // Load shift untuk cek tipe
            $presensi->load('jadwalShift.shift');
            $shift = $presensi->jadwalShift->shift;
            $isShiftLembur = $shift && $shift->is_shift_lembur == 1;

            // Jika ada lembur pending (overtime atau shift lembur), approve juga
            if ($presensi->status_lembur === Presensi::STATUS_LEMBUR_OVERTIME || 
                $presensi->status_lembur === Presensi::STATUS_LEMBUR_SHIFT) {
                $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_APPROVED;
            }

            $presensi->update($updateData);

            // Update gaji pokok jika disetujui
            if ($presensi->jadwalShift->shift->isNormal()) {
                $this->updateGajiPokok($presensi);
            }

            // Jika ada lembur yang diapprove, pastikan record gaji lembur dibuat/diupdate
            if ($presensi->status_lembur === Presensi::STATUS_LEMBUR_APPROVED) {
                $this->processGajiLembur($presensi);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Presensi berhasil disetujui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


/**
 * Process gaji lembur - membuat record di database saat checkout (dengan perhitungan gaji untuk preview)
 */
private function processGajiLembur($presensi)
{
    $presensi->load('jadwalShift.shift');
    $shift = $presensi->jadwalShift->shift;
    $isShiftLembur = $shift && $shift->is_shift_lembur == 1;
    
    // Hanya proses jika ada lembur (overtime atau shift lembur)
    if (!in_array($presensi->status_lembur, [
        Presensi::STATUS_LEMBUR_OVERTIME, 
        Presensi::STATUS_LEMBUR_SHIFT, 
        Presensi::STATUS_LEMBUR_APPROVED
    ])) {
        // Hapus record gaji lembur jika status lembur = NO
        GajiLembur::where('presensi_id', $presensi->id)->delete();
        return;
    }
    
    // Tentukan tipe lembur berdasarkan jenis shift
    $tipeLembur = $isShiftLembur ? 'shift_lembur' : 'overtime';
    
    // Hitung jam lembur
    if ($tipeLembur === 'shift_lembur') {
        // Untuk shift lembur: seluruh jam kerja efektif
        $jamKerjaEfektifMenit = $presensi->jam_kerja_efektif ?: 0;
        $jamLembur = round($jamKerjaEfektifMenit / 60, 2);
        $keterangan = "Shift lembur - seluruh {$jamLembur} jam kerja efektif";
    } else {
        // Untuk overtime: hitung dari jam keluar - jam selesai shift
        $jamKeluar = Carbon::parse($presensi->jam_keluar);
        $jamSelesaiShift = Carbon::parse($shift->jam_selesai);
        $jamSelesaiShift->setDate($jamKeluar->year, $jamKeluar->month, $jamKeluar->day);
        
        if ($jamKeluar->greaterThan($jamSelesaiShift)) {
            $totalMenitOvertime = $jamKeluar->diffInMinutes($jamSelesaiShift);
            $batasLemburMin = $shift->batas_lembur_min ?? 30;
            $menitOvertime = max(0, $totalMenitOvertime - $batasLemburMin);
            $jamLembur = round($menitOvertime / 60, 2);
            $keterangan = "Overtime {$menitOvertime} menit";
        } else {
            $jamLembur = 0;
            $keterangan = "Tidak ada overtime";
        }
    }
    
    // Buat/update record gaji lembur jika ada jam lembur
    if ($jamLembur > 0) {
        // Hitung gaji lembur untuk preview (menggunakan perhitungan yang sudah ada)
        $totalGajiLembur = $jamLembur * 50000; // Sesuai dengan perhitungan yang sudah ada
        
        GajiLembur::updateOrCreate(
            [
                'presensi_id' => $presensi->id,
                'users_id' => $presensi->users_id,
                'tgl_lembur' => $presensi->tgl_presensi,
            ],
            [
                'tipe_lembur' => $tipeLembur,
                'total_jam_lembur' => $jamLembur,
                'total_gaji_lembur' => $totalGajiLembur, // Sudah dihitung untuk preview
                'status_pembayaran' => 0,
                'keterangan_lembur' => $keterangan
            ]
        );
    } else {
        // Hapus record jika tidak ada jam lembur
        GajiLembur::where('presensi_id', $presensi->id)->delete();
    }
}

    /**
     * Reject presensi (Admin only)
     */
public function reject(Request $request, Presensi $presensi)
{
    $request->validate([
        'catatan_admin' => 'required|string|max:500'
    ]);

    try {
        DB::beginTransaction();

        $updateData = [
            'status_approval' => Presensi::STATUS_APPROVAL_REJECTED,
            'catatan_admin' => $request->catatan_admin
        ];

        // Load relasi shift untuk cek tipe shift
        $presensi->load('jadwalShift.shift');
        $shift = $presensi->jadwalShift->shift;
        $isShiftLembur = $shift && $shift->is_shift_lembur == 1;

        // Logic penolakan berdasarkan kondisi:
        
        // 1. Jika pegawai tepat waktu masuk dan pulang tepat waktu (tidak ada lembur), 
        //    tapi ditolak = tidak dihitung sama sekali (ABSENT)
        if ($presensi->status_kehadiran === Presensi::STATUS_KEHADIRAN_PRESENT && 
            $presensi->status_lembur === Presensi::STATUS_LEMBUR_NO) {
            $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_ABSENT;
        }
        
        // 2. Jika pegawai terlambat masuk tapi ditolak = tetap ABSENT
        if ($presensi->status_kehadiran === Presensi::STATUS_KEHADIRAN_LATE) {
            $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_ABSENT;
        }
        
        // 3. Jika pegawai pulang lebih awal tapi ditolak = tetap ABSENT
        if ($presensi->status_kehadiran === Presensi::STATUS_KEHADIRAN_HALF_DAY) {
            $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_ABSENT;
        }
        
        // 4. Jika ada lembur tapi ditolak = lembur tidak dihitung, 
        //    tapi jam kerja normal tetap dihitung sesuai status aslinya
        if ($presensi->status_lembur === Presensi::STATUS_LEMBUR_OVERTIME || 
                $presensi->status_lembur === Presensi::STATUS_LEMBUR_SHIFT) {
            $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO;
            // Untuk shift lembur yang ditolak, karena seluruh jam kerja = lembur,
            // maka jika ditolak = tidak ada jam kerja yang dihitung = ABSENT
            if ($isShiftLembur) {
                $updateData['status_kehadiran'] = Presensi::STATUS_KEHADIRAN_ABSENT;
            } else {
            // Untuk kasus lembur yang ditolak, status kehadiran tetap sesuai kondisi aslinya
            // Misalnya: jika pegawai masuk tepat waktu tapi lembur ditolak, 
            // maka tetap dihitung PRESENT untuk jam kerja normal
            
            // Kembalikan status kehadiran ke kondisi tanpa lembur
            if ($presensi->status_kehadiran === Presensi::STATUS_KEHADIRAN_PRESENT ||
                $presensi->status_kehadiran === Presensi::STATUS_KEHADIRAN_LATE) {
                // Pertahankan status kehadiran asli (present/late)
                // Tidak perlu mengubah status_kehadiran karena sudah benar
            }
        }
    }

        $presensi->update($updateData);

            // Jika lembur ditolak, hapus record gaji lembur
            if (isset($updateData['status_lembur']) && $updateData['status_lembur'] === Presensi::STATUS_LEMBUR_NO) {
                GajiLembur::where('presensi_id', $presensi->id)->delete();
            }

        DB::commit();

        $pesan = 'Presensi berhasil ditolak dengan alasan: ' . $request->catatan_admin;
        if ($isShiftLembur && isset($updateData['status_lembur'])) {
            $pesan .= ' (Shift lembur ditolak, seluruh jam kerja tidak dihitung)';
        }

        return redirect()->back()->with('success', $pesan); 

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
    }
}

    /**
     * Detail presensi (Admin dan User terkait)
     */
    public function detail(Presensi $presensi)
    {
        // Validasi akses
        if (Auth::user()->role === 'pegawai' && $presensi->users_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $presensi->load(['user', 'jadwalShift.shift']);

        return view('dashboard.presensi.detail', compact('presensi'));
    }

    
private function updateGajiPokok($presensi)
{
    // Hanya untuk shift normal dan yang disetujui
    if ($presensi->jadwalShift->shift->isNormal() || 
        $presensi->status_approval !== Presensi::STATUS_APPROVAL_APPROVED) {
        return;
    }

    $bulan = Carbon::parse($presensi->tgl_presensi)->format('Y-m');
    
    GajiPokok::updateOrCreate(
        [
            'users_id' => $presensi->users_id,
            'periode_bulan' => $bulan,
        ],
        [
            'pengaturan_gaji_id' => 1, // Sesuaikan dengan kebutuhan
            'total_jam_kerja' => 0, // Akan dihitung ulang
            'total_gaji_pokok' => 0, // Akan dihitung ulang
            'status_pembayaran' => 0
        ]
    );

    // Recalculate total jam kerja untuk bulan tersebut
    $this->recalculateGajiPokok($presensi->users_id, $bulan);
}

private function recalculateGajiPokok($userId, $periode)
{
    $gajiPokok = GajiPokok::where('users_id', $userId)
                         ->where('periode_bulan', $periode)
                         ->first();
    
    if (!$gajiPokok) return;

    // Ambil semua presensi yang disetujui untuk shift normal dalam periode tersebut
    $presensiData = Presensi::where('users_id', $userId)
                            ->whereYear('tgl_presensi', Carbon::parse($periode)->year)
                            ->whereMonth('tgl_presensi', Carbon::parse($periode)->month)
                            ->where('status_approval', Presensi::STATUS_APPROVAL_APPROVED)
                            ->whereHas('jadwalShift.shift', function($query) {
                                $query->normal();
                            })
                            ->get();

    // Hitung total jam kerja dan total keterlambatan
    $totalJamKerjaMenit = 0;
    $totalMenitTerlambat = 0;

    foreach ($presensiData as $presensi) {
        $totalJamKerjaMenit += $presensi->jam_kerja_efektif ?: 0;
        $totalMenitTerlambat += $presensi->menit_terlambat ?: 0;
    }

    // Convert menit ke jam untuk jam kerja
    $totalJamKerjaDecimal = round($totalJamKerjaMenit / 60, 2);

    // Ambil tarif dari pengaturan gaji (sesuaikan dengan struktur tabel Anda)
    $tarifPerJam = 15000; // Bisa diambil dari $gajiPokok->pengaturanGaji->tarif_per_jam
    $tarifPotonganPerMenit = 500; // Bisa diambil dari $gajiPokok->pengaturanGaji->tarif_potongan_per_menit

    // Hitung gaji pokok
    $gajiKotor = $totalJamKerjaDecimal * $tarifPerJam;
    $totalPotongan = $totalMenitTerlambat * $tarifPotonganPerMenit;
    $gajiBersih = $gajiKotor - $totalPotongan;

    $gajiPokok->update([
        'total_jam_kerja' => $totalJamKerjaDecimal,
        'total_menit_terlambat' => $totalMenitTerlambat, // Tambahkan field ini
        'gaji_kotor' => $gajiKotor, // Tambahkan field ini
        'total_potongan' => $totalPotongan, // Tambahkan field ini
        'total_gaji_pokok' => max(0, $gajiBersih) // Pastikan tidak minus
    ]);
}
}