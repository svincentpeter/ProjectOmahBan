<div class="btn-group dropleft">
    <button type="button" class="btn btn-ghost-primary dropdown rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>

    <div class="dropdown-menu">
        {{-- Cetak struk POS kecil (wkhtmltopdf / halaman thermal) --}}
        <a target="_blank" href="{{ route('sales.pos.pdf', $data->id) }}" class="dropdown-item">
            <i class="bi bi-file-earmark-pdf mr-2 text-success" style="line-height: 1;"></i>
            Cetak Struk POS
        </a>

        @can('access_sale_payments')
            @if (Route::has('sale-payments.index'))
                <a href="{{ route('sale-payments.index', $data->id) }}" class="dropdown-item">
                    <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i>
                    Lihat Pembayaran
                </a>
            @endif
        @endcan

        {{-- Tambah Pembayaran (prioritas utama) --}}
@if ((int) $data->due_amount > 0 && Route::has('sale-payments.create'))
    <a href="{{ route('sale-payments.create', $data->id) }}" class="dropdown-item">
        <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i>
        Tambah Pembayaran
    </a>
@endif


        @can('show_sales')
            <a href="{{ route('sales.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i>
                Detail Penjualan
            </a>
        @endcan

        {{-- ====== Tambahan: Edit selalu tersedia untuk Admin (termasuk pasca-lunas) ====== --}}
        @can('edit_sales')
            <div class="dropdown-divider"></div>
            <a href="{{ route('sales.edit', $data->id) }}" class="dropdown-item">
                <i class="bi bi-pencil-square mr-2 text-primary" style="line-height: 1;"></i>
                Edit Penjualan
                @if($data->payment_status === 'Paid' || $data->status === 'Completed')
                    <small class="text-muted ml-1">(Pasca Lunas)</small>
                @endif
            </a>
        @endcan
    </div>
</div>
