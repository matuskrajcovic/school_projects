@extends ('layout.layout', ['title' => $title])

@section('header')
	@include('admin.partials.header')
@endsection

@section ('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(Request::is('admin/create/*'))
	<h1>Vytvorte novú položku</h1>
@else
	<h1>Upraviť produkt: {{$product->name}}</h1>
@endif

<form method="POST" enctype="multipart/form-data">
	@csrf
	@if(Route::current()->getName() == 'edit-product')
		<input name="_method" type="hidden" value="PUT">
	@endif
	<div class="container-fluid">
		<div class="row row-cols-md-2 row-cols-1">
			@include($fields)
			@if(!Request::is('admin/create/*'))
				<input name="product_type" type="hidden" value="{{ $product->product_type }}">
			@endif
		</div>
		<div class="d-flex justify-content-center">
			<button type="submit" class="btn btn-primary my-2 mx-3">
			@if(Request::is('admin/create/*'))
				Vytvoriť
			@else
				Upraviť
			@endif
			</button>
		</div>
	</div>
</form>
@endsection

@section('scripts')
	@parent
	<script src="{{ asset('js/create-product.js') }}"></script>

	@if(!Request::is('admin/create/*'))
		<script src="{{ asset('js/image-events.js') }}"></script>
	@endif
@endsection
