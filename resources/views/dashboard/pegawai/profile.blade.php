@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="text-center">Profil Pegawai</h1>
    <div class="card mx-auto border-0 shadow rounded-3" style="max-width: 800px;">
        <div class="card-body">
            <p class="border-bottom pb-2"><strong>Email: </strong>{{ $pegawai->id_akun }}</p>
            <p class="border-bottom pb-2"><strong>Nama:</strong> {{ $pegawai->nama_lengkap }}</p>
            <p class="border-bottom pb-2"><strong>Jabatan:</strong> {{ $pegawai->jabatan }}</p>
            <p class="border-bottom pb-2"><strong>Tanggal Masuk:</strong> {{ $pegawai->tgl_masuk->format('Y-m-d') }} </p>
            <p class="border-bottom pb-2"><strong>No HP:</strong> {{ $pegawai->no_hp }}</p>
            <p class="border-bottom pb-2"><strong>Alamat:</strong> {{ $pegawai->alamat }}</p>
        </div>
    </div>
</div>
@endsection
