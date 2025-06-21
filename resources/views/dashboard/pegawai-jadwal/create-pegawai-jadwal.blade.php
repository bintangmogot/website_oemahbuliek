@extends('layouts.app')

@section('content')
  <x-form-layout title="Tambah Jadwal Pegawai">
    <form action="{{ route('pegawai-jadwal.store') }}" method="POST">
      @include('dashboard.pegawai-jadwal.form-pegawai-jadwal')
    </form>
  </x-form-layout>
@endsection
