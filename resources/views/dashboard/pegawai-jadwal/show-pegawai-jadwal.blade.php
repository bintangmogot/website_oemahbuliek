@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">Detail Jadwal Pegawai</h1>
    <div class="border-0 shadow bg-white rounded-3 p-4" style="max-width:600px;margin:auto;">
        <p><strong>Pegawai:</strong> {{ $pegawai_jadwal->pegawai->nama_lengkap }}</p>
        <p><strong>Periode:</strong> {{ $pegawai_jadwal->jadwalShift->nama_periode }}</p>
        <p><strong>Mulai Berlaku:</strong> 
           {{ optional($pegawai_jadwal->jadwalShift->mulai_berlaku)->format('d-m-Y') }}
        </p>
        <p><strong>Berakhir Berlaku:</strong> 
           {{ optional($pegawai_jadwal->jadwalShift->berakhir_berlaku)->format('d-m-Y') ?: '∞' }}
        </p>
        <p><strong>Shift:</strong> {{ $pegawai_jadwal->jadwalShift->shift->nama_shift }}</p>
    </div>
</div>
@endsection
