@extends('layouts.app')

@section('content')
  <x-form-layout title="Edit Data Pegawai">

    @if($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('admin.pegawai.update', $pegawai) }}" method="POST">
      @method('PUT')
      @include('dashboard.pegawai.form')
    </form>

  </x-form-layout>
@endsection
