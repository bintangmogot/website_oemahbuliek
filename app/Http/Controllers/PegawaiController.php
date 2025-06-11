<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePegawaiRequest;
use App\Http\Requests\UpdatePegawaiRequest;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;



class PegawaiController extends Controller
{

public function index()
{
    if (auth()->user()->role === 'pegawai') {
        return view('dashboard.pegawai.index', [
            'pegawai' => Pegawai::where('id_akun', auth()->user()->email)->paginate(10)
        ]);
    }

    // Admin bisa lihat semua data
    return view('dashboard.pegawai.index', [
        'pegawai' => Pegawai::paginate(10)
    ]);
}


    public function create()
    {
                
        if (auth()->user()->role !== 'admin') {
    abort(403, 'Akses ditolak.');
    }
        // ambil user pegawai yang belum punya pegawai
        $users = User::where('role','pegawai')
                    ->whereDoesntHave('relationtopegawai')
                    ->pluck('email');
                    
        return view('dashboard.pegawai.create', compact('users'));

    }

public function store(Request $request)
{
    if (auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak.');
    }

        $data = $request->validate([
            'id_akun'      => 'required|email|exists:users,email|unique:pegawai,id_akun',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:50',
            'tgl_masuk'    => 'required|date',
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'nullable|string',
        ]);

        Pegawai::create($data);

        return redirect()->route('admin.pegawai.create')
                         ->with('success','Data pegawai berhasil dibuat');
    
}

    public function show(Pegawai $pegawai)
    {
        return view('dashboard.pegawai.profile', compact('pegawai'));

    }

    public function showSelf()
{
    $pegawai = Pegawai::where('id_akun', auth()->user()->email)->firstOrFail();
    return view('dashboard.pegawai.profile', compact('pegawai'));
}
    public function edit(Pegawai $pegawai)
    {
        if (auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak.');       
        }

        $users = User::where('role','pegawai')
                    ->whereDoesntHave('relationtopegawai')
                    ->orWhere('email',$pegawai->id_akun)
                    ->pluck('email','email');
        return view('dashboard.pegawai.edit', compact('pegawai','users'));

    }

    public function update(UpdatePegawaiRequest $request, Pegawai $pegawai)
    {
        if (auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak.');       
        }   
        $pegawai->update($request->except('id_akun'));

        return redirect()->route('admin.pegawai.index')
                        ->with('success','Pegawai berhasil diubah');
    }

    public function destroy(Pegawai $pegawai)
    {
        $pegawai->delete();
        return redirect()->route('admin.pegawai.index')
                        ->with('success','Pegawai berhasil dihapus');
                        
        if (auth()->user()->role !== 'admin') {
        abort(403, 'Akses ditolak.');       
        }
    }
}
