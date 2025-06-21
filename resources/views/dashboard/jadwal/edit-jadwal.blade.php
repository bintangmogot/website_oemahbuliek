@extends('layouts.app')

@section('content')
  <x-form-layout title="Edit Jadwal Shift">
    <form action="{{ route('jadwal.update', $jadwal_shift) }}" method="POST">
      @csrf
      @method('PUT')
      @include('dashboard.jadwal.form-jadwal', ['jadwal_shift' => $jadwal_shift])
    </form>
  </x-form-layout>
@endsection
