@extends('layouts.app')
@section('title', 'Buat Pengaturan Gaji')

@section('content')
    <x-form-layout 
        title="💰 Tambah Pengaturan Gaji" 
        :backRoute="route('pengaturan_gaji.index')"
        :submitRoute="route('pengaturan_gaji.store')"      
        submitMethod="POST"                         
        submitLabel="Simpan"                    
    >
        @include('dashboard.pengaturan_gaji.form-pengaturan')
    </x-form-layout>
@endsection