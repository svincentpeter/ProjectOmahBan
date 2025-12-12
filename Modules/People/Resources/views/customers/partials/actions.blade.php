<div class="flex items-center justify-center gap-2">
    {{-- Lihat Detail --}}
    @can('show_customers')
        <a href="{{ route('customers.show', $data->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors" title="Detail Customer">
            <i class="bi bi-eye"></i>
        </a>
    @endcan

    {{-- Edit Customer --}}
    @can('edit_customers')
        <a href="{{ route('customers.edit', $data->id) }}" class="inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-100 rounded-lg hover:bg-amber-200 transition-colors" title="Edit Customer">
            <i class="bi bi-pencil"></i>
        </a>
    @endcan

    {{-- Buat Penjualan via POS --}}
    @can('access_sales')
        <a href="{{ route('app.pos.index') }}?customer_id={{ $data->id }}" class="inline-flex items-center justify-center w-8 h-8 text-emerald-600 bg-emerald-100 rounded-lg hover:bg-emerald-200 transition-colors" title="Buat Penjualan">
            <i class="bi bi-cart-plus"></i>
        </a>
    @endcan

    {{-- Hapus Customer --}}
    @can('delete_customers')
        <button type="button" 
                class="inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 rounded-lg hover:bg-red-200 transition-colors delete-customer" 
                data-id="{{ $data->id }}"
                data-name="{{ $data->customer_name }}"
                data-has-sales="{{ $data->sales_count > 0 ? 'true' : 'false' }}"
                title="Hapus Customer">
            <i class="bi bi-trash"></i>
        </button>
    @endcan
</div>
