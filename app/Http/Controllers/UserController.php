<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $user = User::select('id', 'email','role', 'nama_lengkap', 'jabatan', 'tgl_masuk', 'no_hp')
                     ->paginate(10);

        return view('dashboard.user.index', compact('user'));
    }
    public function create()
    {
        return view('dashboard.user.create-user', ['user' => null]);
    }
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        // Tangani upload file jika ada
        $data['foto_profil'] = $this->handleFotoProfilUpload($request);

        User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],   // gunakan apa yang dipilih di form
            'nama_lengkap'  => $data['nama_lengkap'],
            'jabatan'       => $data['jabatan'],
            'tgl_masuk'     => $data['tgl_masuk'],
            'no_hp'         => $data['no_hp'],
            'alamat'        => $data['alamat'],
            'foto_profil'   => $data['foto_profil'],
        ]);

        return back()->with('success','User ' .$data['nama_lengkap'] . ' berhasil dibuat');

    }

    public function edit(User $user)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Akses ditolak.');       
        }

        // Ambil user yang bisa dipilih di dropdown
        $users = User::where('role', 'admin')
                    ->orWhere('email', $user->email) // bukan $user->id
                    ->pluck('email','email');

        return view('dashboard.user.edit-user', compact('user', 'users'));
    }


    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
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
        
        $nama = $user->nama_lengkap;        
        $user->delete();
        return redirect()->route('admin.user.index')
                         ->with('success','User ' . $nama . ' berhasil dihapus');
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
