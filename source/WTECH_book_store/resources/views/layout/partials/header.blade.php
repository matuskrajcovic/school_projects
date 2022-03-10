@can('adminAccess')
<nav class="admin d-flex justify-content-end my-1">
    <a href="{{ route('admin') }}"><i class="fas fa-user-shield"></i>Admin panel</a>
</nav>
@endcan
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
        <ul class="navbar-nav me-auto ">
            <li class="nav-item my-2 px-md-2">
                <a href="{{route('category', ['id' => '1'])}}">Knihy</a>
            </li>
            <li class="nav-item my-2 px-md-2">
                <a href="{{route('category', ['id' => '2'])}}">E-Knihy</a>
            </li>
            <li class="nav-item my-2 px-md-2">
                <a href="{{route('category', ['id' => '3'])}}">Audioknihy</a>
            </li>
            <li class="nav-item my-2 px-md-2">
                <a href="{{route('category', ['id' => '4'])}}">Darčeky</a>
            </li>
        </ul>
        <div class="d-flex search-bar">
            <form method="GET" class="input-group mx-3 my-2" action="{{ route('results') }}">
                <input class="form-control" id="search" name="search" type="text" placeholder="Vyhľadať" aria-label="Search" value="{{ $search_string ?? '' }}">
                <button type="submit" class="btn btn-secondary">Vyhľadať</button>
            </form>
        </div>
        <ul class="navbar-nav login-cart">
            <li class="nav-item mx-2 my-2">
                @if(Auth::check())
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a onclick="this.parentNode.submit()">
                            <i class="fas fa-user"></i>
                            <span>Odhlásiť sa</span>
                        </a>
                    </form>
                @else
                    <a href="{{route('login')}}">
                        <i class="fas fa-user"></i>
                        <span>Prihlásiť sa</span>
                    </a>
                @endif
            </li>
            <li class="nav-item mx-2 my-2">
                <a href="{{route('cart')}}">
                    <i class="fas fa-shopping-cart"></i> 
                    <span>Košík</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

