@csrf
<div class="form-row">
  <div class="col-lg-6">
    <div class="form-group">
      <label class="mb-1">Nama Kategori <span class="text-danger">*</span></label>
      <input type="text" name="category_name"
             class="form-control @error('category_name') is-invalid @enderror"
             value="{{ old('category_name', $category->category_name ?? null) }}"
             placeholder="Contoh: Bensin / Makan Karyawan / ATK"
             required>
      @error('category_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>

  <div class="col-lg-6">
    <div class="form-group">
      <label class="mb-1">Deskripsi (opsional)</label>
      <input type="text" name="category_description"
             class="form-control @error('category_description') is-invalid @enderror"
             value="{{ old('category_description', $category->category_description ?? null) }}"
             placeholder="Catatan singkat">
      @error('category_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
  </div>
</div>

<div class="mt-3 d-flex">
  <a href="{{ url()->previous() ?: route('expense-categories.index') }}" class="btn btn-light mr-2">Batal</a>
  <button type="submit" class="btn btn-primary">Simpan</button>
</div>
