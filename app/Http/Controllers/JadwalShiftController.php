<?php

namespace App\Http\Controllers;

use App\Models\JadwalShift;
use App\Models\Shift;
use Illuminate\Http\Request;
use App\Http\Requests\StoreJadwalShiftRequest;
use App\Http\Requests\UpdateJadwalShiftRequest;

class JadwalShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin'])->except(['index','show']);
        $this->middleware('auth')->only(['index','show']);
    }

    public function index()
    {
        $jadwals = JadwalShift::with('shift')->paginate(10);
        return view('dashboard.jadwal.index-jadwal', compact('jadwals'));
    }


    public function create()
    {
        $shifts = Shift::pluck('nama_shift','id');
        return view('dashboard.jadwal.create-jadwal', ['jadwal_shift' => null, 'shifts' => $shifts]);
    }

    public function store(StoreJadwalShiftRequest $request)
    {
    $data = $request->validated();
        // ubah array hari_kerja ke comma string 
    $data['hari_kerja'] = implode(',', $data['hari_kerja']);
    JadwalShift::create($data);
        return redirect()
               ->route('jadwal.index')
               ->with('success','Jadwal shift berhasil dibuat');
    }

    
    public function show(JadwalShift $jadwal_shift)
    {
        return view('dashboard.jadwal.show-jadwal', compact('jadwal_shift'));
    }

    public function edit(JadwalShift $jadwal_shift)
    {
        $shifts = Shift::pluck('nama_shift','id');
        return view('dashboard.jadwal.edit-jadwal', compact('jadwal_shift','shifts'));
    }

    public function update(UpdateJadwalShiftRequest $request, JadwalShift $jadwal_shift)
    {
    $data = $request->validated();
    $data['hari_kerja'] = implode(',', $data['hari_kerja']);
    $jadwal_shift->update($data);        
    return redirect()->route('jadwal.index')
                    ->with('success', 'Jadwal shift berhasil diperbarui');    
            }

    public function destroy(JadwalShift $jadwal_shift)
    {
        $jadwal_shift->delete();
        return redirect()
               ->route('jadwal.index')
               ->with('success','Jadwal shift berhasil dihapus');
    }
    
}
