<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\JadwalShift;
use App\Models\GajiLembur;
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
        if ($request->filled('status_approval')) {
            $query->where('status_approval', $request->status_approval);
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tgl_presensi', $request->tanggal);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('users_id', $request->user_id);
        }

        $presensi = $query->paginate(20);
        
        // Data untuk filter
        $pendingCount = Presensi::where('status_approval', Presensi::STATUS_APPROVAL_PENDING)->count();

        return view('dashboard.presensi.admin.index', compact('presensi', 'pendingCount'));
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

            $presensi->update([
                'jam_masuk' => $jamMasuk,
                'foto_masuk' => $fotoPath,
                'menit_terlambat' => $menitTerlambat,
                'status_kehadiran' => $statusKehadiran,
                'status_approval' => $statusApproval
            ]);

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
            // Jika pulang tepat waktu atau lebih lambat
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
                    'total_jam_lembur' => $jamLembur,
                    'total_gaji_lembur' => 0, // Akan dihitung nanti berdasarkan pengaturan gaji
                    'status_pembayaran' => 0
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

                    if ($menitOvertime > 0) {
                        $warnings[] = [
                            'title' => 'Informasi: Overtime Tidak Dihitung',
                            'message' => "Anda pulang {$menitOvertime} menit setelah jam kerja, namun belum mencapai batas minimum lembur ({$batasLemburMin} menit).",
                            'type' => 'overtime_minimal'
                        ];
                    }
                }
            } else {
                // Pulang tepat waktu
                $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO;
                $menitOvertime = 0;

            }
            
            // Pertahankan status kehadiran yang sudah ada (present/late)
            // Kecuali jika belum di-set, maka set sebagai present
            if (!isset($updateData['status_kehadiran'])) {
                $updateData['status_kehadiran'] = $presensi->status_kehadiran ?: Presensi::STATUS_KEHADIRAN_PRESENT;
            }
        }

        $presensi->update($updateData);

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
                'menit_overtime' => $menitOvertime ?? 0
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

            // Jika ada lembur pending, approve juga
            if ($presensi->status_lembur === Presensi::STATUS_LEMBUR_OVERTIME) {
                $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_APPROVED;
            }

            $presensi->update($updateData);

            DB::commit();

            return redirect()->back()->with('success', 'Presensi berhasil disetujui');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
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
        if ($presensi->status_lembur === Presensi::STATUS_LEMBUR_OVERTIME) {
            $updateData['status_lembur'] = Presensi::STATUS_LEMBUR_NO;
            
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

        $presensi->update($updateData);

            // Jika lembur ditolak, hapus record gaji lembur
            if (isset($updateData['status_lembur']) && $updateData['status_lembur'] === Presensi::STATUS_LEMBUR_NO) {
                GajiLembur::where('presensi_id', $presensi->id)->delete();
            }

        DB::commit();

        return redirect()->back()->with('success', 'Presensi berhasil ditolak dengan alasan: ' . $request->catatan_admin);

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
}