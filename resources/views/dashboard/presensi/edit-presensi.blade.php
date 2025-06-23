@extends('layouts.app')
@section('title', 'Edit Presensi')

@section('content')
  <x-form-layout title="Edit Presensi" :backRoute="route('presensi.index')">
    <form action="{{ route('admin.presensi.update', $presensi) }}"
          method="POST">
      @csrf
      @method('PUT')
      @include('dashboard.presensi.form-presensi')
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </x-form-layout>
@endsection
