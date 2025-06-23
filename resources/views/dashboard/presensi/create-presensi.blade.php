@extends('layouts.app')

@section('content')
  <x-form-layout title="Tambah Presensi" :backRoute="route('presensi.index')">

    <form action="{{ route('presensi.store') }}"
          method="POST">
      @csrf
      @include('dashboard.presensi.form-presensi')
      <button class="btn btn-success">Simpan</button>
    </form>
  </x-form-layout>
@endsection
