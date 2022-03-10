<article class="item col text-center my-3">
	<a href="{{route('product', ['id' => $product->id])}}">
		<div class="thumbnail-container d-flex align-middle justify-content-center">
			@if(isset($product->main_photo))
				<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/' . $product->main_photo ?? 'not-set.png')}}">
			@else
				<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/not-set.png')}}">
			@endif
		</div>
		<h3>{{$product->name}}</h3>
	</a>
	@if($product->product_type != 'merchandice')
		<span class="author">{{$product->author->name}}</span>
	@endif
	<i class="fas fa-shopping-cart"></i>
	<span class="price">
		<data value="5">{{$product->price}}</data>€
	</span>
</article>
