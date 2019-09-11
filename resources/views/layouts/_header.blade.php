<nav class="navbar navbar-expand-lg navbar-light bg-light navbar-static-top">
    <div class="container">
        <!-- Branding Image -->
        <a class="navbar-brand " href="{{ url('/') }}">
            Laravel Shop
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav navbar-right">
                <!-- Authentication Links -->
                @guest
                     <li class="nav-item"><a class="nav-link" href="{{route('login')}}">Log in</a></li>
                     <li class="nav-item"><a class="nav-link" href="{{route('register')}}">Register</a></li>


                @else

                    <li class="nav-item">
                        <a class="nav-link mt-1" href="{{ route('cart.index') }}"><i class="fa fa-shopping-cart"></i></a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img src="https://i.pravatar.cc/150?u=fake@pravatar.com" class="img-responsive img-circle" width="30px" height="30px">
                            {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" id="logout" href="#"
                               onclick="event.preventDefault();document.getElementById('logout-form').submit();">Log Out</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
                            </form>
                            <a href="{{ route('user_addresses.index') }}" class="dropdown-item">Shipping Address</a>
                            <a href="{{ route('products.favorites') }}" class="dropdown-item">My Favors</a>

                            <a href="{{ route('orders.index') }}" class="dropdown-item">My Orders</a>

                        </div>
                    </li>
                @endguest

            </ul>
        </div>
    </div>
</nav>