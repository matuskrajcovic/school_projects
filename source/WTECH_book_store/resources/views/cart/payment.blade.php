@extends('layout.layout', ['title' => "Platba"])

@section('content')
    @include('cart.partials.header', ['step' => 4])
<section class="cart">
	<form method="POST" class="payment-methods container d-flex flex-column align-items-center flex-grow-1 justify-content-center" action="{{ route('cart-payment') }}">
        @csrf
        <input type="hidden" name="step" value="5">
		<div class="form-check my-5">
			<article class="form-check">
				<i class="fas fa-credit-card"></i>
				<input class="form-check-input" type="radio" id="card" name="payment-method" value="1">
				<label class="form-check-label" for="card">Platba kartou online</label>
				<p>Zaplatíte jednoducho svojou kreditnou alebo debetnou kratou cez platodbnú bránu TransCard. Pripravte si čísla z vašej karty. Tento spôsob platny je bez poplatku.</p>
			</article>
			<article class="form-check">
				<i class="fas fa-university"></i>
				<input class="form-check-input" type="radio" id="account-transfer" name="payment-method" value="2">
				<label class="form-check-label" for="account-transfer">Prevod na účet</label>
				<p>Jednoduchý prevod na bankový účet, ktorý Vám obratom po vytvorení objednávky zašleme na Váš mail aj s inštrukciami. Tento spôsob platny je bez poplatku.</p>
			</article>
			<article class="form-check">
				<i class="fas fa-money-bill-wave"></i>
				<input class="form-check-input" type="radio" id="cash-on-delivery" name="payment-method" value="3">
				<label class="form-check-label" for="cash-on-delivery">Na dobierku</label>
				<p>Za tovar zaplatíte pri jeho prevzatí. cena služby je 1.45€.</p>
			</article>
		</div>
        <button class="btn btn-primary" type="submit">Pokračovať</button>
</form>
</section>
@endsection
