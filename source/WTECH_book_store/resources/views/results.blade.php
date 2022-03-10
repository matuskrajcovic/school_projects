@extends ('layout.layout', ['title' => 'Vyhľadávanie', 'search_string' => $query['search'] ?? ''])

@section ('content')
<section class="category">
	<h1>
		@if(Request::is('category/*'))
			{{ $category_name }}
		@else
			@if(isset($query['search']))
				Vyhľadávanie pre: "{{ $query['search'] }}"
			@else
				Vyhľadávanie
			@endif
		@endif
	</h1>
	<section class="filters">
		@if(Request::is('category/*'))
			<ul class="categories">
				@foreach($subcategories as $cat)
				<li>
					<a class="button" href="{{ route('category', ['id' => $cat->id]) }}">
						<button class="btn btn-secondary">{{ $cat->name }}</button>
					</a>
				</li>
				@endforeach
			</ul>
		@endif
		@include('layout.partials.filters', ['query' => $query])
	</section>
	<section class="results container-fluid">
		<div class="row row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
		@foreach($products as $product)
			@include('layout.partials.item', ['product' => $product])
		@endforeach
		@if (count($products) < 1)
			Nenašli sa žiadne produkty
		@endif
		</div>
		<div class="d-flex justify-content-center my-5">
			{{ $products->links() }}
		</div>
	</section>
</section>
@endsection
