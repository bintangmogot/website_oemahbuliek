@extends('layouts.app')

@section('content')
  <x-index-table
    title="Daftar Presensi"
    createRoute="presensi.create"
    createLabel="Tambah Presensi"
    destroyRoute="presensi.destroy"
    :columns="[
      ['label'=>'Pegawai',     'field'=>'user.nama_lengkap'],
      ['label'=>'Tanggal',     'field'=>'jadwal.tanggal'],
      ['label'=>'Shift Ke-',    'field'=>'shift_ke'],
      ['label'=>'Masuk',       'field'=>'jam_masuk'],
      ['label'=>'Keluar',      'field'=>'jam_keluar'],
      ['label'=>'Status',      'field'=>'status_kehadiran'],
    ]"
    :items="$presensi"
    :showActions="auth()->user()->role === 'admin'"
    :routes="[
      'show'    => 'presensi.show',
      'edit'    => 'presensi.edit',
      // 'destroy' => 'presensi.destroy',
    ]"
    routeKey="presensi"
  />
@endsection
