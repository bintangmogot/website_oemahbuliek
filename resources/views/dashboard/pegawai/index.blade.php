@extends('layouts.app')

@section('content')
    <x-index-table
    title="Daftar Pegawai"
    :showFilter="false"
    {{-- exportRoute="admin.pegawai.export" --}}
    createRoute="admin.pegawai.create"
    createLabel="Tambah Pegawai"
        :columns="[
            ['label' => 'Email', 'field' => 'id_akun'],
            ['label' => 'Nama', 'field' => 'nama_lengkap'],
            ['label' => 'Jabatan', 'field' => 'jabatan'],
            ['label' => 'Tanggal Masuk', 'field' => 'tgl_masuk'],
            ['label' => 'No HP', 'field' => 'no_hp'],
        ]"
        :items="$pegawai"
        :showActions="auth()->user()->role === 'admin'"
        :routes="[
            'show' => 'admin.pegawai.show',
            'edit' => 'admin.pegawai.edit',
            'destroy' => 'admin.pegawai.destroy',
        ]"
    />
@endsection
