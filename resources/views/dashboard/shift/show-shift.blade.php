@extends('layouts.app')
@section('title', 'Shift')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">⏰ Detail Shift</h1>
    <div class="border-0 shadow bg-white rounded-3 p-4" style="max-width: 600px; margin: auto;">
        <p><strong>Nama Shift:</strong> {{ $shift->nama_shift }}</p>
        <p><strong>Jenis Shift: </strong> {{ $shift->is_shift_lembur == 1 ? 'Shift Lembur' : 'Shift Normal' }}</p>
        <p><strong>Jam Mulai:</strong> {{ $shift->jam_mulai }}</p>
        <p><strong>Jam Selesai:</strong> {{ $shift->jam_selesai }}</p>
        <p><strong>Toleransi Terlambat:</strong> {{ $shift->toleransi_terlambat }} menit</p>
        <p><strong>Batas Lembur:</strong> {{ $shift->batas_lembur_min }} menit</p>
        <p><strong>Status:</strong> 
            <span class="badge {{ $shift->status == 1 ? 'bg-success' : 'bg-secondary' }}">
                {{ $shift->status == 1 ? 'Aktif' : 'Nonaktif' }}
            </span>
        </p>    
    </div>
</div>
@endsection
