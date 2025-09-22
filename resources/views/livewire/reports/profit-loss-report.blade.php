<div wire:key="pl-root">
  {{-- TOOLBAR --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 fw-light">Laporan <span class="fw-bold">Laba / Rugi</span></h3>
    <div class="btn-group">
      <button type="button" wire:click="exportCsv" class="btn btn-outline-primary">
        <i class="bi bi-download mr-1"></i> Export
      </button>
      <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
        <i class="bi bi-printer mr-1"></i> Cetak
      </button>
    </div>
  </div>

  {{-- FILTERS (BS4 friendly + card) --}}
  <div class="card border-0 shadow-sm mb-4 filter-panel">
    <div class="card-body">
      <form wire:submit.prevent="generateReport">
        <div class="form-row align-items-center">
          <div class="col-lg-5 mb-2 mb-lg-0">
            <input wire:model="startDate" type="date" class="form-control form-control-lg">
          </div>
          <div class="col-lg-5 mb-2 mb-lg-0">
            <input wire:model="endDate" type="date" class="form-control form-control-lg">
          </div>
          <div class="col-lg-2">
            <button type="submit" class="btn btn-primary btn-lg w-100 d-flex align-items-center justify-content-center" style="height:50px;">
              <span wire:loading.remove wire:target="generateReport"><i class="bi bi-arrow-right-circle-fill"></i></span>
              <span wire:loading wire:target="generateReport" class="spinner-border spinner-border-sm"></span>
            </button>
          </div>
        </div>

        <div wire:loading.flex wire:target="generateReport" class="align-items-center mt-3 text-muted">
          <div class="spinner-border text-primary spinner-border-sm mr-2" role="status"></div>
          <span>Menganalisa data...</span>
        </div>
      </form>
    </div>
  </div>

  {{-- REPORT PANEL --}}
  @if($revenue !== null)
    <div class="report-panel">
      <div class="row">
        <div class="col-lg-5 text-center text-lg-left">
          <h5 class="text-muted">LABA BERSIH PERIODE INI</h5>
          <div class="net-profit-value">
            <span class="gradient-text">{{ format_currency($netProfit) }}</span>
          </div>
          <p class="text-muted mt-2">
            <span class="text-success">{{ format_currency($grossProfit) }}</span> (Laba Kotor) -
            <span class="text-warning">{{ format_currency($expenses) }}</span> (Beban)
          </p>
        </div>

        <div class="col-lg-1 d-none d-lg-flex justify-content-center">
          <div class="vertical-divider"></div>
        </div>

        <div class="col-lg-6">
          <h5 class="text-muted mb-4 pt-3 pt-lg-0">Rincian Perhitungan</h5>

          <div class="kpi-list">
            <div class="kpi-item">
              <div class="kpi-label">
                <i class="bi bi-graph-up-arrow mr-3"></i>
                <span>REVENUE (BERSIH)</span>
              </div>
              <div class="kpi-value text-primary">{{ format_currency($revenue) }}</div>
            </div>

            <div class="kpi-item">
              <div class="kpi-label">
                <i class="bi bi-cart-x mr-3"></i>
                <span>COGS (HPP BERSIH)</span>
              </div>
              <div class="kpi-value text-danger">{{ format_currency($cogs) }}</div>
            </div>

            <div class="kpi-item">
              <div class="kpi-label">
                <i class="bi bi-piggy-bank mr-3"></i>
                <span>GROSS PROFIT</span>
              </div>
              <div class="kpi-value text-success">{{ format_currency($grossProfit) }}</div>
            </div>

            <div class="kpi-item">
              <div class="kpi-label">
                <i class="bi bi-wallet2 mr-3"></i>
                <span>EXPENSES</span>
              </div>
              <div class="kpi-value text-warning">{{ format_currency($expenses) }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif

  {{-- CSS KUSTOM (tanpa variabel BS5) --}}
  <style>
    .filter-panel .form-control { background-color:#f8f9fa; border-color:#dee2e6; }
    .filter-panel .form-control:focus {
      background-color:#fff;
      border-color:#20a8d8;             /* primary CoreUI/BS4 */
      box-shadow:0 0 0 .2rem rgba(32,168,216,.25);
    }

    .report-panel {
      background:#fff; border:1px solid #e9ecef; border-radius:1rem;
      padding:2rem 2.5rem; margin-top:2rem; box-shadow:0 8px 32px rgba(0,0,0,.07);
    }
    .net-profit-value { font-size:clamp(2.5rem,8vw,4.5rem); font-weight:800; line-height:1.1; margin-bottom:.5rem; }
    .gradient-text{
      background:linear-gradient(45deg,#20a8d8,#63c2de); /* nuansa CoreUI */
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }

    .vertical-divider { width:1px; background:#e9ecef; height:100%; }

    .kpi-list .kpi-item { display:flex; justify-content:space-between; align-items:center; padding:1.25rem 0; border-bottom:1px solid #f1f3f5; }
    .kpi-list .kpi-item:last-child { border-bottom:none; }

    .kpi-item .kpi-label { display:flex; align-items:center; color:#6c757d; font-weight:500; }
    .kpi-item .kpi-label i { font-size:1.5rem; color:#adb5bd; }
    .kpi-item .kpi-value { font-size:1.5rem; font-weight:700; }

    @media print{
      body { background:#fff!important; }
      .c-sidebar,.c-header,.btn,.filter-panel { display:none!important; }
      .report-panel { box-shadow:none!important; border:1px solid #ddd!important; }
      .gradient-text{ -webkit-print-color-adjust:exact!important; color-adjust:exact!important; color:#20a8d8!important; }
    }
  </style>
</div>
