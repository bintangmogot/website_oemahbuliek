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
     public function index()
    {
        $users = User::select('email','role')
                     ->paginate(10);

        return view('dashboard.admin.index', compact('users'));
    }
    public function create()
    {
        return view('dashboard.admin.create');
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,pegawai',
        ]);

        User::create([
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],   // gunakan apa yang dipilih di form
        ]);

        return redirect()->route('admin.user.create')
                         ->with('success','User berhasil dibuat');
    }

    public function show(User $user)
    {
        return view('dashboard.admin.profile', compact('admin'));

    }
    public function showSelf()
{
    $admin = User::where('email', auth()->user()->email)->firstOrFail();
    return view('dashboard.admin.profile', compact('admin'));
}
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.user.index')
                         ->with('success','User berhasil dihapus');
    }
}
