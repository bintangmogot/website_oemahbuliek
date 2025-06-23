@extends('layouts.app')
@section('title', 'Edit User')

@section('content')
  <x-form-layout
    title="👤 Edit User"
    :backRoute="route('user.index')"          {{-- tombol Kembali --}}
    :submitRoute="route('user.update', $user->id)"        {{-- action form --}}
    submitMethod="PUT"                       {{-- method form --}}
    formId="user-form"                        {{-- ID form unik --}}
    submitLabel="Simpan Perubahan"                 {{-- label tombol Simpan --}}
  >
    @include('dashboard.user.form-user', ['settings' => $settings, 'user' => $user])
  </x-form-layout>
@endsection
