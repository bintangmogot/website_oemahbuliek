@extends('layouts.app')

@section('content')
  <x-index-table
    title="Jadwal Shift"
    createRoute="jadwal.create"
    createLabel="Tambah Jadwal"
    :columns="[
      ['label' => 'Shift',         'field' => 'shift.nama_shift'],
      ['label' => 'Periode',       'field' => 'nama_periode'],
      ['label' => 'Mulai Berlaku', 'field' => 'mulai_berlaku'],
      ['label' => 'Berakhir',      'field' => 'berakhir_berlaku'],
      ['label' => 'Hari Kerja',    'field' => 'hari_kerja_list'], 
    ]"
    :items="$jadwals"
    :showActions="auth()->user()->role === 'admin'"
    :routes="[
      'show'    => 'jadwal.show',
      'edit'    => 'jadwal.edit',
      'destroy' => 'jadwal.destroy',
    ]"
    routeKey="jadwal_shift"
  />
@endsection
