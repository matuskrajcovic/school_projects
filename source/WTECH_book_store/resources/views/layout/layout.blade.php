@section('head')
	@include('layout.partials.head')
@endsection

@section('header')
	@include('layout.partials.header')
@endsection

@section('script-variables')
	@include('layout.partials.script-variables')
@endsection

@section('scripts')
	@include('layout.partials.scripts')
@endsection

<!doctype html>
<html lang="sk">
<head>
	@yield('head')
</head>

<body>
	<header>
		@yield('header')
	</header>

	<main class="container">
		@yield('content')
	</main>

	<footer>
		@include('layout.partials.footer')
	</footer>

	<script>
		@yield('script-variables')
	</script>
	@yield('scripts')
</body>
</html>
