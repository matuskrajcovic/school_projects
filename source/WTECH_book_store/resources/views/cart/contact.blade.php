@extends('layout.layout', ['title' => "Kontaktné údaje"])

@section('content')
    @include('cart.partials.header', ['step' => 2])
	<section class="cart">
		<form method="POST" class="container col-md-6 col-12 cart-contact-details flex-grow-1 justify-content-center d-flex flex-column" action="{{ route('cart-contact')}}">
			@csrf
			<input type="hidden" name="step" value="3">
			<div class="mb-3">
				<label class="form-label">Meno:</label>
				<input class="cart-name form-control" name="name" type="text" value="{{ $user->name ?? '' }}" required>
			</div>
			<div class="mb-3">
				<label class="form-label">E-mail:</label>
				<input class="cart-email form-control" name="email" type="email" value="{{ $user->email ?? '' }}" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Adresa:</label>
				<input class="cart-address form-control" name="address" type="text" value="{{ $address->address ?? '' }}" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Mesto:</label>
				<input class="cart-address form-control" name="city" type="text" value="{{ $address->city ?? '' }}" required>
			</div>
			<div class="mb-3">
				<label class="form-label">PSČ:</label>
				<input class="cart-postal-code form-control" name="postal_code" type="text" value="{{ $address->postal_code ?? '' }}" required>
			</div>
			<div class="mb-3">
				<label class="form-label">Telefónne číslo:</label>
				<input class="cart-phone form-control" name="phone" value="{{ $address->phone ?? '' }}" type="text" required>
			</div>
			<button class="btn btn-primary" type="submit">Pokračovať</button>
		</form>
	</section>
@endsection
