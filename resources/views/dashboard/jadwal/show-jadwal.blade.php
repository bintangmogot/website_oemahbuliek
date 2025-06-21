@extends('layouts.app')

@section('content')
<div class="container py-3">
    <h1 class="fw-bold text-center">Detail Jadwal Shift</h1>
    <div class="border-0 shadow bg-white rounded-3 p-4" style="max-width: 600px; margin: auto;">
        
        <p><strong>Nama Periode:</strong> {{ $jadwal_shift->nama_periode }}</p>
        <p><strong>Shift:</strong> {{ $jadwal_shift->shift->nama_shift }}</p>
        
        <p><strong>Hari Kerja:</strong>
            @php
              $map = [
                'Mon'=>'Senin','Tue'=>'Selasa','Wed'=>'Rabu',
                'Thu'=>'Kamis','Fri'=>'Jumat','Sat'=>'Sabtu','Sun'=>'Minggu',
              ];
            @endphp
            {{ implode(', ', collect(explode(',', $jadwal_shift->hari_kerja))
                ->map(fn($d) => $map[$d] ?? $d)
                ->toArray()) }}
        </p>
        
        <p><strong>Periode Berlaku:</strong>
            {{ optional($jadwal_shift->mulai_berlaku)->format('d-m-Y') }}
            -
            {{ optional($jadwal_shift->berakhir_berlaku)->format('d-m-Y') ?: '∞' }}
        </p>
        
    </div>
</div>
@endsection
