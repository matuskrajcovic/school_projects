@extends ('layout.layout', ['title' => $product->name])

@section ('head')
    @parent
	<meta name="title" content="{{ $product->name }}">
	@if($product->product_type != 'merchandice')
		<meta name="author" content="{{ $product->author->name }}">
	@endif

@endsection
@section ('content')
	<div class="detail">
		<section class="main-info container-fluid">
			<div class="row row-cols-md-2 row-cols-1">
				<section class="col illustrations px-3 py-3">
					<section class="main-img d-flex justify-content-center">
						@if (isset($product->main_photo))
							<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/' . $product->main_photo)}}">
						@else
							<img alt="Thumbnail pre {{$product->name}}" class="thumbnail" src="{{asset('images/covers/not-set.png')}}">
						@endif
					</section>
					<section class="small-illustrations container-fluid my-3 d-flex justify-content-center align-middle">
						@if(count($photos) > 0)
						<ul class="row row-cols-md-4 row-cols-2 my-3">
							@foreach ($photos as $img)
							<img alt="Obal knihy {{ $product->name }}" src="{{ asset('images/covers/' . $img->path) }}">
							@endforeach
						</ul>
						@endif
					</section>
				</section>
				<section class="col main-info-text px-3 py-3">
					<h1>{{ $product->name }}</h1>
					<p>
						@if($product->product_type != 'merchandice')
							<span class="author">{{$product->author->name}}</span>
						@endif
						<span class="category">{{ $product->category->name }}</span><br><br>
						<span class="price">{{ $product->price }}€</span>
					</p>
					<p class="description">{{ $item->detail }}</p>
                    <form method="POST" action="{{route('add-to-cart')}}">
                        @csrf
                        <div class="form-group mx-2">
                            <label for="count">Počet:</label>
                            <input type="number" class="form-control" name="amount" min="1" step="1" placeholder="1" id="count" value="1">
						</div>
                        <input type="hidden" name="id" value="{{ $product->id }}">
                        <button type="submit" class="btn btn-primary my-3">Pridať do košíka</button>
                    </form>
                    <button class="btn btn-danger my-3" data-id="{{ $product->id }}" onclick="delete_cart_product(this)">Odobrať z košíka</button>
				</section>
			</div>
		</section>
		<section class="info container-fluid">
			<h2>Viac o
				@if($product->product_type == 'merchandice')
					produkte
				@else
					knihe
				@endif
			</h2>
			<ul class="details">
				<li>
					<dt>Názov:</dt>
					<dd>{{ $product->name }}</dd>
				</li>
				@if($product->product_type != 'merchandice')
					<li>
						<dt>Autor:</dt>
						<dd>{{ $product->author->name }}</dd>
					</li>
					<li>
						<dt>Vydavateľstvo:</dt>
						<dd>{{ $item->publisher }}</dd>
					</li>
					<li>
						<dt>Jazyk:</dt>
						<dd>{{ $item->language->name }}</dd>
					</li>
				@endif
				@if($product->product_type == 'book' or $product->product_type == 'e_book')
					<li>
						<dt>Počet strán:</dt>
						<dd>{{ $item->pages }}</dd>
					</li>
				@endif
			</ul>
			<p>{{ $item->long_detail }}</p>
		</section>
		<section class="reviews container-fluid">
			<h2>Recenzie</h2>
			@foreach($product->reviews as $review)
			<article class="review">
				<h4>{{ $review->user->name }}</h4>
				<p>{{ $review->content }}</p>
			</article>
			@endforeach
		</section>
		<section class="recommended container-fluid">
			<div class="top-selling row row-cols-1">
				<div class="col">
					<h2>Odporúčame tiež</h2>
					<div class="items container-fluid">
						<div class="row row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
							@foreach($recommended as $item)
								@include('layout.partials.item', ['product' => $item])
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</section>
	</div>
@endsection
