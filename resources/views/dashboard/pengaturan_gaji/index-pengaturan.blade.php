@extends('layouts.app')
@section('title', 'Daftar Pengaturan Gaji')
@section('content')
  <x-index-table
    title="Pengaturan Gaji"
    createRoute="pengaturan_gaji.create"
    createLabel="Tambah Pengaturan"
    destroyRoute="pengaturan_gaji.destroy"
    :columns="[
      ['label'=>'Nama','field'=>'nama'],
      ['label'=>'Tarif/Jam','field'=>'tarif_kerja_per_jam'],
      ['label'=>'Tarif Lembur','field'=>'tarif_lembur_per_jam'],
      ['label'=>'Potongan Telat','field'=>'potongan_terlambat_per_menit'],
      ['label'=>'Status','field'=>'status'],
    ]"
    :items="$settings"
    :showActions="true"
    :routes="['edit'=>'pengaturan_gaji.edit','destroy'=>'pengaturan_gaji.destroy']"
    routeKey="pengaturan_gaji"
  />
@endsection