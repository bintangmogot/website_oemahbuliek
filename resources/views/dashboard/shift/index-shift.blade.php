@extends('layouts.app')

@section('content')

  <x-index-table
    title="Daftar Shift"
    createRoute="shift.create"
    createLabel="Tambah Shift"
    destroyRoute="admin.shift.destroy"
    :columns="[
      ['label'=>'Nama Shift',      'field'=>'nama_shift'],
      ['label'=>'Mulai',           'field'=>'jam_mulai', 'format'=>'H:i'],
      ['label'=>'Selesai',         'field'=>'jam_selesai', 'format'=>'H:i'],
      ['label'=>'Toleransi (mnt)', 'field'=>'toleransi_terlambat'],
      ['label'=>'Batas Lembur (mnt)','field'=>'batas_lembur_min'],
    ]"
    :items="$shifts"
    :showActions="auth()->user()->role === 'admin'"
    :routes="[
      'show'    => 'shift.show',
      'edit'    => 'shift.edit',
      'destroy' => 'shift.destroy',
    ]"
    routeKey="shift"
  />
@endsection
