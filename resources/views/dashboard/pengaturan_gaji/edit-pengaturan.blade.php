@extends('layouts.app')
@section('content')
  <x-form-layout title="💰 Edit Pengaturan Gaji" :backRoute="route('pengaturan_gaji.index')">
    <form action="{{ route('pengaturan_gaji.update', $pengaturan_gaji) }}" method="POST">
      @method('PUT')
      @csrf
      @include('dashboard.pengaturan_gaji.form-pengaturan')
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </x-form-layout>
@endsection