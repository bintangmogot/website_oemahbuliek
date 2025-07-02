@extends('layouts.app')
@section('title', 'Tambah Bahan Baku')

@section('content')
  <x-form-layout
    title="📦 Tambah Bahan Baku Baru"
    :backRoute="route('bahan-baku.index')"
    :submitRoute="route('bahan-baku.store')"
    submitMethod="POST"
    formId="bahan-baku-form"
    submitLabel="Simpan Bahan Baku"
  >
    @include('dashboard.inventaris.bahan_baku.form')
  </x-form-layout>
@endsection