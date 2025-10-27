{{-- Quick view untuk expand row DataTable --}}
<div class="p-3" style="background-color: #ffffff; border-left: 4px solid #4834DF; box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);">
    <table class="table table-sm table-bordered mb-0">
        <thead class="thead-light">
            <tr>
                <th width="3%">#</th>
                <th width="35%">Item</th>
                <th width="8%" class="text-center">Qty</th>
                <th width="13%" class="text-right">Harga</th>
                <th width="13%" class="text-right">Sub Total</th>
                <th width="13%" class="text-right">HPP</th>
                <th width="15%" class="text-center">Info Diskon</th> {{-- ✅ KOLOM BARU --}}
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
                $sumSub = 0;
                $sumHpp = 0;
            @endphp

            @forelse($sale->saleDetails as $d)
                @php
                    $name = $d->product_name ?? $d->item_name ?? '—';
                    $qty = (int)($d->quantity ?? 0);
                    $unit = (int)($d->price ?? 0);
                    $sub = (int)($d->sub_total ?? 0);

                    // HPP
                    $hppInt = (int)($d->hpp ?? 0);
                    if ($d->source_type === 'manual' && $d->manual_kind === 'goods') {
                        $hppInt = (int)($d->manual_hpp ?? 0);
                    }
                    $hppTotalInt = $hppInt * $qty;

                    // Profit
                    $profitInt = $sub - $hppTotalInt;

                    $sumSub += $sub;
                    $sumHpp += $hppTotalInt;
                @endphp

                <tr>
                    <td class="text-center">{{ $no++ }}</td>
                    <td>{{ $name }}</td>
                    <td class="text-center">{{ $qty }}</td>
                    
                    {{-- ✅ HARGA (DENGAN INDICATOR DISKON) --}}
                    <td class="text-right">
                        @if(!empty($d->is_price_adjusted))
                            <small class="text-muted d-block">
                                <del>{{ format_currency($d->original_price) }}</del>
                            </small>
                            <strong class="text-danger">{{ format_currency($unit) }}</strong>
                        @else
                            {{ format_currency($unit) }}
                        @endif
                    </td>
                    
                    <td class="text-right">{{ format_currency($sub) }}</td>
                    <td class="text-right">{{ format_currency($hppTotalInt) }}</td>
                    
                    {{-- ✅ INFO DISKON --}}
                    <td class="text-center">
                        @if(!empty($d->is_price_adjusted) && !empty($d->price_adjustment_note))
                            <button type="button" 
                                    class="btn btn-sm btn-warning" 
                                    data-toggle="tooltip" 
                                    title="{{ $d->price_adjustment_note }}"
                                    style="font-size: 0.75em;">
                                <i class="bi bi-tag-fill"></i> -{{ format_currency($d->price_adjustment_amount) }}
                            </button>
                        @elseif(!empty($d->is_price_adjusted))
                            <span class="badge badge-warning badge-sm">
                                -{{ format_currency($d->price_adjustment_amount) }}
                            </span>
                        @else
                            <span class="badge badge-secondary badge-sm">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-3">
                        <i class="bi bi-inbox"></i> Tidak ada item detail untuk invoice ini.
                    </td>
                </tr>
            @endforelse
        </tbody>

        {{-- FOOTER --}}
        @if($sale->saleDetails->count() > 0)
            <tfoot class="bg-light font-weight-bold">
                <tr>
                    <td colspan="4" class="text-right">Total:</td>
                    <td class="text-right">{{ format_currency($sumSub) }}</td>
                    <td class="text-right">{{ format_currency($sumHpp) }}</td>
                    <td class="text-center">
                        @php
                            $totalAdjustment = $sale->saleDetails->where('is_price_adjusted', 1)->sum('price_adjustment_amount');
                        @endphp
                        @if($totalAdjustment > 0)
                            <span class="badge badge-warning">-{{ format_currency($totalAdjustment) }}</span>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>

@push('scripts')
<script>
    // Init tooltip untuk button diskon
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
