<article class="cart-item">
	<div class="row row-cols-sm-2 row-cols-1 my-3">
		<div class="col row row-cols-2 justify-content-center my-3">
			<div class="col-6 d-flex align-items-center justify-content-end">
				@if (isset($product->main_photo))
					<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/' . $product->main_photo)}}">
				@else
					<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/not-set.png')}}">
				@endif
			</div>
			<div class="col-6 flex-column d-flex justify-content-start">
				<a href="{{route('product', ['id' => $product->id])}}"><h3 class="cart-name">{{ $product->name }}</h3></a>
				<span class="cart-author">{{ $product->author->name }}</span>
			</div>
		</div>
		<div class="col row row-cols-2 justify-content-center my-3">
			<div class="col-6 d-flex align-items-center justify-content-end">
				<span class="cart-price">{{ $product->price }}€</span>
			</div>
			<div class=" col-6 d-flex align-items-center justify-content-start">
				<input class="cart-amount form-control" autocomplete="off" type="number" placeholder="1" value="{{ $product->pivot->count ?? $product->amount ?? $product['amount'] }}" onchange="update_cart_product(this)">
				<i class="fas fa-trash" onclick="delete_cart_product(this)" data-id="{{ $product->id }}"></i>
			</div>
		</div>
	</div>
</article>
