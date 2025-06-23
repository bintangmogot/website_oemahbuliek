@extends('layouts.app')
@section('title', 'Tambah User')

@section('content')
  <x-form-layout
    title="👤 Tambah User"
    :backRoute="route('user.index')"          {{-- tombol Kembali --}}
    :submitRoute="route('user.store')"        {{-- action form --}}
    submitMethod="POST"                       {{-- method form --}}
    formId="user-form"                        {{-- ID form unik --}}
    submitLabel="Simpan User"                 {{-- label tombol Simpan --}}
  >
    @include('dashboard.user.form-user', ['settings' => $settings])
  </x-form-layout>
@endsection
