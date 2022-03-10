@extends('layout.layout', ['title' => "Sumarizácia objednávky"])
@section('content')
    @include('cart.partials.header', ['step' => 5])
<form method="POST" action="{{ route('cart-summary') }}">
	<section class="cart cart-summary-content justify-content-center flex-grow-1 row row-cols-2">
        @csrf
		<section class="items container col-12 d-flex flex-column flex-grow-1 justify-content-center">
            @foreach ($products ?? [] as $product)
                @include('cart.partials.cart-item-summary', $product)
            @endforeach
		</section>

		<section class="col-12 col-md-6 my-5">
			<table class="table">
				<tr>
					<td><strong>Meno:</strong></td>
					<td>{{ $user['name'] }}</td>
				</tr>
				<tr>
					<td><strong>Adresa:</strong></td>
					<td>{{ $user['city'], $user['address'] }}</td>
				</tr>
				<tr>
					<td><strong>PSČ:</strong></td>
					<td>{{ $user['postal_code'] }}</td>
				</tr>
				<tr>
					<td><strong>E-Mail:</strong></td>
					<td>{{ $user['email'] }}</td>
				</tr>
				<tr>
					<td><strong>Telefónne číslo:</strong></td>
					<td>{{ $user['phone'] }}</td>
				</tr>
			</table>
		</section>
		<section class="col-12 col-md-6 my-5">
			<table class="table">
				<tr>
					<td><strong>Doprava:</strong></td>
					<td>{{ $shipping[$shipping_info['shipping-type']] }}</td>
				</tr>
				<tr>
					<td><strong>Poznámka:</strong></td>
					<td>{{ $shipping_info['comment'] }}</td>
				</tr>
			</table>
		</section>
		<section>
			<input type="checkbox" name="order-agreement" id="order-agreement" required>
			<label class="mx-2" for="order-agreement">Súhlasím s podmienkami</label>
		</section>
		<section>
			<h4 class="text-end">Spolu:</h4>
			<h4 class="text-end">{{ $sum }} €</h4>
		</section>
	</section>
	<div class="proceed d-flex justify-content-center align-items-center">
		<button  class="btn btn-primary" type="submit">Dokončiť objednávku</button>
	</div>
</form>
@endsection
