@extends('layouts.app')

@section('content')
  <h2>Edit Data Pegawai</h2>
  
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
    @include('dashboard.pegawai.form')
  </form>
@endsection
