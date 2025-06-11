@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Profil Pegawai</h1>
    <div class="card">
        <div class="card-body">
            <p><strong>Email: </strong>{{ $pegawai->id_akun }}</p>
            <p><strong>Nama:</strong> {{ $pegawai->nama_lengkap }}</p>
            <p><strong>Jabatan:</strong> {{ $pegawai->jabatan }}</p>
            <p><strong>Tanggal Masuk:</strong> {{ $pegawai->tgl_masuk->format('Y-m-d') }} </p>
            <p><strong>No HP:</strong> {{ $pegawai->no_hp }}</p>
            <p><strong>Alamat:</strong> {{ $pegawai->alamat }}</p>
        </div>
    </div>
</div>
@endsection
