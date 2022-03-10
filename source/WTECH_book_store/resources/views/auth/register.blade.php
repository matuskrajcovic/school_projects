@extends ('layout.layout', [
	'title' => 'Zaregistrujte sa',
	'description' => 'Registrácia na stránku Naše Kníhkupectvo'
])

@section ('content')
<section class="register container col-md-6 col-12 flex-grow-1 justify-content-center d-flex flex-column">
	<h2>Zaregistrujte sa</h2>

	<!-- Validation Errors -->
	<x-auth-validation-errors class="mb-4" :errors="$errors" />

	<p>
	Prosím, vyplňte nasledovné údaje:
	</p>
	<form method="POST" action="{{ route('register') }}">
		@csrf

		<div class="mb-3">
			<label class="form-label" for="name">Meno:</label>
			<input class="form-control" type="text" id="name" name="name" required autofocus>
		</div>
		<div class="mb-3">
			<label class="form-label" for="email">Email:</label>
			<input class="form-control" type="email" id="email" name="email" required>
		</div>
		<div class="mb-3">
			<label class="form-label" for="password">Heslo:</label>
			<input class="form-control" type="password" id="password" name="password" required autocomplete="new-password">
		</div>

		<div class="mb-3">
			<label class="form-label" for="password_confirmation">Heslo ešte raz:</label>
			<input class="form-control" type="password" id="password_confirmation" name="password_confirmation" required>
		</div>

		<div class="form-check mb-3">
			<input type="checkbox" name="terms" id="terms" value="agrees" required>
			<label for="terms">Súhlasím s podmienkami spracovávania osobných údajov.</label>
		</div>
		
		<div class="form-check mb-3">
			<input type="checkbox" name="age" id="age" value="agrees" required>
			<label for="age">Mám aspoň 16 rokov.</label>
		</div>

		<div class="flex items-center justify-end mt-4">
			<a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
				Už ste zaregistrovaný?
			</a>
		</div>

		<div>
			<button class="btn btn-primary my-4" type="submit" name="submit">Zaregistrovať sa</button>
		</div>
	</form>
</section>
@endsection
