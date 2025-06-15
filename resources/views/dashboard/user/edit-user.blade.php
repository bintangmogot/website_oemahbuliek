@extends('layouts.app')
@section('content')
  <x-form-layout title="Edit User">
    <form action="{{ route('admin.user.update', $user) }}"
          method="POST"
          enctype="multipart/form-data">
      @include('dashboard.user.form-user')
      <button class="btn btn-primary">Simpan Perubahan</button>
    </form>
  </x-form-layout>
@endsection
