<section class="cart-header container my-4 d-flex justify-content-center align-items-center">
    <ul>
        <li class="{{ $step == 1 ? 'active' : '' }} my-1">
            <span class="order">1</span>
            <span class="name">Košík</span>
        </li>
        <li class="{{ $step == 2 ? 'active' : '' }} my-1">
            <span class="order">2</span>
            <span class="name">Kontaktné údaje</span>
        </li>
        <li class="{{ $step == 3 ? 'active' : '' }} my-1">
            <span class="order">3</span>
            <span class="name">Doprava</span>
        </li>
        <li class="{{ $step == 4 ? 'active' : '' }} my-1">
            <span class="order">4</span>
            <span class="name">Platba</span>
        </li>
        <li class="{{ $step == 5 ? 'active' : '' }} my-1">
            <span class="order">5</span>
            <span class="name">Sumarizácia</span>
        </li>
    </ul>
</section>

<section>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</section>

