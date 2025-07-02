@extends('layouts.app')
@section('title', 'Catat Transaksi Stok')

@section('content')
  <x-form-layout
    title="📜 Catat Transaksi Stok Baru"
    :backRoute="route('riwayat-stok.index')"
    :submitRoute="route('riwayat-stok.store')"
    submitMethod="POST"
    formId="riwayat-stok-form"
    submitLabel="Simpan Transaksi"
  >
    @include('dashboard.inventaris.riwayat_stok.form')
  </x-form-layout>
@endsection