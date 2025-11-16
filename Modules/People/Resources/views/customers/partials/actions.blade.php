<div class="btn-group dropleft">
    <button type="button" class="btn btn-ghost-primary dropdown rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>

    <div class="dropdown-menu">
        {{-- Lihat Detail --}}
        @can('show_customers')
            <a href="{{ route('customers.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i>
                Detail Customer
            </a>
        @endcan

        {{-- Edit Customer --}}
        @can('edit_customers')
            <div class="dropdown-divider"></div>
            <a href="{{ route('customers.edit', $data->id) }}" class="dropdown-item">
                <i class="bi bi-pencil-square mr-2 text-primary" style="line-height: 1;"></i>
                Edit Customer
            </a>
        @endcan

        {{-- Buat Penjualan via POS --}}
        @can('access_sales')
            <div class="dropdown-divider"></div>
            <a href="{{ route('app.pos.index') }}?customer_id={{ $data->id }}" class="dropdown-item">
                <i class="bi bi-cart-plus mr-2 text-success" style="line-height: 1;"></i>
                Buat Penjualan
            </a>
        @endcan

        {{-- Hapus Customer --}}
        @can('delete_customers')
            <div class="dropdown-divider"></div>
            <button type="button" 
                    class="dropdown-item text-danger delete-customer" 
                    data-id="{{ $data->id }}"
                    data-name="{{ $data->customer_name }}"
                    data-has-sales="{{ $data->sales_count > 0 ? 'true' : 'false' }}">
                <i class="bi bi-trash mr-2" style="line-height: 1;"></i>
                Hapus Customer
            </button>
        @endcan
    </div>
</div>
