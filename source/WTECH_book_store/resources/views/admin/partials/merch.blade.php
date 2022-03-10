<section class="col">
	@include('admin.partials.common')
	<div class="form-group my-2 mx-3">
		<label for="stock">Na sklade:</label>
		<input type="number" class="form-control" id="stock" name="stock" placeholder="sem napíšte sklad" value="{{$item->stock ?? ''}}">
	</div>
</section>
<section class="col">
	<div class="form-group my-2 mx-3">
		<label for="detail">Detail produktu:</label>
		<textarea class="form-control" id="detail" name="detail" placeholder="max 100 znakov">{{ $item->detail ?? '' }}</textarea>
	</div>
	@include('admin.partials.images')
</section>

