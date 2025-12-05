{{--
    Items Table Partial
    
    Variables:
    - $items (Collection of StockOpnameItem)
--}}

<div class="table-responsive">
    <table class="table table-hover table-bordered" id="items-table">
        <thead class="thead-light">
            <tr>
                <th width="5%">No</th>
                <th width="15%">Kode Produk</th>
                <th>Nama Produk</th>
                <th width="12%">Kategori</th>
                <th width="10%" class="text-center">Stok Sistem</th>
                <th width="10%" class="text-center">Hasil Hitung</th>
                <th width="10%" class="text-center">Variance</th>
                <th width="8%" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $index => $item)
                <tr class="{{ $item->variance_type === 'shortage' ? 'table-danger' : ($item->variance_type === 'surplus' ? 'table-info' : '') }}">
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->product_code }}</strong>
                    </td>
                    <td>
                        {{ $item->product->product_name }}
                        @if($item->variance_reason)
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-info-circle"></i> {{ $item->variance_reason }}
                            </small>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-secondary">
                            {{ $item->product->category->category_name }}
                        </span>
                    </td>
                    <td class="text-center">
                        <strong>{{ number_format($item->system_qty) }}</strong>
                    </td>
                    <td class="text-center">
                        @if($item->actual_qty !== null)
                            <strong class="text-success">{{ number_format($item->actual_qty) }}</strong>
                            @if($item->counted_at)
                                <br>
                                <small class="text-muted">{{ $item->counted_at->format('d/m H:i') }}</small>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->variance_type === 'match')
                            <span class="badge badge-success">
                                <i class="bi bi-check-circle"></i> Cocok
                            </span>
                        @elseif($item->variance_type === 'surplus')
                            <span class="badge badge-info">
                                <i class="bi bi-arrow-up"></i> +{{ $item->variance_qty }}
                            </span>
                        @elseif($item->variance_type === 'shortage')
                            <span class="badge badge-danger">
                                <i class="bi bi-arrow-down"></i> {{ $item->variance_qty }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($item->adjustment_id)
                            <a href="{{ route('adjustments.show', $item->adjustment_id) }}" 
                               class="badge badge-primary"
                               data-toggle="tooltip"
                               title="Lihat Adjustment">
                                <i class="bi bi-link-45deg"></i> Adj
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <i class="bi bi-inbox"></i> Tidak ada data
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($items->count() > 0)
            <tfoot class="thead-light">
                <tr>
                    <th colspan="4" class="text-right">Total:</th>
                    <th class="text-center">{{ number_format($items->sum('system_qty')) }}</th>
                    <th class="text-center">{{ number_format($items->sum('actual_qty')) }}</th>
                    <th class="text-center">
                        @php
                            $totalVariance = $items->sum('variance_qty');
                        @endphp
                        <span class="badge badge-{{ $totalVariance > 0 ? 'info' : ($totalVariance < 0 ? 'danger' : 'success') }}">
                            {{ $totalVariance > 0 ? '+' : '' }}{{ $totalVariance }}
                        </span>
                    </th>
                    <th></th>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

@push('page_scripts')
<script>
$(document).ready(function() {
    $('#items-table').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
        },
        pageLength: 25,
        order: [[6, 'desc']], // Sort by variance descending
        columnDefs: [
            { orderable: false, targets: [0, 7] }
        ]
    });

    $('[data-toggle="tooltip"]').tooltip();
});
</script>
@endpush
