@php
    // Ambil baris pembayaran dari berbagai kemungkinan variabel (array/objek)
    $row = $payment ?? $data ?? $row ?? $item ?? $model ?? null;

    // Normalisasi ambil ID (mendukung object & array)
    $pid = is_array($row) ? ($row['id'] ?? null)      : ($row->id ?? null);
    $sid = is_array($row)
        ? ($row['sale_id'] ?? ($row['sale']['id'] ?? null))
        : ($row->sale->id ?? $row->sale_id ?? null);
@endphp

@if ($row && $pid)
<div class="btn-group btn-group-sm" role="group">
    @can('access_sale_payments')
        {{-- Edit (tetap lewat route helper, aman karena paramnya lengkap) --}}
        <a href="{{ route('sale-payments.edit', [$sid, $pid]) }}"
           class="btn btn-outline-primary"
           title="Edit Pembayaran">
            <i class="bi bi-pencil-square"></i>
        </a>

        {{-- Hapus (AJAX; URL diputuskan di JS agar tidak bentrok signature route) --}}
        <button type="button"
                class="btn btn-outline-danger js-del-payment"
                title="Hapus Pembayaran"
                data-payment-id="{{ $pid }}"
                @if($sid) data-sale-id="{{ $sid }}" @endif>
            <i class="bi bi-trash"></i>
        </button>
    @endcan
</div>
@endif

@once
@push('scripts')
<script>
document.addEventListener('click', async (ev) => {
    const btn = ev.target.closest('.js-del-payment');
    if (!btn) return;

    ev.preventDefault();
    if (!confirm('Hapus pembayaran ini?')) return;

    const pid   = btn.dataset.paymentId;
    const sid   = btn.dataset.saleId || '';
    const token = document.querySelector('meta[name="csrf-token"]').content;

    // Susun kandidat URL: nested dulu, lalu flat (fallback)
    const candidates = [];
    if (sid) candidates.push(`/sales/${sid}/payments/${pid}`);
    candidates.push(`/sale-payments/destroy/${pid}`);

    let success = false;
    for (const url of candidates) {
        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                },
                body: new URLSearchParams({ _method: 'DELETE' })
            });

            // Jika controller balas JSON {ok:true} → aman.
            // Jika redirect (mode klasik) → res.ok juga true.
            if (res.ok) { success = true; break; }
        } catch (_) {}
    }

    if (success) {
        location.reload();
    } else {
        alert('Gagal menghapus pembayaran. Coba lagi.');
    }
});
</script>
@endpush
@endonce
