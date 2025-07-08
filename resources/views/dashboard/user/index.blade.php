@extends('layouts.app')
@section('title', 'Data User')

@section('content')
<x-session-status/>
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
      ['label'=> 'Jenis Gaji', 'field' => 'pengaturanGaji.nama'],
      ['label' => 'No HP', 'field' => 'no_hp'],
    ]"
    :items="$users"
    :showActions="auth()->user()->role === 'admin'"
    :routes="[
        'show' => 'user.show',
        'edit' => 'user.edit',
        'destroy' => 'user.destroy',
    ]"
    routeKey="user"
  />
@endsection
