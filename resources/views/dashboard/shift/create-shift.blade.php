@extends('layouts.app')

@section('content')
  <x-form-layout title="Tambah Shift">
    <form action="{{ route('shift.store') }}" 
        method="POST">
      @csrf
      @include('dashboard.shift.form-shift')
    </form>
  </x-form-layout>
@endsection
