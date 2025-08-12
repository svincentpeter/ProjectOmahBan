<div class="dt-child p-3">
  <div class="table-responsive">
    <table class="table table-sm table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:2rem">#</th>
          <th>Item</th>
          <th class="text-end">Qty</th>
          <th class="text-end">Harga</th>
          <th class="text-end">Sub Total</th>
          <th class="text-end">HPP</th>
          <th class="text-end">Profit</th>
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
            $qty     = (int)($d->quantity ?? 0);
            $unit    = (int)($d->unit_price ?? $d->price ?? 0);
            $sub     = (int)($d->sub_total ?? ($qty * $unit));
            $hppCalc = (float)($d->hpp ?? 0) * $qty;

            $hppInt    = (int) round($hppCalc);
            $profitInt = (int) ($sub - $hppInt);

            // Akumulasi total untuk footer
            $sumSub += $sub;
            $sumHpp += $hppInt;

            $name = $d->item_name ?: ($d->product_name ?: '-');
          @endphp
          <tr>
            <td>{{ $no++ }}</td>
            <td>{{ $name }}</td>
            <td class="text-end">{{ $qty }}</td>
            <td class="text-end">{{ format_currency($unit) }}</td>
            <td class="text-end">{{ format_currency($sub) }}</td>
            <td class="text-end">{{ format_currency($hppInt) }}</td>
            <td class="text-end">{{ format_currency($profitInt) }}</td>
          </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center text-muted"><em>Tidak ada item detail untuk invoice ini.</em></td>
            </tr>
        @endforelse
      </tbody>
      {{-- Tampilkan footer hanya jika ada detail item --}}
      @if($sale->saleDetails->isNotEmpty())
        <tfoot>
            <tr class="table-light">
              <th colspan="4" class="text-end">Total</th>
              <th class="text-end">{{ format_currency($sumSub) }}</th>
              <th class="text-end">{{ format_currency($sumHpp) }}</th>
              <th class="text-end">{{ format_currency((int)($sumSub - $sumHpp)) }}</th>
            </tr>
        </tfoot>
      @endif
    </table>
  </div>
</div>