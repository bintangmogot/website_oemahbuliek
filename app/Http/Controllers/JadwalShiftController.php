<?php

namespace App\Http\Controllers;

use App\Models\JadwalShift;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;


class JadwalShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin'])->except(['index', 'show']);
        $this->middleware('auth')->only('index','show');
    }

    public function index()
    {
    // Cek role user
    if (auth()->user()->role === 'admin') {
        $jadwalShifts = JadwalShift::with(['shift', 'user']) // relasi shift & user
            ->orderBy('tanggal', 'desc')
            ->paginate(15);
    } else {
        $jadwalShifts = JadwalShift::with(['shift'])
            ->where('users_id', auth()->id()) // Langsung filter berdasarkan users_id

            ->where('status', 1)
            ->orderBy('tanggal', 'asc')
            ->paginate(10);
    }

        return view('dashboard.jadwal.index-jadwal', compact('jadwalShifts'));
    }

    public function create()
    {
        $shifts = Shift::where('status', 1)->get(['id', 'nama_shift', 'jam_mulai', 'jam_selesai']);
        $users = User::where('role', 'pegawai')
            ->where('status', 1)
            ->get(['id', 'nama_lengkap', 'jabatan']);

        return view('dashboard.jadwal.create-jadwal', compact('shifts', 'users'));
    }

     public function store(Request $request)
    {
        // Validation rules
        $request->validate([
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shift,id',
            'tanggal' => 'required|date|after_or_equal:today',
            'status' => 'nullable|in:0,1',
        ]);

        // Custom validation untuk memastikan tidak ada duplikasi
        // kombinasi user_id + tanggal + shift_id
        // Satu pegawai tidak bisa dijadwalkan di shift yang sama pada tanggal yang sama

        $duplicates = [];
        foreach ($request->user_ids as $userId) {
            $exists = JadwalShift::where('users_id', $userId)
                ->where('tanggal', $request->tanggal)
                ->where('shift_id', $request->shift_id)
                ->exists();
            
            if ($exists) {
                $user = User::find($userId);
                $shift = Shift::find($request->shift_id);
                $duplicates[] = "{$user->nama_lengkap} sudah memiliki jadwal {$shift->nama_shift} pada tanggal " . date('d-m-Y', strtotime($request->tanggal));
            }
        }

        if (!empty($duplicates)) {
            return back()->withErrors([
                'user_ids' => 'Duplikasi jadwal ditemukan: ' . implode(', ', $duplicates)
            ])->withInput();
        }

        try {
            DB::beginTransaction();
            
            // Buat jadwal untuk setiap user yang dipilih
            $createdSchedules = [];
            foreach ($request->user_ids as $userId) {
                $jadwalShift = JadwalShift::create([
                    'users_id' => $userId,
                    'shift_id' => $request->shift_id,
                    'tanggal' => $request->tanggal,
                    'status' => $request->status ?? 1,
                ]);
                $createdSchedules[] = $jadwalShift;
            }

            DB::commit();

            $userCount = count($request->user_ids);
            $message = "Berhasil menambahkan jadwal shift untuk {$userCount} pegawai.";
            
            return redirect()->route('jadwal-shift.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat menyimpan jadwal: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function update(Request $request, JadwalShift $jadwalShift)
    {
        // Validation rules
        $request->validate([
            'users_id' => 'required|exists:users,id',
            'shift_id' => 'required|exists:shift,id',
            'tanggal' => 'required|date',
            'status' => 'nullable|in:0,1,2',
        ]);

        // Custom validation untuk memastikan tidak ada duplikasi
        // (kecuali record yang sedang di-update)
        $exists = JadwalShift::where('users_id', $request->users_id)
            ->where('tanggal', $request->tanggal)
            ->where('shift_id', $request->shift_id)
            ->where('id', '!=', $jadwalShift->id) // Ignore current record
            ->exists();

        if ($exists) {
            $user = User::find($request->users_id);
            $shift = Shift::find($request->shift_id);
            return back()->withErrors([
                'users_id' => "{$user->nama_lengkap} sudah memiliki jadwal {$shift->nama_shift} pada tanggal " . date('d-m-Y', strtotime($request->tanggal))
            ])->withInput();
        }

        try {
            $jadwalShift->update([
                'users_id' => $request->users_id,
                'shift_id' => $request->shift_id,
                'tanggal' => $request->tanggal,
                'status' => $request->status ?? $jadwalShift->status,
            ]);

            return redirect()->route('jadwal-shift.index')
                ->with('success', 'Jadwal shift berhasil diperbarui.');

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Terjadi kesalahan saat memperbarui jadwal: ' . $e->getMessage()
            ])->withInput();
        }
    }

    public function show(JadwalShift $jadwalShift)
    {
        $jadwalShift->load(['shift', 'user']);
        return view('dashboard.jadwal.show-jadwal', compact('jadwalShift'));
    }

    public function edit(JadwalShift $jadwalShift)
    {
        $shifts = Shift::where('status', 1)->get(['id', 'nama_shift', 'jam_mulai', 'jam_selesai']);
        $users = User::where('role', 'pegawai')
            ->where('status', 1)
            ->get(['id', 'nama_lengkap']);

        return view('dashboard.jadwal.edit-jadwal', compact('jadwalShift', 'shifts', 'users'));
    }



    public function destroy(JadwalShift $jadwalShift)
    {
        try {
            $userName = $jadwalShift->user->nama_lengkap;
            $tanggal = $jadwalShift->tanggal->format('d/m/Y');
            
            $jadwalShift->delete();
            
            return redirect()->route('jadwal-shift.index')
                ->with('success', "Jadwal shift {$userName} tanggal {$tanggal} berhasil dihapus");
        } catch (\Exception $e) {
            return redirect()->route('jadwal-shift.index')
                ->with('error', 'Tidak dapat menghapus jadwal shift karena masih memiliki data terkait.');
        }
    }


//  Menampilkan daftar shift untuk dipilih
 
public function pilihShift()
    {
        $shifts = Shift::where('status', 1)->get(); // Ambil semua shift yang aktif
        return view('dashboard.jadwal.pilih-shift', compact('shifts'));
    }
    // Method untuk melihat detail jadwal per shift
public function detailShift($shift_id, $tanggal)
{
    try {
        // Validasi shift_id ada di database
        $shift = Shift::findOrFail($shift_id);
        
        // Validasi format tanggal
        $tanggal = \Carbon\Carbon::parse($tanggal)->format('Y-m-d');
        
        // Ambil data jadwal shift berdasarkan shift_id dan tanggal
        $jadwalShifts = JadwalShift::with(['user'])
            ->where('shift_id', $shift_id)
            ->where('tanggal', $tanggal)
            ->get();

        return view('dashboard.jadwal.detail-shift', compact('shift', 'jadwalShifts', 'tanggal'));
        
    } catch (\Exception $e) {
        return redirect()->route('jadwal-shift.index')
            ->with('error', 'Data shift tidak ditemukan atau tanggal tidak valid.');
    }
}
}