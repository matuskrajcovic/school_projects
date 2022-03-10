@extends('layout.layout', ['title' => "Spôsob doručenia"])

@section('content')
    @include('cart.partials.header', ['step' => 3])
<section class="cart">
	<form method="POST" class="cart-shipping-options my-4 mx-auto container row row-cols-2 flex-grow-1" action="{{ route('cart-shipping') }}">
        @csrf
        <input type="hidden" name="step" value="4">
		<div class="cart-shipping-methods d-flex col-12 col-lg-6 justify-content-center align-items-top my-5">
			<div>
				<article class="form-check">
					<input class="form-check-input" id="post-office-address" type="radio" name="shipping-type" value="post_to_address">
					<label class="form-check-label" for="post-office-address">Slovenská pošta (na adresu)</label>
					<p>Slovenská pošta Vám doručí balík priamo na Vašu adresu. Dodacia doba je maiximálne 4 dni.</p>
				</article>
				<article class="form-check">
					<input class="form-check-input" id="post-office-post" type="radio" name="shipping-type" value="post_to_post">
					<label class="form-check-label" for="post-office-post">Slovenská pošta (na poštu)</label>
					<p>Slovenská pošta uchová Váš balík priamo na najbližšej pošte. Dodacia doba je maiximálne 2 dni.</p>
				</article>
				<article class="form-check">
					<input class="form-check-input" id="courier" type="radio" name="shipping-type" value="courier">
					<label class="form-check-label" for="courier">Kuriérska služba</label>
					<p>Kuriér Vám doručí balík priamo na Vašu adresu. Dodacia doba je maiximálne 2 dni.</p>
				</article>
				<article class="form-check">
					<input class="form-check-input" id="self-pickup" type="radio" name="shipping-type" value="to_branch">
					<label class="form-check-label" for="self-pickup">Osobný odber na pobočke</label>
					<p>Svoj tovar si môžete vyzdvihnúť na našej jedinej pobočke Bratislava, Obchodná 25.</p>
				</article>
			</div>
		</div>
		<div class="branch-pick d-flex col-12 col-lg-6 justify-content-center align-items-top my-5">
			<div>
				<p class="mt-4 mb-2 p-0">Poznámka:</p>
				<textarea class="form-control" name="comment"></textarea>
			</div>
		</div>
        <button class="btn btn-primary" type="submit">Pokračovať</button>
	</form>
</section>
@endsection
