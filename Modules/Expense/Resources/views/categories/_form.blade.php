<div class="card-body">
    {{-- Category Name --}}
    <div class="form-group">
        <label for="category_name" class="font-weight-bold">
            Nama Kategori <span class="text-danger">*</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="cil-tag"></i>
                </span>
            </div>
            <input type="text" 
                   class="form-control @error('category_name') is-invalid @enderror" 
                   id="category_name" 
                   name="category_name" 
                   value="{{ old('category_name', $category->category_name ?? '') }}"
                   placeholder="Contoh: Transport, ATK, Konsumsi"
                   maxlength="100"
                   required
                   autofocus>
            @error('category_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">
            <i class="cil-info mr-1"></i>
            <span id="name-counter">0</span>/100 karakter
        </small>
    </div>

    {{-- Category Description --}}
    <div class="form-group">
        <label for="category_description" class="font-weight-bold">
            Deskripsi
            <span class="badge badge-secondary">Opsional</span>
        </label>
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <i class="cil-notes"></i>
                </span>
            </div>
            <textarea class="form-control @error('category_description') is-invalid @enderror" 
                      id="category_description" 
                      name="category_description" 
                      rows="4"
                      maxlength="500"
                      placeholder="Jelaskan kategori ini untuk memudahkan identifikasi..."
                      >{{ old('category_description', $category->category_description ?? '') }}</textarea>
            @error('category_description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <small class="form-text text-muted">
            <i class="cil-info mr-1"></i>
            <span id="desc-counter">0</span>/500 karakter
        </small>
    </div>
</div>

@push('page_scripts')
<script>
    $(document).ready(function() {
        // Character counter for category name
        const nameInput = $('#category_name');
        const nameCounter = $('#name-counter');
        
        nameInput.on('input', function() {
            nameCounter.text($(this).val().length);
        });
        
        // Trigger on load
        nameCounter.text(nameInput.val().length);
        
        // Character counter for description
        const descInput = $('#category_description');
        const descCounter = $('#desc-counter');
        
        descInput.on('input', function() {
            descCounter.text($(this).val().length);
        });
        
        // Trigger on load
        descCounter.text(descInput.val().length);
    });
</script>
@endpush
