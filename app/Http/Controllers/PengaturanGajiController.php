<?php

namespace App\Http\Controllers;

use App\Models\PengaturanGaji;
use Illuminate\Http\Request;

class PengaturanGajiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','role:admin']);
    }

    public function index()
    {
        $settings = PengaturanGaji::paginate(10);
        return view('dashboard.pengaturan_gaji.index-pengaturan', compact('settings'));
    }

    public function create()
    {
        return view('dashboard.pengaturan_gaji.create-pengaturan');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string|unique:pengaturan_gaji,nama',
            'tarif_kerja_per_jam' => 'required|integer',
            'tarif_lembur_per_jam' => 'required|integer',
            'potongan_terlambat_per_menit' => 'required|integer',
            'status' => 'required|in:0,1',
        ]);

        PengaturanGaji::create($data);
        return redirect()->route('pengaturan_gaji.index')->with('success','Pengaturan gaji berhasil dibuat');
    }

    public function edit(PengaturanGaji $pengaturanGaji)
    {
        return view('dashboard.pengaturan_gaji.edit-pengaturan', ['pengaturan_gaji' => $pengaturanGaji]);
    }

    public function update(Request $request, PengaturanGaji $pengaturanGaji)
    {
        $data = $request->validate([
            'nama' => 'required|string|unique:pengaturan_gaji,nama,' . $pengaturanGaji->id,
            'tarif_kerja_per_jam' => 'required|integer',
            'tarif_lembur_per_jam' => 'required|integer',
            'potongan_terlambat_per_menit' => 'required|integer',
            'status' => 'required|in:0,1',
        ]);

        $pengaturanGaji->update($data);
        return back()->with('success','Pengaturan gaji berhasil diperbarui');
    }

    public function destroy(PengaturanGaji $pengaturanGaji)
    {
        $pengaturanGaji->delete();
        return redirect()->route('pengaturan_gaji.index')->with('success','Pengaturan gaji berhasil dihapus');
    }
}
