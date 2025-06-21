@extends('layouts.app')

@section('content')
  <x-form-layout title="Edit Jadwal Pegawai">
<form action="{{ route('pegawai-jadwal.update', [$pegawai_jadwal->users_id, $pegawai_jadwal->jadwal_shift_id]) }}" method="POST">
      @csrf
      @method('PUT')
      @include('dashboard.pegawai-jadwal.form-pegawai-jadwal')

    </form>
  </x-form-layout>
@endsection
