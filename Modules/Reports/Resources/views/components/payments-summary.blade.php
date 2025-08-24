<table class="table table-sm table-borderless mb-0">
  <tbody>
  @forelse ($receipts as $rc)
    @php
      $methodColors = ['Tunai'=>'secondary','Cash'=>'secondary','Transfer'=>'info','QRIS'=>'success','Debit'=>'primary','Kredit'=>'warning'];
      $m = $rc->payment_method;
      $color = $methodColors[$m] ?? 'secondary';
    @endphp
    <tr>
      <td style="width:55%">
        <span class="badge bg-{{ $color }}">{{ $m }}</span>
        @if (($rc->bank_name ?? '—') !== '—')
          <span class="ms-1 badge bg-light text-dark">{{ $rc->bank_name }}</span>
        @endif
      </td>
      <td class="text-end fw-semibold">{{ format_currency($rc->total) }}</td>
    </tr>
  @empty
    <tr><td colspan="2" class="text-muted">Tidak ada data.</td></tr>
  @endforelse
  </tbody>
</table>
