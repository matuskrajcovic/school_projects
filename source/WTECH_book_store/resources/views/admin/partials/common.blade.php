<div class="form-group my-2 mx-3">
	<label for="category_id">Kategória:</label>
	<select class="form-select" id="category_id" name="category_id">
		@foreach ($categories as $category)
			<option value="{{ $category->id }}" @if($product and $category->id == $product->category->id) selected @endif>{{ $category->name }}</option>
		@endforeach
	</select>
</div>
<div class="form-group my-2 mx-3">
	<label for="name">Názov:</label>
	<input type="text" class="form-control" id="name" name="name" placeholder="sem napíšte meno knihy" value="{{ $product->name ?? ''}}">
</div>
<div class="form-group my-2 mx-3">
	<label for="price">Cena:</label>
	<input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="sem napíšte cenu" value="{{ $product->price ?? ''}}">
</div>
