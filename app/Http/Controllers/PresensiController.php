<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;

class PresensiController extends Controller
{
public function __construct()
{
    // Semua method → admin & pegawai bisa akses
    $this->middleware(['auth','role:admin|pegawai']);

    // Kecuali destroy → hanya admin
    $this->middleware(['auth','role:admin'])
         ->only(['destroy']);
}

    public function index()
    {
        $presensi = Presensi::with(['user','jadwal'])->paginate(10);
        return view('dashboard.presensi.index-presensi', compact('presensi'));
    }

    public function show(Presensi $presensi)
    {
        return view('dashboard.presensi.show-presensi', compact('presensi'));
    }

    public function create()
    {
        return view('dashboard.presensi.create-presensi', [
            'presensi' => new Presensi(),
            'users'    => \App\Models\User::pluck('nama_lengkap','id'),
            'jadwals'  => \App\Models\JadwalShift::pluck('mulai_berlaku','id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_jadwal'        => 'required|exists:jadwal_shift,id',
            'id_users'         => 'required|exists:users,id',
            'tgl_presensi'     => 'required|date',
            'shift_ke'         => 'required|integer',
            'status_kehadiran' => 'required|string|max:50',
            'keterangan'       => 'nullable|string',
        ]);

        Presensi::create($data);

        return redirect()
               ->route('presensi.index')
               ->with('success', 'Presensi berhasil dibuat');
    }

    public function edit(Presensi $presensi)
    {
        return view('dashboard.presensi.form-presensi', [
            'presensi' => $presensi,
            'users'    => \App\Models\User::pluck('nama_lengkap','id'),
            'jadwals'  => \App\Models\JadwalShift::pluck('mulai_berlaku','id'),
        ]);
    }

    public function update(Request $request, Presensi $presensi)
    {
        $data = $request->validate([
            'tgl_presensi'     => 'required|date',
            'shift_ke'         => 'required|integer',
            'status_kehadiran' => 'required|string|max:50',
            'keterangan'       => 'nullable|string',
        ]);

        $presensi->update($data);

        return back()->with('success', 'Presensi berhasil diperbarui');
    }

    public function destroy(Presensi $presensi)
    {
        $presensi->delete();

        return redirect()
               ->route('admin.presensi.index')
               ->with('success', 'Presensi berhasil dihapus');
    }
}
