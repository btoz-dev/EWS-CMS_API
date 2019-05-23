<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EWS') }} CMS</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap/bootstrap.min.css') }}">


    <!-- Dashboard CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/datatables.min.css') }}"/>
    @yield('stylesheet')

</head>
<body>

    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="#">{{ config('app.name', 'EWS') }} CMS</a>
        <!-- <input class="form-control form-control-dark w-100" type="text" placeholder="Search" aria-label="Search"> -->
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();">
                    Logout
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </li>
        </ul>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                            <i class="fas fa-home"></i>
                            Dashboard <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        @can('view_usermgmt')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('usermgmt*') ? 'active' : '' }}" href="{{ url('/usermgmt') }}">
                                <i class="fas fa-users"></i>
                                User Management
                                </a>
                            </li>
                        @endcan
                        @can('view_roles')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}" href="{{ url('/roles') }}">
                                <i class="fas fa-users"></i>
                                Role Management
                                </a>
                            </li>
                        @endcan
                        @can('view_rkmReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('rkmReport*') ? 'active' : '' }}" href="{{ url('/rkmReport') }}">
                                <i class="far fa-chart-bar"></i>
                                RKM Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_spiPlantReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('mandorPlantcareReport*') ? 'active' : '' }}" href="{{ url('/mandorPlantcareReport') }}">
                                <i class="far fa-chart-bar"></i>
                                SPI Plantcare Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_mandorFruitReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('mandorFruitcareReport*') ? 'active' : '' }}" href="{{ url('/mandorFruitcareReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Mandor Fruitcare Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_mandorPanenReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('mandorPanenReport*') ? 'active' : '' }}" href="{{ url('/mandorPanenReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Mandor Panen Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_kawilPlantReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('kawilPlantcareReport*') ? 'active' : '' }}" href="{{ url('/kawilPlantcareReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Kawil Plantcare Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_kawilFruitReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('kawilFruitcareReport*') ? 'active' : '' }}" href="{{ url('/kawilFruitcareReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Kawil Fruitcare Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_kawilPanenReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('kawilPanenReport*') ? 'active' : '' }}" href="{{ url('/kawilPanenReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Kawil Panen Reports
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('phbtReport*') ? 'active' : '' }}" href="{{ url('/phbtReport') }}">
                            <i class="far fa-chart-bar"></i>
                            PH BT Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('phhtReport*') ? 'active' : '' }}" href="{{ url('/phhtReport') }}">
                            <i class="far fa-chart-bar"></i>
                            PH HT Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('phcltReport*') ? 'active' : '' }}" href="{{ url('/phcltReport') }}">
                            <i class="far fa-chart-bar"></i>
                            PH CLT Reports
                            </a>
                        </li>
                        @can('view_customReport')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('customReport*') ? 'active' : '' }}" href="{{ url('/customReport') }}">
                                <i class="far fa-chart-bar"></i>
                                Custom Reports
                                </a>
                            </li>
                        @endcan
                        @can('view_apk')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('apk*') ? 'active' : '' }}" href="{{ url('/apk') }}">
                                <i class="fab fa-android"></i>
                                APK
                                </a>
                            </li>
                        @endcan
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ url('/users') }}">
                            <i class="fab fa-android"></i>
                            user admin test
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div id="flash-msg">
                    @include('flash::message')
                </div>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <!-- jQuery -->
    <!-- <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script> -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <!-- Popper -->
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    
    <!-- DataTable -->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script> -->
    <!-- <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script> -->
    <script type="text/javascript" src="{{ asset('js/datatables.min.js') }}"></script>

    <script defer src="{{ asset('js/all.js') }}"></script>
    @yield('script')
    <script>
      var msg = '{{Session::get('alert')}}';
      var exist = '{{Session::has('alert')}}';
      if(exist){
        alert(msg);
      }
    </script>
</body>
</html>