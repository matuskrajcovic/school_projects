<div class="form-group my-2 mx-3">
	<label for="main_photo">Hlavný obrázok:</label>
	<input type="file" class="form-control" id="main_photo" name="main_photo">
</div>
<div class="form-group my-2 mx-3">
	<label for="images">Ostatné obrázky:</label>
	<input type="file" class="form-control" id="images" name="images[]" multiple>
</div>
@if(!Request::is('admin/create/*'))
<div class="container-fluid my-5 product-images">
	@if(isset($main_image))
	<h2>Hlavný obrázok</h2>
	<div class="row row-cols-xl-6 row-cols-md-4 row-cols-sm-3">
		<div class="col my-3 mx-3">
			<img alt="Obal produktu {{$product->title}}" src="{{asset('images/covers/' . $main_image)}}">
			<button class="btn btn-danger my-2" data-id="{{ $product->id }}">Vymazať</button>
		</div>
	</div>
	@endif
	@if(count($images) > 0)
		<h2>Vedľajšie obrázky</h2>
	@endif
	<ul class="row row-cols-xl-6 row-cols-md-4 row-cols-sm-3">
	@foreach($images as $image)
		<li class="col my-3 mx-3">
			<img alt="Obal produktu {{$product->title}}" src="{{asset('images/covers/' . $image->path)}}">
			<button class="btn btn-danger my-2" data-id="{{ $product->id }}" data-path="{{ $image->path }}" data-imageid="{{ $image->id }}">Vymazať</button>
		</li>
	@endforeach
	</ul>
</div>
@endif
