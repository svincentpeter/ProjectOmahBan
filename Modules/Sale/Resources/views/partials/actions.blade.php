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

        @can('access_sale_payments')<a href="{{ route('sale-payments.index', $data->id) }}" class="dropdown-item"><i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i>
                Lihat Pembayaran
            </a>
        @endcan

        @can('access_sale_payments')
            @if((int) $data->due_amount > 0)
                <a href="{{ route('sale-payments.create', $data->id) }}" class="dropdown-item">
                    <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i>
                    Tambah Pembayaran
                </a>
            @endif
        @endcan

        @can('show_sales')
            <a href="{{ route('sales.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i>
                Detail Penjualan
            </a>
        @endcan
    </div>
</div>
