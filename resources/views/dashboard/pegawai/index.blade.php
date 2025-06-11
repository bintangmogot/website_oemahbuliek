@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Daftar Pegawai</h1>

    @if(auth()->user()->role === 'admin')
        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-primary mb-3">Tambah Pegawai</a>
    @endif

    <table class="table">
      <thead>
        <tr>
          <th>Email</th>
          <th>Nama</th>
          <th>Jabatan</th>
          <th>Tanggal Masuk</th>
          <th>No HP</th>
          @if(auth()->user()->role === 'admin')
            <th>Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach ($pegawai as $p)
          <tr>
            <td>{{ $p->id_akun }}</td>
            <td>{{ $p->nama_lengkap }}</td>
            <td>{{ $p->jabatan }}</td>
            <td>{{ $p->tgl_masuk->format('Y-m-d') }}</td>
            <td>{{ $p->no_hp }}</td>
            @if(auth()->user()->role === 'admin')
              <td>
                <div class="dropdown">
                  <button class="btn btn-theme info dropdown-toggle" 
                          type="button" 
                          id="actionsDropdown{{ $p->id }}" 
                          data-bs-toggle="dropdown" 
                          aria-expanded="false">
                    Actions
                  </button>
                  <ul class="dropdown-menu custom" aria-labelledby="actionsDropdown{{ $p->id }}">
                    <li>
                      <a class="dropdown-item" 
                         href="{{ route('admin.pegawai.show', $p->id) }}">
                        Lihat
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" 
                         href="{{ route('admin.pegawai.edit', $p->id) }}">
                        Edit
                      </a>
                    </li>
                    <li>
                      <form action="{{ route('admin.pegawai.destroy', $p->id) }}" 
                            method="POST" 
                            onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="dropdown-item delete">
                          Hapus
                        </button>
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

    {{ $pegawai->links() }}
</div>
@endsection
