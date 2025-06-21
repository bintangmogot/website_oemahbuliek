@extends('layouts.app')

@section('content')
  <x-form-layout title="Tambah Jadwal Shift">
    <form action="{{ route('jadwal.store') }}" method="POST">
      @csrf
      @include('dashboard.jadwal.form-jadwal', ['jadwal_shift' => null])
    </form>
  </x-form-layout>
@endsection
