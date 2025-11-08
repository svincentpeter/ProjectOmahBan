{{-- Quick view untuk expand row DataTable (ringkas) --}}
@php
    /** @var \Modules\Sale\Entities\Sale $sale */
    // Kompatibel: gunakan saleDetails jika ada, fallback ke details
    $items = $sale->saleDetails ?? ($sale->details ?? collect());
@endphp

<div class="p-3"
    style="background-color:#ffffff; border-left:4px solid #4834DF; box-shadow: inset 0 2px 8px rgba(0,0,0,0.05);">
    <div class="table-responsive">
        <table class="table table-sm table-striped mb-0">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                    <th class="text-center">Manual</th>
                    <th class="text-center">Edit</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $d)
                    @php
                        $name = $d->product_name ?? ($d->item_name ?? '—');
                        $qty = (int) ($d->quantity ?? 0);
                        $unit = (int) ($d->price ?? 0);
                        $sub = (int) ($d->sub_total ?? $qty * $unit);

                        $isManual = ($d->source_type ?? null) === 'manual';
                        $manualKind = $d->manual_kind ?? null; // 'goods' | 'service' | null

                        $isAdjusted = (bool) ($d->is_price_adjusted ?? false);
                        $adjAmt = (int) ($d->price_adjustment_amount ?? 0);
                        $note = trim((string) ($d->price_adjustment_note ?? ''));
                    @endphp
                    <tr>
                        <td>{{ $name }}</td>
                        <td class="text-center">{{ $qty }}</td>
                        <td class="text-right">{{ format_currency($unit) }}</td>
                        <td class="text-right">{{ format_currency($sub) }}</td>

                        {{-- Kolom Manual (badge sederhana + title browser default) --}}
                        <td class="text-center">
                            @if ($isManual)
                                <span class="badge badge-warning"
                                    title="Manual: {{ $manualKind === 'service' ? 'Jasa' : ($manualKind === 'goods' ? 'Barang' : '-') }}">
                                    Ya
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Kolom Edit (indikator perubahan harga) --}}
                        <td class="text-center">
                            @if ($isAdjusted)
                                @if ($adjAmt > 0)
                                    <span class="badge badge-danger" title="{{ $note }}">
                                        -{{ format_currency($adjAmt) }}
                                    </span>
                                @elseif($adjAmt < 0)
                                    <span class="badge badge-success" title="{{ $note }}">
                                        +{{ format_currency(abs($adjAmt)) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            <i class="bi bi-inbox"></i> Tidak ada item untuk invoice ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
