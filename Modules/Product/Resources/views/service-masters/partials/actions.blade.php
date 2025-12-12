{{-- actions.blade.php (Flowbite/Tailwind) --}}
<div class="flex items-center justify-center gap-1.5">
    {{-- EDIT --}}
    <button type="button" 
            class="btn-edit p-2 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
            data-id="{{ $d->id }}"
            data-name="{{ $d->service_name }}" 
            data-price="{{ $d->standard_price }}" 
            data-category="{{ $d->category }}"
            data-description="{{ $d->description }}" 
            title="Edit Jasa">
        <i class="bi bi-pencil"></i>
    </button>

    {{-- HISTORY --}}
    <a href="{{ route('service-masters.audit-log', $d->id) }}" 
       class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
       title="Lihat History">
        <i class="bi bi-clock-history"></i>
    </a>

    {{-- TOGGLE STATUS --}}
    <form action="{{ route('service-masters.toggle-status', $d->id) }}" method="POST" class="inline">
        @csrf
        <button type="submit" 
                class="p-2 rounded-lg transition-colors {{ $d->status ? 'text-emerald-600 hover:bg-emerald-50' : 'text-zinc-400 hover:bg-zinc-100' }}"
                title="{{ $d->status ? 'Status: Aktif' : 'Status: Nonaktif' }}">
            <i class="bi {{ $d->status ? 'bi-check-circle-fill' : 'bi-x-circle' }}"></i>
        </button>
    </form>

    {{-- DELETE --}}
    <button type="button" 
            class="btn-delete p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
            data-id="{{ $d->id }}"
            data-name="{{ $d->service_name }}"
            data-price="{{ $d->standard_price }}"
            data-category="{{ $d->category }}"
            title="Hapus Jasa">
        <i class="bi bi-trash"></i>
    </button>
</div>