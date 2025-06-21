{{-- resources/views/dashboard/pegawai-jadwal/index.blade.php --}}
@extends('layouts.app')

@section('content')

<div class="container py-5">
    {{-- Header: Title, Export, Create --}}
<div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white" style="min-height: 50vh">
  {{-- session alerts --}}
@if(session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@elseif(session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@elseif(session('info'))
  <div class="alert alert-info">{{ session('info') }}</div>
@endif

  <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Jadwal Pegawai</h1>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('pegawai-jadwal.create') }}" class="btn btn-success">Tambah Jadwal</a>
        @endif
    </div>
    
    <table class="table table-striped">
<thead>
    <tr>
        <th>Pegawai</th>
        <th>Periode</th>
        <th>Shift</th>
        <th>Tanggal</th>
        @if(auth()->user()->role === 'admin')
            <th>Aksi</th>
        @endif
    </tr>
</thead>
<tbody>
    @foreach($items as $item)
    <tr>
        <td>{{ $item['nama_lengkap'] }}</td>
        <td>{{ $item['periode'] }}</td>
        <td>{{ $item['shift'] }}</td>
        {{-- <td>{{ $item['tanggal'] }}</td> --}}
        @if(auth()->user()->role === 'admin')
<td>
  <div class="dropdown">
    <button
      class="btn btn-theme info dropdown-toggle"type="button"id="actionsDropdown{{ $loop->index }}"data-bs-toggle="dropdown"aria-expanded="false">
        Actions
    </button>
    <ul class="dropdown-menu custom" aria-labelledby="actionsDropdown{{ $loop->index }}">
      <li>
        <a
          class="dropdown-item"
          href="{{ route('pegawai-jadwal.show', [$item['users_id'], $item['jadwal_shift_id']]) }}">
          Lihat
        </a>
      </li>
      <li>
        <a
          class="dropdown-item"
          href="{{ route('pegawai-jadwal.edit', [$item['users_id'], $item['jadwal_shift_id']]) }}">
          Edit
        </a>
      </li>
      <li>
        <form
          action="{{ route('pegawai-jadwal.destroy', [$item['users_id'], $item['jadwal_shift_id']]) }}"
          method="POST"
          onsubmit="return confirm('Yakin ingin menghapus?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="dropdown-item delete">Hapus</button>
        </form>
      </li>
    </ul>
  </div>
</td>

        @endif
    </tr>
    @endforeach
</tbody>

    </table>
</div>
</div>
@endsection