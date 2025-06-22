<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PengaturanGaji;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{

    public function __construct()
{
    $this->middleware(['auth','role:admin'])->except('showSelf');
    $this->middleware('auth')->only('showSelf');

}
     public function index()
    {
        $users = User::with('pengaturanGaji')
        ->select('id', 'pengaturan_gaji_id', 'email','role', 'nama_lengkap', 'jabatan', 'tgl_masuk', 'no_hp', 'status')
        ->paginate(10);

        return view('dashboard.user.index', compact('users'));
    }
    public function create()
    {
        $settings = PengaturanGaji::where('status',1)->pluck('nama','id');
         $user = null;
        return view('dashboard.user.create-user', compact('settings', 'user'));
    }
    public function store(StoreUserRequest $request)
    {
        $data = $request->validate([
            'pengaturan_gaji_id'=> 'nullable|exists:pengaturan_gaji,id',
            'email'        => 'required|email|unique:users,email',
            'password'     => 'required|string|min:6',
            'role'         => 'required|in:admin,pegawai',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:50',
            'tgl_masuk'    => 'required|date',
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|max:2048',
        ]);

        // Tangani upload file jika ada
        $data['foto_profil'] = $this->handleFotoProfilUpload($request);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return back()->with('success','User ' .$data['nama_lengkap'] . ' berhasil dibuat');

    }

    public function edit(User $user)
    {
        $settings = PengaturanGaji::where('status',1)->pluck('nama','id');
        
        return view('dashboard.user.edit-user', compact('user', 'settings'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validate([
            'pengaturan_gaji_id'=>'nullable|exists:pengaturan_gaji,id',
            'password'     => 'nullable|string|min:6',
            'role'         => 'required|in:admin,pegawai',
            'nama_lengkap' => 'required|string|max:255',
            'jabatan'      => 'required|string|max:50',
            'tgl_masuk'    => 'required|date',
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'nullable|string',
            'foto_profil'  => 'nullable|image|max:2048',
        ]);
        
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        // Tangani upload file jika ada
        $data['foto_profil'] = $this->handleFotoProfilUpload($request);

        $user->update($data);
        return back()->with('success','User ' .$data['nama_lengkap'] .  ' berhasil diperbarui');
    }

    public function show(User $user)
    {
        return view('dashboard.user.profile-user', compact('user'));

    }
    public function showSelf()
{
        $user = auth()->user();

    return view('dashboard.user.profile-user', compact('user'));
}
public function destroy(User $user)
{
    try {
        $nama = $user->nama_lengkap;
        $user->delete();
        return redirect()->route('user.index')
                         ->with('success', 'User ' . $nama . ' berhasil dihapus');
    } catch (\Illuminate\Database\QueryException $e) {
        // Kode SQLSTATE 23000 untuk constraint violation
        if ($e->getCode() === '23000') {
            return redirect()->route('user.index')
                             ->with('error', 'Tidak dapat menghapus user ini karena masih memiliki data terkait.');
        }
        throw $e;
    }
}



    // ========== PRIVATE HELPER ==========
    private function handleFotoProfilUpload(Request $request): string
    {
        if ($request->hasFile('foto_profil')) {
            return $request->file('foto_profil')->store('profil', 'public');
        }

        return 'profil/default.png';
    }

}
