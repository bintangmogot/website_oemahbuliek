@extends('layouts.app')
@section('content')
  <x-form-layout title="Tambah User">

    <form action="{{ route('admin.user.store') }}"
          method="POST"
          enctype="multipart/form-data">
      @include('dashboard.user.form-user')
      <button class="btn btn-success">Simpan</button>
    </form>
  </x-form-layout>
@endsection
