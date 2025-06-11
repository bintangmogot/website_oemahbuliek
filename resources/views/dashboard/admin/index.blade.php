@extends('layouts.app')
@section('content')
<div class="container">
  <h1>Daftar User</h1>
  <a href="{{ route('admin.user.create') }}" class="btn btn-primary mb-3">Tambah User</a>
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <table class="table">
    <thead>
      <tr><th>Email</th><th>Role</th><th>Aksi</th></tr>
    </thead>
    <tbody>
      @foreach($users as $u)
      <tr>
        <td>{{ $u->email }}</td>
        <td>{{ ucfirst($u->role) }}</td>
        <td>
          <form action="{{ route('admin.user.destroy', $u->email) }}" method="POST" class="d-inline">
            @csrf @method('DELETE')
            <button class="btn btn-danger btn-sm"
                    onclick="return confirm('Yakin hapus user ini?')">Hapus</button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  {{ $users->links() }}
</div>
@endsection
