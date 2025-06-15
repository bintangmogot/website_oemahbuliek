@extends('layouts.app')

@section('content')
  <x-index-table
    title="Daftar User"
    createRoute="admin.user.create"
    createLabel="Tambah User"
    destroyRoute="admin.user.destroy"
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
        'show' => 'admin.user.show',
        'edit' => 'admin.user.edit',
        'destroy' => 'admin.user.destroy',
    ]"
    routeKey="user"
  />
@endsection
