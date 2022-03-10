@extends('layout.layout', ['title' => "Objednávka vybavená"])

@section('content')
	<section class="cart">
		<h1>Objednávka vybavená</h1>
		<a href="{{route('homepage')}}" class="d-flex justify-content-center"><h3>Na domovskú obrazovku</h3></a>
	</section>
@endsection
