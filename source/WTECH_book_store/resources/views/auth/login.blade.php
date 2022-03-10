@extends ('layout.layout', [
	'title' => 'Prihláste sa',
	'description' => 'Prihlásenie na stránku Naše Kníhkupectvo'
])

@section ('content')
<section class="login container col-md-6 col-12 flex-grow-1 justify-content-center d-flex flex-column">
	<h2>Prihláste sa</h2>

	<!-- Session Status -->
	<x-auth-session-status class="mb-4" :status="session('status')" />

	<!-- Validation Errors -->
	<x-auth-validation-errors class="mb-4" :errors="$errors" />
	<p>
	Prosím, vyplňte nasledovné údaje:
	</p>
	<form method="POST" action="{{ route('login') }}">
		@csrf

		<div class="mb-3">
			<label class="form-label" for="email">Váš email:</label>
			<input class="form-control" type="email" id="email" name="email" placeholder="E-mail" required autofocus>
		</div>

		<div class="mb-3">
			<label class="form-label" for="password">Vaše heslo:</label>
			<input class="form-control" type="password" id="password" name="password" placeholder="Heslo" required autocomplete="current-password">
		</div>

		<!-- Remember Me -->
		<div class="block mt-4">
			<input id="remember_me" type="checkbox" class="form-check-label rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
			<label for="remember_me" class="form-check-label inline-flex items-center">Zapamätajte si ma</label>
		</div>
		
		<p>
			Ak nemáte účet, tak sa môžete <a href="{{route('register')}}">zaregistrovať</a>.
		</p>

		<div class="mb-3">
			<button class="btn btn-primary" type="submit" name="submit">Prihlásiť sa</button>
		</div>

		<div class="flex items-center justify-end mt-4">
			@if (Route::has('password.request'))
				<a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
					Zabudli ste svoje heslo?
				</a>
			@endif
		</div>
	</form>
</section>
@endsection
