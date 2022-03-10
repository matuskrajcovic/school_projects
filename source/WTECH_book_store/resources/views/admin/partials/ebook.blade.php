<section class="col">
	@include('admin.partials.common')
	<div class="form-group my-2 mx-3">
		<label for="author_id">Autor:</label>
		<select class="form-select" id="author_id" name="author_id" placeholder="vyberte autora">
			@foreach ($authors as $author)
				<option value="{{ $author->id }}" @if($product and $author->id == $product->author->id) selected @endif>{{ $author->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="form-group my-2 mx-3">
		<label for="publisher">Vydavateľ:</label>
		<input type="text" class="form-control" id="publisher" name="publisher" placeholder="sem napíšte meno vydavateľa" value="{{$item->publisher ?? ''}}">
	</div>
	<div class="form-group my-2 mx-3">
		<label for="year">Rok:</label>
		<input type="text" class="form-control" id="year" name="year" placeholder="sem napíšte rok" value="{{$item->year ?? ''}}">
	</div>
	<div class="form-group my-2 mx-3">
		<label for="pages">Počet strán:</label>
		<input type="number" class="form-control" id="pages" name="pages" placeholder="sem napíšte počet" value="{{$item->pages ?? ''}}">
	</div>
	<div class="form-group my-2 mx-3">
		<label for="language_id">Jazyk:</label>
		<select class="form-select" id="language_id" name="language_id">
			@foreach ($languages as $language)
				<option value="{{ $language->id }}" @if($item and $language->id == $item->language->id) selected @endif>{{ $language->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="form-group my-2 mx-3">
		<label for="format">Formát:</label>
		<select class="form-select" id="format" name="format" placeholder="vyberte formát">
			<option value="epub" @if($item and $item->format == "epub") selected @endif>EPUB</option>
			<option value="mobi" @if($item and $item->format == "mobi") selected @endif>MOBI</option>
			<option value="pdf" @if($item and $item->format == "pdf") selected @endif>PDF</option>
		</select>
	</div>
</section>
<section class="col">
	<div class="form-group my-2 mx-3">
		<label for="detail">Detail produktu:</label>
		<textarea class="form-control" id="detail" name="detail" placeholder="max 100 znakov">{{$item->detail ?? ''}}</textarea>
	</div>
	<div class="form-group my-2 mx-3">
		<label for="long_detail">Dlhý detail produktu:</label>
		<textarea class="form-control" id="long_detail" name="long_detail" placeholder="max 5000 znakov">{{$item->long_detail ?? ''}}</textarea>
	</div>
	@include('admin.partials.images')
</section>

