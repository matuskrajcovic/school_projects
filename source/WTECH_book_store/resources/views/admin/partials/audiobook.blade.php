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
		<label for="language_id">Jazyk:</label>
		<select class="form-select" id="language_id" name="language_id">
			@foreach ($languages as $language)
				<option value="{{ $language->id }}" @if($item and $language->id == $item->language->id) selected @endif>{{ $language->name }}</option>
			@endforeach
		</select>
	</div>
	<div class="form-group my-2 mx-3">
		<label for="duration">Dĺžka:</label>
		<input type="text" class="form-control" id="duration" name="duration" placeholder="sem napíšte dĺžku" value="{{$item->duration ?? ''}}">
	</div>
	<div class="form-group my-2 mx-3">
		<label for="format">Formát:</label>
		<select class="form-select" id="format" name="format" placeholder="vyberte formát" value="{{$item->format ?? ''}}">
			<option value="mp3" @if($item and $item->format == "mp3") selected @endif>MP3</option>
			<option value="aax" @if($item and $item->format == "aax") selected @endif>AAX</option>
			<option value="ogg" @if($item and $item->format == "ogg") selected @endif>OGG</option>
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

