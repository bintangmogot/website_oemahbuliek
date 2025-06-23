@extends('layouts.app')
@section('title', 'Buat Shift')

@section('content')
  <x-form-layout 
    title="⏰ Tambah Shift" 
    :backRoute="route('shift.index')"
    :submitRoute="route('shift.store')"        
    submitMethod="POST"                       
    formId="shift-form"                      
    submitLabel="Simpan"                 
  >
    @include('dashboard.shift.form-shift')
  </x-form-layout>
@endsection
