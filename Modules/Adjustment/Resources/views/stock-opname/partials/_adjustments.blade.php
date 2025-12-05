{{--
    Adjustments Partial
    
    Variables:
    - $items (Collection of StockOpnameItem yang punya adjustment_id)
--}}

@if($items->count() > 0)
    <div class="alert alert-info mb-4">
        <i class="bi bi-info-circle"></i>
        <strong>{{ $items->count() }} Adjustment</strong> telah dibuat untuk mengoreksi selisih stok.
        Status adjustment dapat dilihat di halaman <a href="{{ route('adjustments.index') }}">Adjustment Management</a>.
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Produk</th>
                    <th class="text-center">Variance</th>
                    <th class="text-center">Type</th>
                    <th>Adjustment Reference</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                    @if($item->adjustment)
                        <tr>
                            <td>
                                <strong>{{ $item->product->product_code }}</strong><br>
                                <small class="text-muted">{{ $item->product->product_name }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-{{ $item->variance_type === 'surplus' ? 'info' : 'danger' }}">
                                    {{ $item->variance_qty > 0 ? '+' : '' }}{{ $item->variance_qty }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($item->adjustment->type === 'addition')
                                    <span class="badge badge-success">
                                        <i class="bi bi-plus-circle"></i> Addition
                                    </span>
                                @else
                                    <span class="badge badge-danger">
                                        <i class="bi bi-dash-circle"></i> Subtraction
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('adjustments.show', $item->adjustment_id) }}" class="font-weight-bold">
                                    {{ $item->adjustment->reference }}
                                </a>
                            </td>
                            <td class="text-center">
                                {!! $item->adjustment->status_badge !!}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('adjustments.show', $item->adjustment_id) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i>
        Tidak ada selisih yang memerlukan adjustment. Semua stok sudah cocok!
    </div>
@endif
