@csrf

{{-- Baris 1: Tanggal, Kategori, Nominal --}}
<div class="form-row">
  <div class="col-lg-4">
    <div class="form-group">
      <label class="mb-1">Tanggal <span class="text-danger">*</span></label>
      <input
        type="date"
        name="date"
        class="form-control @error('date') is-invalid @enderror"
        value="{{ old('date', optional($expense->date ?? null)->toDateString() ?? now()->toDateString()) }}"
        required
      >
      @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-lg-4">
    <div class="form-group">
      <label class="mb-1">Kategori <span class="text-danger">*</span></label>
      <select name="category_id" class="form-control @error('category_id') is-invalid @enderror" required>
        <option value="" disabled {{ old('category_id', $expense->category_id ?? null) ? '' : 'selected' }}>
          Pilih Kategori
        </option>
        @foreach($categories as $cat)
          <option value="{{ $cat->id }}" @selected(old('category_id', $expense->category_id ?? null) == $cat->id)>
            {{ $cat->category_name }}
          </option>
        @endforeach
      </select>
      @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror

      @if(($categories ?? collect())->isEmpty())
        <small class="text-muted d-block mt-1">
          Belum ada kategori.
          @if(Route::has('expense-categories.create'))
            <a href="{{ route('expense-categories.create') }}">Tambah di sini</a>.
          @endif
        </small>
      @endif
    </div>
  </div>

  <div class="col-lg-4">
    <div class="form-group">
      <label class="mb-1">Nominal (Rp) <span class="text-danger">*</span></label>
      <input
        type="text" id="amount" name="amount"
        class="form-control js-money @error('amount') is-invalid @enderror"
        value="{{ number_format((int) old('amount', $expense->amount ?? 0), 0, ',', '.') }}"
        placeholder="0" required
      >
      @error('amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

{{-- Baris 2: Deskripsi --}}
<div class="form-row">
  <div class="col-12">
    <div class="form-group">
      <label class="mb-1">Deskripsi <span class="text-danger">*</span></label>
      <input
        type="text" name="details"
        class="form-control @error('details') is-invalid @enderror"
        placeholder="Contoh: Beli Bensin Motor Operasional"
        value="{{ old('details', $expense->details ?? null) }}" required
      >
      @error('details') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

{{-- Baris 3: Metode Pembayaran & Bank --}}
<div class="form-row">
  <div class="col-lg-6">
    <div class="form-group">
      <label class="mb-1 d-block">Metode Pembayaran <span class="text-danger">*</span></label>
      @php $pm = old('payment_method', $expense->payment_method ?? 'Tunai'); @endphp
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_method" id="pm_cash" value="Tunai" @checked($pm==='Tunai')>
        <label class="form-check-label" for="pm_cash">Tunai</label>
      </div>
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="payment_method" id="pm_transfer" value="Transfer" @checked($pm==='Transfer')>
        <label class="form-check-label" for="pm_transfer">Transfer</label>
      </div>
      @error('payment_method') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-lg-6">
    <div class="form-group">
      <label class="mb-1">Bank (jika Transfer)</label>
      <input
        type="text" name="bank_name"
        class="form-control @error('bank_name') is-invalid @enderror"
        placeholder="Mandiri / BCA / BRI"
        value="{{ old('bank_name', $expense->bank_name ?? null) }}"
      >
      @error('bank_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
      <small class="text-muted">Isi hanya jika metode pembayaran = Transfer.</small>
    </div>
  </div>
</div>

{{-- Baris 4: Lampiran --}}
<div class="form-row">
  <div class="col-lg-6">
    <div class="form-group">
      <label class="mb-1">Lampiran Nota (opsional)</label>
      <input type="file" name="attachment" class="form-control @error('attachment') is-invalid @enderror"
             accept="image/*,application/pdf">
      @error('attachment') <div class="invalid-feedback">{{ $message }}</div> @enderror
      @isset($expense->attachment_path)
        <small class="text-muted d-block mt-1">File saat ini: {{ $expense->attachment_path }}</small>
      @endisset
    </div>
  </div>
</div>

{{-- Aksi --}}
<div class="mt-3 d-flex">
  <a href="{{ url()->previous() ?: route('expenses.index') }}" class="btn btn-light mr-2">Batal</a>
  <button type="submit" class="btn btn-primary">Simpan</button>
</div>

{{-- === AutoNumeric & helper submit/unmask (dipush sekali saja) === --}}
@once
  @push('page_scripts')
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4"></script>
    <script>
      (function () {
        const AN_OPTS = {
          decimalPlaces: 0,
          digitGroupSeparator: '.',
          decimalCharacter: ',',
          modifyValueOnWheel: false,
          emptyInputBehavior: 'zero'
        };

        function stripMoney(v){ return parseInt(String(v||'').replace(/[^\d\-]/g,''))||0; }

        function initAN(scope){
          (scope||document).querySelectorAll('input.js-money').forEach(function(el){
            if (el._an) return;
            el._an = new AutoNumeric(el, AN_OPTS);
          });
        }

        function bindUnmaskOnSubmit(scope){
          (scope||document).querySelectorAll('form').forEach(function(form){
            // hanya form yang punya input uang
            if (form._anBound || !form.querySelector('input.js-money')) return;
            form._anBound = true;
            form.addEventListener('submit', function(){
              form.querySelectorAll('input.js-money').forEach(function(i){
                i.value = i._an ? i._an.getNumber() : stripMoney(i.value);
              });
            });
          });
        }

        function toggleBank(form){
          const bank = form.querySelector('input[name="bank_name"]');
          const cash = form.querySelector('#pm_cash');
          const transfer = form.querySelector('#pm_transfer');
          if (!bank) return;
          function refresh(){
            const isTransfer = transfer && transfer.checked;
            bank.disabled = !isTransfer;
            if (!isTransfer) bank.value = '';
          }
          [cash, transfer].forEach(function(r){ if(r) r.addEventListener('change', refresh); });
          refresh();
        }

        document.addEventListener('DOMContentLoaded', function(){
          initAN();
          bindUnmaskOnSubmit();
          document.querySelectorAll('form').forEach(toggleBank);
        });
      })();
    </script>
  @endpush
@endonce
