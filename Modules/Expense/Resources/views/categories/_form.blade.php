<div class="space-y-6">
    {{-- Category Name --}}
    <div>
        <label for="category_name" class="block mb-2 text-sm font-medium text-zinc-900">
            Nama Kategori <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <i class="bi bi-tag text-zinc-400"></i>
            </div>
            <input type="text" 
                   name="category_name" 
                   id="category_name" 
                   class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 @error('category_name') border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 @enderror" 
                   placeholder="Contoh: Transport, ATK, Konsumsi" 
                   value="{{ old('category_name', $category->category_name ?? '') }}" 
                   maxlength="100"
                   required 
                   autofocus>
        </div>
        @error('category_name')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <div class="mt-1 flex justify-end">
            <small class="text-xs text-zinc-500">
                <span id="name-counter">0</span>/100 karakter
            </small>
        </div>
    </div>

    {{-- Category Description --}}
    <div>
        <label for="category_description" class="block mb-2 text-sm font-medium text-zinc-900">
            Deskripsi <span class="bg-zinc-100 text-zinc-800 text-xs font-medium px-2.5 py-0.5 rounded ml-2">Opsional</span>
        </label>
        <div class="relative">
            <div class="absolute top-3 left-3 pointer-events-none">
                <i class="bi bi-card-text text-zinc-400"></i>
            </div>
            <textarea name="category_description" 
                      id="category_description" 
                      rows="4" 
                      class="bg-zinc-50 border border-zinc-300 text-zinc-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 @error('category_description') border-red-500 bg-red-50 text-red-900 placeholder-red-700 focus:ring-red-500 focus:border-red-500 @enderror"
                      placeholder="Jelaskan kategori ini untuk memudahkan identifikasi..."
                      maxlength="500">{{ old('category_description', $category->category_description ?? '') }}</textarea>
        </div>
        @error('category_description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <div class="mt-1 flex justify-end">
            <small class="text-xs text-zinc-500">
                <span id="desc-counter">0</span>/500 karakter
            </small>
        </div>
    </div>
</div>

@push('page_scripts')
<script>
    $(document).ready(function() {
        // Character counter logic
        function updateCounter(input, counter) {
            counter.text(input.val().length);
        }

        const nameInput = $('#category_name');
        const nameCounter = $('#name-counter');
        const descInput = $('#category_description');
        const descCounter = $('#desc-counter');

        nameInput.on('input', () => updateCounter(nameInput, nameCounter));
        descInput.on('input', () => updateCounter(descInput, descCounter));

        // Initial check
        updateCounter(nameInput, nameCounter);
        updateCounter(descInput, descCounter);
    });
</script>
@endpush
