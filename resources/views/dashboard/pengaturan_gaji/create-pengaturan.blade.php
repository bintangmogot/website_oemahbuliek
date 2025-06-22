@extends('layouts.app')
@section('content')
  <x-form-layout title="Tambah Pengaturan Gaji">
    <form action="{{ route('pengaturan_gaji.store') }}" method="POST">
      @csrf
      @include('dashboard.pengaturan_gaji.form-pengaturan')
      <button class="btn btn-success">Simpan</button>
    </form>
  </x-form-layout>
@endsection