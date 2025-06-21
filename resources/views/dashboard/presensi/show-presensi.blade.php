@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">Detail Presensi</h1>
    <div class="border-0 shadow bg-white rounded-3 p-4" style="max-width: 800px; margin: auto;">
        <p><strong>Pegawai:</strong> {{ $presensi->user->nama_lengkap }}</p>
        <p><strong>Tanggal:</strong> {{ $presensi->jadwal->tanggal }}</p>
        <p><strong>Shift Ke-:</strong> {{ $presensi->shift_ke }}</p>
        <p><strong>Jam Masuk:</strong> {{ optional($presensi->jam_masuk)->format('Y-m-d H:i') }}</p>
        <p><strong>Jam Keluar:</strong> {{ optional($presensi->jam_keluar)->format('Y-m-d H:i') }}</p>
        <p><strong>Status Kehadiran:</strong> {{ $presensi->status_kehadiran }}</p>
        <p><strong>Keterangan:</strong> {{ $presensi->keterangan }}</p>
    </div>
</div>
@endsection
