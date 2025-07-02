@extends('layouts.app')
@section('title', 'Edit Bahan Baku')

@section('content')
  <x-form-layout
    title="📦 Edit Bahan Baku"
    :backRoute="route('bahan-baku.index')"
    :submitRoute="route('bahan-baku.update', $item->id)"
    submitMethod="PUT"
    formId="bahan-baku-form"
    submitLabel="Simpan Perubahan"
  >
    @include('dashboard.inventaris.bahan_baku.form', ['item' => $item])
  </x-form-layout>
@endsection