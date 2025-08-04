<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Luxzen Admin') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Custom CSS for Sidebar -->
    <style>
        body { overflow-x: hidden; }
        #sidebar-wrapper { min-height: 100vh; margin-left: -15rem; transition: margin .25s ease-out; }
        #sidebar-wrapper .sidebar-heading { padding: 0.875rem 1.25rem; font-size: 1.2rem; }
        #wrapper.toggled #sidebar-wrapper { margin-left: 0; }
        #page-content-wrapper { min-width: 100vw; }
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            #wrapper.toggled #sidebar-wrapper { margin-left: -15rem; }
        }
        .sidebar-nav .nav-link { color: #adb5bd; }
        .sidebar-nav .nav-link.active, .sidebar-nav .nav-link:hover { color: #fff; background-color: #495057; }
        .modal-backdrop { z-index: 1040 !important; }
        .modal { z-index: 1050 !important; }
    </style>

    @livewireStyles
</head>
<body>
    <div class="d-flex" id="wrapper" x-data="{ toggled: false }">
        
        {{-- Only show sidebar if the user is authenticated --}}
        @auth
        <!-- Sidebar -->
        <div class="bg-dark border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-dark text-white">
                <a href="{{ route('dashboard') }}" class="text-white text-decoration-none h5">Luxzen Admin</a>
            </div>
            <div class="list-group list-group-flush sidebar-nav">
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('users.index') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a>
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('trips.index') ? 'active' : '' }}" href="{{ route('trips.index') }}">Trips</a>
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('vehicles.index') ? 'active' : '' }}" href="{{ route('vehicles.index') }}">Vehicles</a>
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('invoices.index') ? 'active' : '' }}" href="{{ route('invoices.index') }}">Invoices</a>
                <a class="list-group-item list-group-item-action list-group-item-dark p-3 {{ request()->routeIs('settings') ? 'active' : '' }}" href="{{ route('settings') }}">Settings</a>
                <a href="{{ route('driver-locations') }}" class="list-group-item list-group-item-action {{ request()->is('driver-locations') ? 'active' : '' }}">Driver Locations</a>
            </div>
        </div>
        @endauth

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    {{-- Only show menu button if user is authenticated --}}
                    @auth
                        <button class="btn btn-primary" id="sidebarToggle" @click="toggled = !toggled">Menu</button>
                    @endauth
                    
                    <a class="navbar-brand ms-2" href="{{ url('/') }}">{{ config('app.name', 'Laravel') }}</a>

                    <div class="collapse navbar-collapse">
                        <!-- Right Side Of Navbar -->
                        <ul class="navbar-nav ms-auto">
                            <!-- Authentication Links -->
                            @guest
                                @if (Route::has('login'))
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                    </li>
                                @endif
                            @else
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endguest
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page content-->
            <main class="container-fluid p-4">
                @yield('content')
            </main>
        </div>
    </div>
    
    @livewireScripts
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Alpine JS for toggling -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Chart.js for Dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('scripts')
</body>
</html>