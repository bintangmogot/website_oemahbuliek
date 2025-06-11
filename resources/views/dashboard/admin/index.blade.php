@extends('layouts.app')

@section('content')
  <x-index-table-user
    title="Daftar User"
    createRoute="admin.user.create"
    createLabel="Tambah User"
    destroyRoute="admin.user.destroy"
    :columns="[
      ['label' => 'Email', 'field' => 'email'],
      ['label' => 'Role', 'field' => 'role'],
    ]"
    :items="$users"
  />
@endsection
