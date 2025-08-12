@extends('layouts.app')

@section('title', 'Semua Penjualan')

@section('breadcrumb')
  <ol class="breadcrumb border-0 m-0">
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
    <li class="breadcrumb-item active">Semua Penjualan</li>
  </ol>
@endsection

@section('content')
<div class="container-fluid">

  {{-- ===== Filter Card ===== --}}
  <div class="row mb-3">
    <div class="col-12">
      <div class="card filter-card shadow-sm">
        <div class="card-body py-3">
          <div class="d-flex flex-wrap align-items-center mb-2">
            <h6 class="mb-0 mr-2">Filter</h6>
            <small class="text-muted">Pilih salah satu preset, atau gunakan Bulan / Rentang Tanggal.</small>
            <div class="ml-auto">
              <button id="btn_filter_reset" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
              </button>
            </div>
          </div>

          {{-- Preset chips --}}
          <div class="mb-3">
            <div class="chip-group" id="chip_group">
              <button type="button" class="chip" data-preset="today">Hari ini</button>
              <button type="button" class="chip" data-preset="this_week">Minggu ini</button>
              <button type="button" class="chip" data-preset="this_month">Bulan ini</button>
              <button type="button" class="chip" data-preset="last_month">Bulan lalu</button>
              <button type="button" class="chip" data-preset="this_year">Tahun ini</button>
            </div>
            <input type="hidden" id="filter_preset">
          </div>

          <div class="divider"><span>atau</span></div>

          {{-- Bulan & Rentang Tanggal --}}
          <div class="form-row align-items-end">
            <div class="col-md-3 mb-2">
              <label class="mb-1">Bulan</label>
              <div class="input-group icon-input">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="bi bi-calendar3"></i></span>
                </div>
                <input type="month" id="filter_bulan" class="form-control form-control-sm" placeholder="YYYY-MM">
              </div>
            </div>

            <div class="col-md-3 mb-2">
              <label class="mb-1">Dari</label>
              <div class="input-group icon-input">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
                </div>
                <input type="date" id="filter_dari" class="form-control form-control-sm">
              </div>
            </div>

            <div class="col-md-3 mb-2">
              <label class="mb-1">Sampai</label>
              <div class="input-group icon-input">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                </div>
                <input type="date" id="filter_sampai" class="form-control form-control-sm">
              </div>
            </div>

            <div class="col-md-3 mb-2 text-md-right">
              <button id="btn_filter_apply" class="btn btn-primary btn-sm">
                <i class="bi bi-funnel"></i> Terapkan
              </button>
              <div class="mt-1">
                <small class="text-muted">Jika isi tanggal, preset & bulan diabaikan.</small>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  {{-- ===== Tabel ===== --}}
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            {!! $dataTable->table() !!}
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('page_scripts')
  {!! $dataTable->scripts() !!}

  <style>
    .filter-card{border:1px solid #e9ecef;border-radius:.5rem}
    .chip-group{display:flex;flex-wrap:wrap;gap:.5rem}
    .chip{border:1px solid #d7dce2;background:#fff;border-radius:999px;padding:.25rem .75rem;font-size:.8125rem;cursor:pointer;transition:all .15s}
    .chip:hover{background:#f3f5f8}
    .chip.active{background:#eef4ff;border-color:#5a8dee;color:#2c5bdc}
    .divider{position:relative;text-align:center;margin:.25rem 0 1rem}
    .divider:before{content:"";height:1px;background:#e9ecef;position:absolute;left:0;right:0;top:50%}
    .divider span{position:relative;background:#fff;padding:0 .5rem;color:#98a2b3;font-size:.75rem}
    .icon-input .input-group-text{background:#fff}
    .dt-child{background:#f8fafc;border-left:3px solid #5a8dee}
  </style>

  <script>
  function collectFilters(){
    return {
      preset: document.getElementById('filter_preset').value || '',
      month : document.getElementById('filter_bulan').value || '',
      from  : document.getElementById('filter_dari').value || '',
      to    : document.getElementById('filter_sampai').value || ''
    };
  }
  function clearAll(){
    document.getElementById('filter_preset').value = '';
    ['filter_bulan','filter_dari','filter_sampai'].forEach(id=>{ const el=document.getElementById(id); if(el) el.value=''; });
    document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));
  }

  document.addEventListener('DOMContentLoaded', function(){
    $(document).on('init.dt', function(e, settings){
      if(settings.sTableId !== 'sales-table') return;
      const table = $('#sales-table').DataTable();

      $('#sales-table').on('preXhr.dt', function(evt,set,data){
        Object.assign(data, collectFilters());
      });

      document.querySelectorAll('.chip').forEach(chip=>{
        chip.addEventListener('click', function(){
          document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));
          this.classList.add('active');
          document.getElementById('filter_preset').value = this.dataset.preset;
          document.getElementById('filter_bulan').value = '';
          document.getElementById('filter_dari').value = '';
          document.getElementById('filter_sampai').value = '';
          table.ajax.reload();
        });
      });

      ['filter_bulan','filter_dari','filter_sampai'].forEach(id=>{
        const el = document.getElementById(id);
        el.addEventListener('change', ()=>{
          document.getElementById('filter_preset').value = '';
          document.querySelectorAll('.chip').forEach(c=>c.classList.remove('active'));
        });
      });

      document.getElementById('btn_filter_apply').addEventListener('click', ()=> table.ajax.reload());
      document.getElementById('btn_filter_reset').addEventListener('click', ()=>{ clearAll(); table.ajax.reload(); });

      // Toggle child-row
      $('#sales-table tbody').on('click', '.btn-row-detail', function(){
        const $btn = $(this);
        const tr = $btn.closest('tr');
        const row = table.row(tr);
        const url = $btn.data('url');

        if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
          $btn.html('<i class="bi bi-caret-down-square"></i>');
          return;
        }

        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        $.get(url, function(html){
          row.child(html).show();
          tr.addClass('shown');
          $btn.html('<i class="bi bi-caret-up-square"></i>');
        }).fail(function(jqXHR, textStatus, errorThrown) {
    // Buat pesan error yang lebih deskriptif
    const errorMsg = `Gagal memuat detail. (Error: ${jqXHR.status} - ${errorThrown})`;
    alert(errorMsg);
    $btn.html('<i class="bi bi-caret-down-square"></i>');
}).always(function(){
          $btn.prop('disabled', false);
        });
      });
    });
  });
  </script>
@endpush
