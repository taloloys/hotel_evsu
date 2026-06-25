<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', optional($product ?? null)->name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
            @foreach($categories as $category)
            <option value="{{ $category->category_id }}" @selected(old('category_id', optional($product ?? null)->category_id) == $category->category_id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', optional($product ?? null)->description) }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Price</label>
        <input type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price', optional($product ?? null)->price) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Stock Quantity</label>
        <input type="number" min="0" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', optional($product ?? null)->stock_quantity ?? 0) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Low Stock Threshold</label>
        <input type="number" min="0" name="low_stock_threshold" class="form-control" value="{{ old('low_stock_threshold', optional($product ?? null)->low_stock_threshold) }}" placeholder="Uses global default if empty">
    </div>
    <div class="col-md-6">
        <label class="form-label">Product Image</label>
        <input type="file" name="image" class="form-control" accept="image/*">
        @if(!empty(optional($product ?? null)->image_url))
            <img src="{{ $product->image_url }}" alt="" class="mt-2 rounded" style="max-height:80px;">
        @endif
    </div>
    <div class="col-md-6 d-flex align-items-end">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', optional($product ?? null)->is_active ?? true))>
            <label class="form-check-label">Active</label>
        </div>
    </div>
</div>
