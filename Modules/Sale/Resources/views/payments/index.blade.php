@extends('layouts.app')

@section('title', 'Pembayaran – INV '.$sale->reference)

@section('breadcrumb')
  <ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales.index') }}">Sales</a></li>
    <li class="breadcrumb-item active">Pembayaran</li>
  </ol>
@endsection

@section('content')
<div class="container-fluid mb-4">

  {{-- Ringkasan --}}
  <div class="row g-3 mb-3">
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <small class="text-muted">Total</small>
        <h5 class="mb-0">{{ format_currency($sale->total_amount) }}</h5>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <small class="text-muted">Dibayar</small>
        <h5 class="mb-0">{{ format_currency($sale->paid_amount) }}</h5>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <small class="text-muted">Kurang</small>
        <h5 class="mb-0 text-danger">{{ format_currency($sale->due_amount) }}</h5>
      </div></div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm"><div class="card-body">
        <small class="text-muted">Status</small>
        <h5 class="mb-0">
          @php $ps = $sale->payment_status; @endphp
          <span class="badge {{ $ps==='Paid'?'badge-success':($ps==='Partial'?'badge-warning':'badge-secondary') }}">{{ $ps }}</span>
        </h5>
      </div></div>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Pembayaran – INV {{ $sale->reference }}</h5>
        <div class="d-flex gap-2">
          <a href="{{ route('sales.index') }}" class="btn btn-light">Kembali</a>
          <a href="{{ route('sale-payments.create', $sale->id) }}" class="btn btn-primary">
            Tambah Pembayaran
          </a>
        </div>
      </div>

      <div class="table-responsive">
        <table id="payments-table" class="table table-striped table-bordered w-100">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th>Referensi</th>
              <th>Metode</th>
              <th>Bank</th>
              <th class="text-right">Jumlah</th>
              <th>Catatan</th>
              <th>Dibuat</th>
              <th style="width:70px">Aksi</th>
            </tr>
          </thead>
          <tfoot>
            <tr>
              <th colspan="4" class="text-right">Total Dibayar</th>
              <th class="text-right" id="ft-total">Rp0</th>
              <th colspan="3"></th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const dt = $('#payments-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: @json(route('sale-payments.datatable', $sale->id)),
    order: [[0,'desc']],
    columns: [
      {data:'date', name:'date'},
      {data:'reference', name:'reference'},
      {data:'payment_method', name:'payment_method'},
      {data:'bank_name', name:'bank_name', defaultContent: ''},
      {data:'amount_formatted', name:'amount', className:'text-right', orderData:[4]},
      {data:'note', name:'note', orderable:false},
      {data:'created_at', name:'created_at'},
      {data:'actions', orderable:false, searchable:false, className:'text-center'}
    ],
    drawCallback: function(settings) {
      // hitung total di halaman saat ini
      let api = this.api();
      let sum = 0;
      api.column(4, {page:'current'}).data().each(function(v){
        // ambil angka dari "Rp1.234.567"
        const n = (v||'').toString().replace(/[^\d\-]/g,'');
        sum += parseInt(n||0,10);
      });
      document.getElementById('ft-total').textContent =
        new Intl.NumberFormat('id-ID', {style:'currency', currency:'IDR', maximumFractionDigits:0}).format(sum);
    }
  });

  // hapus row
  $(document).on('click', '.js-del-payment', function(){
    const url = this.dataset.url;
    if (!url) return;
    if (!confirm('Hapus pembayaran ini?')) return;
    fetch(url, {method:'DELETE', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'}})
      .then(r=>r.json()).then(j=>{
        if(j.ok){ dt.ajax.reload(null,false); }
        else{ alert(j.message||'Gagal menghapus'); }
      }).catch(()=>alert('Gagal terhubung'));
  });
});
</script>
@endpush
