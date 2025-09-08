@php
    $isDefault = function_exists('settings') && settings() && settings()->default_currency_id == $data->id;
@endphp

@can('edit_currencies')
    <a href="{{ route('currencies.edit', $data->id) }}" class="btn btn-info btn-sm" title="Ubah">
        <i class="bi bi-pencil"></i>
    </a>
@endcan

@can('delete_currencies')
    @if($isDefault)
        <button class="btn btn-danger btn-sm" disabled title="Tidak dapat menghapus mata uang default">
            <i class="bi bi-trash"></i>
        </button>
    @else
        <button id="delete" class="btn btn-danger btn-sm" title="Hapus"
            onclick="
                event.preventDefault();
                if (confirm('Hapus mata uang ini? Tindakan tidak dapat dibatalkan.')) {
                    document.getElementById('destroy{{ $data->id }}').submit()
                }
            ">
            <i class="bi bi-trash"></i>
        </button>
        <form id="destroy{{ $data->id }}" class="d-none" action="{{ route('currencies.destroy', $data->id) }}" method="POST">
            @csrf
            @method('delete')
        </form>
    @endif
@endcan
