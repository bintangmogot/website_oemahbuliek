@extends('layouts.app')

@section('content')
  <x-form-layout title="Edit Shift">
    <form action="{{ route('shift.update', $shift) }}" method="POST">
      @csrf
      @method('PUT')
      @include('dashboard.shift.form-shift', ['shift' => $shift])
    </form>
  </x-form-layout>
@endsection
