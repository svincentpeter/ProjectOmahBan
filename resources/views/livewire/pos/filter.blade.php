<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <div>
        <label for="category" class="block mb-2 text-sm font-medium text-slate-700">Kategori Produk</label>
        <select wire:model.live="category" id="category" 
            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full p-2.5 shadow-sm transition-all hover:border-ob-primary">
            <option value="">Semua Produk</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
            @endforeach
        </select>
    </div>

    <!-- Filter Brand Baru -->
    <div>
        <label for="brand" class="block mb-2 text-sm font-medium text-slate-700">Merek / Brand</label>
        <select wire:model.live="brand" id="brand" 
            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full p-2.5 shadow-sm transition-all hover:border-ob-primary">
            <option value="">Semua Merek</option>
            @foreach ($brands as $b)
                <option value="{{ $b->id }}">{{ $b->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="showCount" class="block mb-2 text-sm font-medium text-slate-700">Jumlah Tampil</label>
        <select wire:model.live="showCount" id="showCount" 
            class="bg-white border border-slate-200 text-slate-900 text-sm rounded-xl focus:ring-ob-primary focus:border-ob-primary block w-full p-2.5 shadow-sm transition-all hover:border-ob-primary">
            <option value="9">9 Produk</option>
            <option value="15">15 Produk</option>
            <option value="21">21 Produk</option>
            <option value="30">30 Produk</option>
            <option value="">Semua Produk</option>
        </select>
    </div>
</div>
