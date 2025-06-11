{{-- resources/views/components/index-table-user.blade.php --}}
@props([
  'title',
  'createRoute' => null,
  'createLabel' => 'Tambah Data',
  'columns' => [],
  'items' => [],
  'destroyRoute',
])

<div class="px-3 py-5" style="background-color: #fcecec; min-height: 100vh;">
  <div class="row justify-content-center">
    <div class="col-sm-10">
      <div class="card shadow-sm border-0">
        <div class="card-body p-4">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold">{{ $title }}</h5>
            @if($createRoute)
              <a href="{{ route($createRoute) }}" class="btn btn-primary">
                {{ $createLabel }}
              </a>
            @endif
          </div>

          <div class="table-responsive rounded-3 shadow-sm">
            <table class="table table-striped table-hover table-borderless mb-0 rounded-3">
              <thead style="background-color:#FFD9D9">
                <tr>
                  @foreach($columns as $col)
                    <th>{{ $col['label'] }}</th>
                  @endforeach
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($items as $item)
                  <tr>
                    @foreach($columns as $col)
                      <td>{{ $item[$col['field']] }}</td>
                    @endforeach
                    <td>
                      <form action="{{ route($destroyRoute, $item->email) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini?')">
                          Hapus
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr><td colspan="{{ count($columns) + 1 }}">Data kosong</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <div class="mt-3">
            {{ $items->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
