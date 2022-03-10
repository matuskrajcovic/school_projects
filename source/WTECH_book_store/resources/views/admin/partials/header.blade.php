<nav class="navbar navbar-expand-md container-fluid">
    <span class="navbar-brand">
        <a href="{{route('homepage')}}">
            <img src="{{ asset('assets/logo.svg') }}" class="topbar-icon">
            <span>Naše Kníhkupectvo</span>
        </a>
    </span>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon d-flex align-items-center justify-content-center">
            <i class="fas fa-bars"></i>
        </span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item my-2 px-md-3">
                <a href="{{route('admin')}}">Produkty</a>
            </li>
            <li class="nav-item my-2 mx-3 text-muted">
                Pridať:
            </li>
            <li class="nav-item my-2 px-md-3 ">
				<a href="{{route('create-product', ['type' => 'book'])}}">knihu</a>
            </li>
            <li class="nav-item my-2 px-md-3">
				<a href="{{route('create-product', ['type' => 'e_book'])}}">e-knihu</a>
            </li>
            <li class="nav-item my-2 px-md-3">
				<a href="{{route('create-product', ['type' => 'audio_book'])}}">audioknihu</a>
            </li>
            <li class="nav-item my-2 px-md-3">
				<a href="{{route('create-product', ['type' => 'merchandice'])}}">darček</a>
            </li>
            <li class="nav-item my-2 px-md-3">
				<a href="{{route('create-product', ['type' => 'author'])}}">autora</a>
            </li>
        </ul>
        <ul class="navbar-nav login-cart">
            <li class="nav-item mx-2 my-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a onclick="this.parentNode.submit()">
                        <i class="fas fa-user"></i>
                        <span>Odhlásiť sa</span>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</nav>


