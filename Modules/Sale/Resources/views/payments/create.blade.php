@extends('layouts.app')

@section('title', 'Tambah Pembayaran — INV '.$sale->reference)

{{-- breadcrumb mengikuti style Edit Sale --}}
@section('breadcrumb')
    <ol class="breadcrumb border-0 m-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
        <li class="breadcrumb-item active">Tambah Pembayaran</li>
    </ol>
@endsection

@section('content')
<div class="container-fluid mb-4">

    {{-- ========== Ringkasan atas (kartu-kartu) ========== --}}
    <div class="row">
        <div class="col-md-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Total</small>
                    <h5 class="mb-0">{{ format_currency((int) $sale->total_amount) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Dibayar</small>
                    <h5 class="mb-0">{{ format_currency((int) $sale->paid_amount) }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <small class="text-muted">Kurang</small>
                    @php $due = (int) $sale->due_amount; @endphp
                    <h5 class="mb-0 {{ $due <= 0 ? 'text-success' : 'text-danger' }}">
                        {{ format_currency($due) }}
                    </h5>
                </div>
            </div>
        </div>
        <div class="col-md-3 mt-3 mt-md-0">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <small class="text-muted d-block">Status</small>
                    @php $ps = (string) $sale->payment_status; @endphp
                    <span class="badge
                        {{ $ps==='Paid' ? 'badge-success' : ($ps==='Partial' ? 'badge-warning' : 'badge-secondary') }}">
                        {{ $ps }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    {{-- ========== /Ringkasan =========='--}}

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    @include('utils.alerts')

                    <form id="payment-form" action="{{ route('sale-payments.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                        <div class="form-row">
                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="mb-1">Tanggal <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control"
                                           value="{{ old('date', now()->toDateString()) }}" required>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group" x-data="{ pm: '{{ old('payment_method','Tunai') }}' }">
                                    <label class="mb-1">Metode <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="payment_method" class="form-control" x-model="pm" required>
                                        <option value="Tunai">Tunai</option>
                                        <option value="Transfer">Transfer</option>
                                        <option value="QRIS">QRIS</option>
                                    </select>

                                    {{-- Bank / Rekening (muncul saat Transfer/QRIS) --}}
                                    <div class="mt-2" x-show="pm==='Transfer' || pm==='QRIS'">
                                        <label class="mb-1">Bank / Rekening <span class="text-danger">*</span></label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control"
                                               :required="pm==='Transfer' || pm==='QRIS'"
                                               value="{{ old('bank_name') }}" placeholder="BCA a.n. ...">
                                        <small class="text-muted">Wajib untuk Transfer/QRIS (dicatat di pembayaran).</small>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <div class="form-group">
                                    <label class="mb-1">Jumlah <span class="text-danger">*</span></label>
                                    <input type="text" name="amount" id="amount"
                                           class="form-control js-money"
                                           value="{{ old('amount', $due) }}" placeholder="Rp0" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-1">Catatan (opsional)</label>
                            <input type="text" name="note" class="form-control"
                                   placeholder="mis: pelunasan, dp, dsb." value="{{ old('note') }}">
                        </div>

                        <div class="mt-2 d-flex">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="bi bi-check"></i> Simpan Pembayaran
                            </button>

                            <button type="button" id="btn-fill-due"
                                    class="btn btn-outline-primary mr-2"
                                    data-due="{{ $due }}" {{ $due<=0 ? 'disabled' : '' }}>
                                Lunasi Sisa ({{ format_currency($due) }})
                            </button>

                            <a href="{{ url()->previous() ?: route('sales.index') }}" class="btn btn-light">Cancel</a>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Angka di-<em>clamp</em> di server (anti overpay). Status bayar diperbarui otomatis.
                        </small>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page_scripts')
{{-- AutoNumeric: sama seperti di Edit Sale --}}
<script src="https://cdn.jsdelivr.net/npm/autonumeric@4"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const amount = document.getElementById('amount');
    const btnDue = document.getElementById('btn-fill-due');

    // Inisialisasi AutoNumeric (format uang) — sama dengan halaman Edit Sale
    let an = null;
    if (window.AutoNumeric && amount) {
        an = new AutoNumeric(amount, {
            decimalPlaces: 0,
            digitGroupSeparator: '.',
            decimalCharacter: ',',
            modifyValueOnWheel: false,
            emptyInputBehavior: 'zero'
        });
        // Set nilai awal = due
        const initDue = parseInt(btnDue?.dataset?.due || '0', 10) || 0;
        an.set(initDue);
    }

    // Tombol "Lunasi Sisa" → isi input jumlah dengan due & fokus
    btnDue?.addEventListener('click', function () {
        const due = parseInt(this.dataset.due || '0', 10) || 0;
        if (an) an.set(due);
        else amount.value = (due || 0).toLocaleString('id-ID');
        amount.focus();
    });

    // Unmask sebelum submit (supaya server dapat integer murni)
    document.getElementById('payment-form')?.addEventListener('submit', function () {
        if (an) amount.value = an.getNumber(); // kirim angka mentah
    });
});
</script>
@endpush
