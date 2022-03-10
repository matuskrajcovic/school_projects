@extends('layout.layout', ['title' => "Nákupný košík"])

@section('content')
    @include('cart.partials.header', ['step' => 1])
    <section class="cart">
        <form method="POST" class="items container d-flex flex-column flex-grow-1 justify-content-center" action="{{ route('cart') }}">
            @csrf
            <input type="hidden" name="step" value="2">
            @foreach ($products ?? [] as $product)
                @include('cart.partials.cart-item', $product)
            @endforeach

            <button class="btn btn-primary" type="submit">Pokračovať</button>
        </form>
    </section>
	
@endsection
