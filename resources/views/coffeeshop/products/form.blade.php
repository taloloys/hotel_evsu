<div class="row g-3">

    {{-- PRODUCT NAME --}}
    <div class="col-md-6">
        <label class="form-label text-muted small">Product Name</label>
        <input type="text"
               name="name"
               class="form-control"
               value="{{ old('name', optional($product ?? null)->name) }}"
               required>
    </div>

    {{-- CATEGORY --}}
    <div class="col-md-6">
        <label class="form-label text-muted small">Category</label>
        <select name="category_id" class="form-select" required>
            @foreach($categories as $category)
                <option value="{{ $category->category_id }}"
                    @selected(old('category_id', optional($product ?? null)->category_id) == $category->category_id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- DESCRIPTION --}}
    <div class="col-12">
        <label class="form-label text-muted small">Description</label>
        <textarea name="description"
                  class="form-control"
                  rows="3">{{ old('description', optional($product ?? null)->description) }}</textarea>
    </div>

    {{-- PRICE --}}
    <div class="col-md-4">
        <label class="form-label text-muted small">Price</label>
        <div class="input-group">
            <span class="input-group-text">₱</span>
            <input type="number"
                   step="0.01"
                   min="0"
                   name="price"
                   class="form-control"
                   value="{{ old('price', optional($product ?? null)->price) }}"
                   required>
        </div>
    </div>

    {{-- STOCK --}}
    <div class="col-md-4">
        <label class="form-label text-muted small">Stock Quantity</label>
        <input type="number"
               min="0"
               name="stock_quantity"
               class="form-control"
               value="{{ old('stock_quantity', optional($product ?? null)->stock_quantity ?? 0) }}"
               required>
    </div>

    {{-- LOW STOCK --}}
    <div class="col-md-4">
        <label class="form-label text-muted small">Low Stock Threshold</label>
        <input type="number"
               min="0"
               name="low_stock_threshold"
               class="form-control"
               value="{{ old('low_stock_threshold', optional($product ?? null)->low_stock_threshold) }}"
               placeholder="Uses global default if empty">
    </div>

    {{-- IMAGE --}}
    <div class="col-md-6">
        <label class="form-label text-muted small">Product Image</label>

        <input type="file"
               name="image"
               class="form-control"
               accept="image/*">

        @if(!empty(optional($product ?? null)->image_url))
            <div class="mt-2">
                <img src="{{ $product->image_url }}"
                     class="rounded border"
                     style="max-height:80px;">
            </div>
        @endif
    </div>

    {{-- STATUS --}}
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check form-switch ps-0">
            <input class="form-check-input ms-0"
                   type="checkbox"
                   name="is_active"
                   value="1"
                   @checked(old('is_active', optional($product ?? null)->is_active ?? true))>

            <label class="form-check-label ms-2">
                Active Product
            </label>
        </div>
    </div>

</div>