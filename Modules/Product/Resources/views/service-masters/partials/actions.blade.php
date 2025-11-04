{{-- actions.blade.php (FIXED) --}}
<div class="action-buttons d-inline-flex align-items-center">
        {{-- EDIT --}}
        <button type="button" class="btn btn-warning btn-icon btn-edit" data-id="{{ $d->id }}"
            data-name="{{ $d->service_name }}" data-price="{{ $d->standard_price }}" data-category="{{ $d->category }}"
            data-description="{{ $d->description }}" title="Edit Jasa">
            <i class="cil-pencil"></i>
        </button>

        {{-- HISTORY --}}
        <a href="{{ route('service-masters.audit-log', $d->id) }}" class="btn btn-info btn-icon" data-toggle="tooltip"
            title="Lihat History">
            <i class="cil-history"></i>
        </a>

        {{-- TOGGLE STATUS --}}
        <form action="{{ route('service-masters.toggle-status', $d->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-icon {{ $d->status ? 'btn-success' : 'btn-secondary' }}"
                data-toggle="tooltip" title="{{ $d->status ? 'Status: Aktif' : 'Status: Nonaktif' }}">
                <i class="cil-{{ $d->status ? 'check-circle' : 'x-circle' }}"></i>
            </button>
        </form>

        {{-- DELETE --}}
        <button type="button" class="btn btn-danger btn-icon btn-delete" data-id="{{ $d->id }}"
            data-name="{{ $d->service_name }}" title="Hapus Jasa">
            <i class="cil-trash"></i>
        </button>
    </div>