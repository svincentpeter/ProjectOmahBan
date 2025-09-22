<div wire:key="daily-root">
  {{-- TOOLBAR --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 fw-light">Laporan <span class="fw-bold">Kas Harian</span></h3>
    <div class="btn-group">
      <button type="button" wire:click="exportCsv" class="btn btn-outline-primary">
        <i class="bi bi-download me-1"></i> Export
      </button>
      <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Cetak
      </button>
    </div>
  </div>

  {{-- FILTERS (PAKAI CARD + BS4 CLASSES) --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
      <form wire:submit.prevent>
        <div class="row">
          <div class="col-md-6 col-lg-3 mb-3">
            <label class="form-label small d-block">Tanggal</label>
            <input wire:model.debounce.500ms="date" type="date" class="form-control">
          </div>

          <div class="col-md-6 col-lg-3 mb-3">
            <label class="form-label small d-block">Kasir</label>
            <select wire:model="cashierId" class="form-control">
              <option value="">Semua Kasir</option>
              @foreach($this->cashiers as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 col-lg-3 mb-3">
            <label class="form-label small d-block">Metode Pembayaran</label>
            <select wire:model="paymentMethod" class="form-control">
              <option value="">Semua</option>
              @foreach($this->methodOptions as $m)
                <option value="{{ $m }}">{{ $m }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-6 col-lg-3 mb-3">
            <label class="form-label small d-block">Bank (opsional)</label>
            <input wire:model.debounce.500ms="bankName" type="text" class="form-control" placeholder="e.g., BCA, QRIS…">
          </div>
        </div>
      </form>

      <div wire:loading.flex class="align-items-center mt-2 text-primary">
        <div class="spinner-border spinner-border-sm mr-2" role="status"></div>
        <span>Memuat ulang data laporan…</span>
      </div>
    </div>
  </div>

  {{-- REPORT PANEL --}}
  <div class="report-panel">
    <div class="text-center mb-4">
      <h5 class="text-muted mb-2">INCOME BERSIH HARI INI</h5>
      <div class="net-profit-value">
        <span class="gradient-text">{{ format_currency($incomeBersih) }}</span>
      </div>
      <p class="text-muted mt-2">
        <span class="text-primary">{{ format_currency($omzet) }}</span> (Omzet) −
        <span class="text-danger">{{ format_currency($pengeluaran) }}</span> (Pengeluaran)
      </p>
    </div>

    {{-- TABS (BS4) --}}
    <ul class="nav nav-tabs" id="reportTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="summary-tab" data-toggle="tab" href="#summary-pane" role="tab" aria-controls="summary-pane" aria-selected="true">
          <i class="bi bi-grid-1x2-fill mr-2"></i>Ringkasan Penerimaan
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="transactions-tab" data-toggle="tab" href="#transactions-pane" role="tab" aria-controls="transactions-pane" aria-selected="false">
          <i class="bi bi-receipt-cutoff mr-2"></i>Detail Transaksi
        </a>
      </li>
    </ul>

    <div class="tab-content" id="reportTabContent">
      {{-- RINGKASAN --}}
      <div class="tab-pane fade show active" id="summary-pane" role="tabpanel" aria-labelledby="summary-tab">
        <div class="table-responsive">
          @php
            $sumTotal = $ringkasanPembayaran->sum('total_amount');
            $sumTrx   = $ringkasanPembayaran->sum('trx_count');
          @endphp
          <table class="table table-custom">
            <thead>
              <tr>
                <th>Metode</th>
                <th>Bank</th>
                <th class="text-center">Jumlah Trx</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ringkasanPembayaran as $row)
                <tr>
                  <td>{{ $row->payment_method }}</td>
                  <td>{{ $row->bank_name ?? '-' }}</td>
                  <td class="text-center">{{ $row->trx_count }}</td>
                  <td class="text-right font-weight-bold">{{ format_currency($row->total_amount) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-5">
                    <i class="bi bi-inboxes h3 d-block mb-2"></i>Tidak ada data.
                  </td>
                </tr>
              @endforelse
            </tbody>
            <tfoot>
              <tr class="table-total">
                <td colspan="2" class="font-weight-bold">TOTAL</td>
                <td class="text-center font-weight-bold">{{ $sumTrx }}</td>
                <td class="text-right font-weight-bolder">{{ format_currency($sumTotal) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      {{-- TRANSAKSI --}}
      <div class="tab-pane fade" id="transactions-pane" role="tabpanel" aria-labelledby="transactions-tab">
        <div class="table-responsive">
          <table class="table table-custom">
            <thead>
              <tr>
                <th>Tanggal</th>
                <th>Nomor Ref.</th>
                <th>Kasir</th>
                <th class="text-center">Status</th>
                <th class="text-right">Total</th>
              </tr>
            </thead>
            <tbody>
              @forelse($transaksi as $s)
                <tr>
                  <td>{{ \Illuminate\Support\Carbon::parse($s->date)->format('d/m/Y H:i') }}</td>
                  <td>{{ $s->reference }}</td>
                  <td>{{ $s->user->name ?? '-' }}</td>
                  <td class="text-center">
                    <span class="badge rounded-pill {{ ($s->payment_status ?? $s->status) === 'Paid' ? 'badge-paid' : 'badge-pending' }}">
                      {{ $s->payment_status ?? $s->status }}
                    </span>
                  </td>
                  <td class="text-right font-weight-bold">{{ format_currency($s->total_amount) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-5">
                    <i class="bi bi-inboxes h3 d-block mb-2"></i>Tidak ada transaksi.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- CSS KUSTOM --}}
  <style>
    .report-panel {
      background-color: #ffffff;
      border: 1px solid #e9ecef;
      border-radius: 1rem;
      padding: 1.5rem 1.75rem;
      box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.07);
    }
    .net-profit-value { font-size: clamp(3rem, 10vw, 5rem); font-weight: 800; line-height: 1.1; }
    .gradient-text {
      background: linear-gradient(45deg, #198754, #1ed760);
      -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .nav-tabs { border-bottom: 2px solid #e9ecef; }
    .nav-tabs .nav-link { border: none; border-bottom: 2px solid transparent; color: #6c757d; font-weight: 600; padding: .75rem 1.25rem; }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link { color: var(--bs-primary); border-color: var(--bs-primary); background-color: transparent; }
    .tab-content { padding-top: 1rem; }

    .table-custom { border-collapse: separate; border-spacing: 0; }
    .table-custom thead th { background-color: #f8f9fa; border-bottom: 2px solid #e9ecef; color: #495057; font-weight: 600; text-transform: uppercase; font-size: .75rem; letter-spacing: .5px; padding: .75rem 1rem; }
    .table-custom tbody tr { border-bottom: 1px solid #f1f3f5; }
    .table-custom tbody td { padding: 1rem; vertical-align: middle; }
    .table-custom tfoot tr.table-total td { font-size: 1.05rem; padding: 1rem; background-color: #f8f9fa; border-top: 2px solid #dee2e6; }
    .badge-paid { background-color: var(--bs-success-bg-subtle); color: var(--bs-success-text-emphasis); }
    .badge-pending { background-color: var(--bs-secondary-bg-subtle); color: var(--bs-secondary-text-emphasis); }

    @media print {
      body { background: #fff !important; }
      .c-sidebar, .c-header, .btn, .card.border-0.shadow-sm, .nav-tabs { display: none !important; }
      .report-panel { box-shadow: none !important; border: 1px solid #ddd !important; }
      .tab-content > .tab-pane { display: block !important; opacity: 1 !important; }
    }
  </style>
</div>
