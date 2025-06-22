@extends('layouts.app')
@section('content')
  <x-form-layout title="Tambah User">
    @php $user = null; @endphp

    <form action="{{ route('user.store') }}"
          method="POST"
          enctype="multipart/form-data">
      @include('dashboard.user.form-user', ['settings' => $settings])      
      <button class="btn btn-success">Simpan</button>
    </form>
  </x-form-layout>
@endsection
