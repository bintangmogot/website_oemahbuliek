@extends('layouts.app')

@section('content')
  <x-index-table
    title="Daftar User"
    createRoute="user.create"
    createLabel="Tambah User"
    destroyRoute="user.destroy"
    :columns="[
      ['label' => 'Email', 'field' => 'email'],
      ['label' => 'Role', 'field' => 'role'],
      ['label' => 'Nama', 'field' => 'nama_lengkap'],
      ['label' => 'Jabatan', 'field' => 'jabatan'],
      ['label' => 'No HP', 'field' => 'no_hp'],
    ]"
    :items="$user"
    :showActions="auth()->user()->role === 'admin'"
    :routes="[
        'show' => 'user.show',
        'edit' => 'user.edit',
        'destroy' => 'user.destroy',
    ]"
    routeKey="user"
  />
@endsection
