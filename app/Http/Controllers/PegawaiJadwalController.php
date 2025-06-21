<?php

namespace App\Http\Controllers;

use App\Models\PegawaiJadwal;
use App\Models\User;
use App\Models\JadwalShift;
use Illuminate\Http\Request;
use App\Http\Requests\StorePegawaiJadwalRequest;
use App\Http\Requests\UpdatePegawaiJadwalRequest;

class PegawaiJadwalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin'])->except(['index','show']);
        $this->middleware('auth')->only(['index','show']);
    }

public function index()
{
    $items = PegawaiJadwal::with(['pegawai', 'jadwalShift'])->get()->map(function($item) {
        return [
            'users_id'           => $item->users_id,
            'jadwal_shift_id'    => $item->jadwal_shift_id,
            'nama_lengkap'       => $item->pegawai->nama_lengkap,
            'periode'            => $item->jadwalShift->nama_periode,
            'shift'              => $item->jadwalShift->shift->nama_shift ?? 'Tidak ada shift',
            'hari_kerja'         => $item->jadwalShift->hari_kerja,
            'mulai_berlaku'      => $item->jadwalShift->mulai_berlaku,
            'berakhir_berlaku'   => $item->jadwalShift->berakhir_berlaku
                                ? $item->jadwalShift->berakhir_berlaku->format('d-m-Y')
                                : '',

        ];
    });
    
    return view('dashboard.pegawai-jadwal.index-pegawai-jadwal', compact('items'));
}

public function show($users_id, $jadwal_shift_id)
{
    $pegawai_jadwal = PegawaiJadwal::where('users_id', $users_id)
                                 ->where('jadwal_shift_id', $jadwal_shift_id)
                                 ->with(['pegawai', 'jadwalShift'])
                                 ->firstOrFail();
    
    return view('dashboard.pegawai-jadwal.show-pegawai-jadwal', compact('pegawai_jadwal'));
}

public function edit($users_id, $jadwal_shift_id)
{
    $pegawai_jadwal = PegawaiJadwal::where('users_id', $users_id)
                                 ->where('jadwal_shift_id', $jadwal_shift_id)
                                 ->firstOrFail();
    
    $users = User::pluck('nama_lengkap', 'id');
    $jadwals = JadwalShift::with('shift')->get()->keyBy('id');
    
    return view('dashboard.pegawai-jadwal.edit-pegawai-jadwal', compact('pegawai_jadwal', 'users', 'jadwals'));
}
    // public function index()
    // {
    //     $items = PegawaiJadwal::with(['pegawai','jadwalShift'])->paginate(10);
    //     return view('dashboard.pegawai-jadwal.index-pegawai-jadwal', compact('items'));
    // }

    // public function show(PegawaiJadwal $pegawai_jadwal)
    // {
    //     return view('dashboard.pegawai-jadwal.show-pegawai-jadwal', compact('pegawai_jadwal'));
    // }

    public function create()
    {
        $users  = User::pluck('nama_lengkap','id');
        $jadwals = JadwalShift::with('shift')->get()->keyBy('id');
        return view('dashboard.pegawai-jadwal.create-pegawai-jadwal', compact('users','jadwals'));
    }

    public function store(StorePegawaiJadwalRequest $request)
    {
    $validated = $request->validated();

    // Cek dulu apakah kombinasi sudah ada
    $exists = PegawaiJadwal::where('users_id', $validated['users_id'])
                           ->where('jadwal_shift_id', $validated['jadwal_shift_id'])
                           ->exists();

    if ($exists) {
        return back()->withInput()->with('error', 'Jadwal untuk pegawai ini sudah ada.');
    }

    // Jika belum ada, baru create
    PegawaiJadwal::create($validated);

    return redirect()->route('pegawai-jadwal.index')
                     ->with('success', 'Jadwal pegawai berhasil ditambahkan');
}

public function update(UpdatePegawaiJadwalRequest $request, $users_id, $jadwal_shift_id)
{
    $validated = $request->validated();
    
// Cek apakah kombinasi baru sudah ada
    $exists = PegawaiJadwal::where('users_id', $validated['users_id'])
                          ->where('jadwal_shift_id', $validated['jadwal_shift_id'])
                          ->exists();
    
    // Jika kombinasi baru sama dengan yang lama, tidak perlu update
    if ($users_id == $validated['users_id'] && $jadwal_shift_id == $validated['jadwal_shift_id']) {
        return back()->with('info', 'Tidak ada perubahan data');
    }
    
    // Jika kombinasi baru sudah ada dan berbeda dari yang lama
    if ($exists) {
        return back()->with('error', 'Kombinasi pegawai dan jadwal shift sudah ada');
    }
    
    // Hapus record lama
    PegawaiJadwal::where('users_id', $users_id)
                 ->where('jadwal_shift_id', $jadwal_shift_id)
                 ->delete();
    
    // Buat record baru
    PegawaiJadwal::create($validated);
    
    return redirect()->route('pegawai-jadwal.index')
                    ->with('success', 'Jadwal pegawai berhasil diperbarui');

}

public function destroy($users_id, $jadwal_shift_id)
{
    $deleted = PegawaiJadwal::where('users_id', $users_id)
                           ->where('jadwal_shift_id', $jadwal_shift_id)
                           ->delete();
    
    if ($deleted) {
        return redirect()
               ->route('pegawai-jadwal.index')
               ->with('success','Jadwal pegawai berhasil dihapus');
    } else {
        return redirect()
               ->route('pegawai-jadwal.index')
               ->with('error','Gagal menghapus jadwal pegawai');
    }
}
    // public function edit(PegawaiJadwal $pegawai_jadwal)
    // {
        
    //     $users  = User::pluck('nama_lengkap','id');
    //     $jadwals = JadwalShift::pluck('tanggal','id');
    //     return view('dashboard.pegawai-jadwal.edit-pegawai-jadwal', compact('pegawai_jadwal','users','jadwals'));
    // }

    // public function update(UpdatePegawaiJadwalRequest $request, PegawaiJadwal $pegawai_jadwal)
    // {
    //     $pegawai_jadwal->update($request->validated());
    //     return back()->with('success','Jadwal pegawai berhasil diperbarui');
    // }

    // public function destroy(PegawaiJadwal $pegawai_jadwal)
    // {
    //     $pegawai_jadwal->delete();
    //     return redirect()
    //            ->route('pegawai-jadwal.index')
    //            ->with('success','Jadwal pegawai berhasil dihapus');
    // }
}
