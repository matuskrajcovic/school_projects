@extends ('layout.layout',  ['title' => 'Admin panel'])

@section('header')
    @include('admin.partials.header')
@endsection

@section ('content')
<section class="admin">
    <h1>Admin panel</h1>
    <form method="GET" class="input-group mx-auto my-2" action="{{ route('admin') }}">
        <input class="form-control" id="search" name="search" type="text" placeholder="Vyhľadať" aria-label="Search" value="{{ $search_string ?? '' }}">
        <button type="submit" class="btn btn-secondary">Vyhľadať</button>
    </form>
	<section class="admin-results container-fluid">
		<div class="row row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
		@foreach($products as $product)
			@include('admin.partials.item', ['product' => $product])
		@endforeach
		</div>
		<div class="d-flex justify-content-center my-5">
			{{ $products->links() }}
		</div>
	</section>
</section>
@endsection
