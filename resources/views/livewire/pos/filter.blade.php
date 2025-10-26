<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="category">Product Category</label>
            <select wire:model.live="category" class="form-control" id="category">
                <option value="">All Products</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Filter Brand Baru -->
    <div class="col-md-4">
        <div class="form-group">
            <label for="brand">Product Brand</label>
            <select wire:model.live="brand" class="form-control" id="brand">
                <option value="">All Brands</option>
                @foreach ($brands as $b)
                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label for="showCount">Product Count</label>
            <select wire:model.live="showCount" class="form-control" id="showCount">
                <option value="9">9 Products</option>
                <option value="15">15 Products</option>
                <option value="21">21 Products</option>
                <option value="30">30 Products</option>
                <option value="">All Products</option>
            </select>
        </div>
    </div>
</div>
