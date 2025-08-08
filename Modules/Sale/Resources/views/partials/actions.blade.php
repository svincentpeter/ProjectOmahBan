<div class="btn-group dropleft">
    <button type="button" class="btn btn-ghost-primary dropdown rounded" data-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-three-dots-vertical"></i>
    </button>
    <div class="dropdown-menu">
        <a target="_blank" href="{{ route('sales.pos.pdf', $data->id) }}" class="dropdown-item">
            <i class="bi bi-file-earmark-pdf mr-2 text-success" style="line-height: 1;"></i> POS Invoice
        </a>
        @can('access_sale_payments')
            <a href="{{ route('sale-payments.index', $data->id) }}" class="dropdown-item">
                <i class="bi bi-cash-coin mr-2 text-warning" style="line-height: 1;"></i> Show Payments
            </a>
        @endcan
        @can('access_sale_payments')
            @if($data->due_amount > 0)
            <a href="{{ route('sale-payments.create', $data->id) }}" class="dropdown-item">
                <i class="bi bi-plus-circle-dotted mr-2 text-success" style="line-height: 1;"></i> Add Payment
            </a>
            @endif
        @endcan
        @can('show_sales')
            <a href="{{ route('sales.show', $data->id) }}" class="dropdown-item">
                <i class="bi bi-eye mr-2 text-info" style="line-height: 1;"></i> Details
            </a>
        @endcan
    
    </div>
</div>
