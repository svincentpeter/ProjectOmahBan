<div class="btn-group dropleft">
    <button type="button" class="btn btn-ghost-primary dropdown rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>

    <div class="dropdown-menu">
        {{-- Lihat Detail --}}
        @can('show_suppliers')
            <a href="{{ route('suppliers.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i>
                Detail Supplier
            </a>
        @endcan

        {{-- Edit Supplier --}}
        @can('edit_suppliers')
            <div class="dropdown-divider"></div>
            <a href="{{ route('suppliers.edit', $data->id) }}" class="dropdown-item">
                <i class="bi bi-pencil-square mr-2 text-primary" style="line-height: 1;"></i>
                Edit Supplier
            </a>
        @endcan

        {{-- Buat Pembelian Baru dari Supplier ini --}}
        @can('create_purchases')
            <div class="dropdown-divider"></div>
            <a href="{{ route('purchases.create', ['supplier_id' => $data->id]) }}" class="dropdown-item">
                <i class="bi bi-cart-plus mr-2 text-success" style="line-height: 1;"></i>
                Buat Pembelian
            </a>
        @endcan

        {{-- Hapus Supplier --}}
        @can('delete_suppliers')
            <div class="dropdown-divider"></div>
            <button type="button" class="dropdown-item text-danger delete-supplier" data-id="{{ $data->id }}"
                data-name="{{ $data->supplier_name }}"
                data-has-purchases="{{ $data->purchases_count > 0 ? 'true' : 'false' }}">
                <i class="bi bi-trash mr-2" style="line-height: 1;"></i>
                Hapus Supplier
            </button>
        @endcan
    </div>
</div>
