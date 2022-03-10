@extends ('layout.layout', ['title' => 'Naše Kníkpupectvo'])

@section ('content')
<section class="banner">
	<img src="{{ asset('assets/banner.jpg') }}">
</section>
<h1>Naše Kníhkupectvo</h1>
<div class="container-fluid">
	<div class="row row-cols-md-2 row-cols-1">
		<section class="favorites col">
			<h2>Najobľúbenejšie</h2>
			<div class="items container-fluid">
				<div class="row row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-2">
					@foreach($favorites as $item)
						@include('layout.partials.item', ['product' => $item])
					@endforeach
				</div>
			</div>
		</section>
		<section class="top-selling col">
			<h2>Najpredávanejšie</h2>
			<div class="items container-fluid">
				<div class="row row-cols-xl-3 row-cols-lg-2 row-cols-md-2 row-cols-sm-2 row-cols-2">
					@foreach($topselling as $item)
						@include('layout.partials.item', ['product' => $item])
					@endforeach
				</div>
			</div>
		</section>
	</div>
	<section class="new-products row row-cols-1">
		<div class="col">
			<h2>Knižné novinky</h2>
			<div class="items container-fluid">
				<div class="row row-cols-xxl-5 row-cols-lg-4 row-cols-md-3 row-cols-2">
					@foreach($new as $item)
						@include('layout.partials.item', ['product' => $item->product])
					@endforeach
				</div>
			</div>
		</div>
	</section>
</div>
@endsection
