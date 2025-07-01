@extends('layouts.app')
@section('title', 'Edit Pengaturan Gaji')

@section('content')
    <x-form-layout 
        title="💰 Edit Pengaturan Gaji" 
        :backRoute="route('pengaturan_gaji.index')"
        :submitRoute="route('pengaturan_gaji.update', $pengaturan_gaji->id)"      
        submitMethod="PUT"                         
        submitLabel="Simpan Perubahan"
    >
        @include('dashboard.pengaturan_gaji.form-pengaturan', ['pengaturan_gaji' => $pengaturan_gaji])
    </x-form-layout>
@endsection