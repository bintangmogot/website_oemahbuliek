@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">Profil Saya</h1>
    <div class="border-0 shadow bg-white rounded-3" style="max-width: 800px; margin: auto;">
        <div class="card-body">
            
        <div class="text-center mb-4">  
            <x-avatar :src="$user->foto_profil" size="150" />
        </div>
            <p class="border-bottom pb-2"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="border-bottom pb-2"><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
            <p class="border-bottom pb-2"><strong>Nama Lengkap:</strong> {{ $user->nama_lengkap }}</p>
            <p class="border-bottom pb-2"><strong>Jabatan:</strong> {{ $user->jabatan }}</p>
            <p class="border-bottom pb-2"><strong>Tanggal Masuk:</strong> {{ $user->tgl_masuk }}</p>
            <p class="border-bottom pb-2"><strong>No. HP:</strong> {{ $user->no_hp }}</p>
            <p class="border-bottom pb-2"><strong>Alamat:</strong> {{ $user->alamat }}</p>

    </div>
</div>
@endsection
