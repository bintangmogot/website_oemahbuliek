{{-- resources/views/components/index-table.blade.php --}}
@props([
    'title' => '',
    'columns' => [],
    'items' => [],
    'showActions' => false,
    'routes' => [],
    'routeKey' => 'id',
    'createRoute' => null,
    'createLabel' => 'Tambah Data',
    'exportRoute' => null,
    'exportLabel' => 'Export',
    'showFilter' => false,
])

<x-session-status/>

<div class="container py-5">
    {{-- Header: Title, Export, Create --}}
<div class="card rounded-4 px-3 py-4 p-sm-3 p-md-4 p-lg-5 bg-white">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">{{ $title ?? 'Daftar Data' }}</h3>
    <div class="d-flex">
      <!-- Filter Button -->
      <button class="btn btn-outline-secondary me-2">
        <i class="bi bi-funnel"></i>
      </button>
      <!-- Export Button -->
      <a href="{{ $exportUrl ?? '#' }}" class="btn btn-success me-2">
        {{ $exportLabel ?? 'Export' }}
      </a>
      <!-- Create Button -->
      <a href="{{ $createUrl ?? route('admin.user.create') }}" class="btn btn-primary">
        {{ $createLabel ?? 'Tambah Baru' }}
      </a>
        </div>
    </div>

    {{-- Card wrapper with border radius and shadow --}}
    <div class="card rounded-2xl border-0 shadow-sm rounded-3">
        {{-- Optional filter section --}}
        <div class="card-body p-0 table-responsive rounded-3">
            {{-- Optional filter section --}}
            {{-- Table with zebra stripes, borderless --}}
            <table class="table table-striped table-borderless mb-0 rounded-3">
                {{-- Table header --}}
                <thead style="background-color:#FFD9D9">
                    <tr>
                        @foreach($columns as $column)
                            <th>{{ $column['label'] }}</th>
                        @endforeach
                        @if($showActions)
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr class="bg-white">
                            @foreach($columns as $column)
                                @php
                                    $field = $column['field'];
                                    $value = data_get($item, $field);
                                    if ($value instanceof \Carbon\Carbon) {
                                        $value = $value->format('Y-m-d');
                                    }
                                @endphp
                                <td class="align-middle">{{ $value }}</td>
                            @endforeach

                            @if($showActions)
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-theme info dropdown-toggle" type="button" id="actionsDropdown{{ $loop->index }}" data-bs-toggle="dropdown" aria-expanded="false">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu custom" aria-labelledby="actionsDropdown{{ $loop->index }}">
                                            @if(isset($routes['show']))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route($routes['show'], [$routeKey => $item->id]) }}">Lihat</a>
                                                </li>
                                            @endif
                                            @if(isset($routes['edit']))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route($routes['edit'], [$routeKey => $item->id]) }}">Edit</a>
                                                </li>
                                            @endif
                                            @if(isset($routes['destroy']))
                                                <li>
                                                    <form action="{{ route($routes['destroy'], [$routeKey => $item->id]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item delete">Hapus</button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columns) + ($showActions ? 1 : 0) }}" class="text-center py-4">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if(method_exists($items, 'links'))
        <div class="d-flex justify-content-end mt-3">
            {{ $items->links() }}
        </div>
    @endif
</div>
