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
                        @if(auth()->user()->can('view_usermgmt') || auth()->user()->can('view_roles'))
                        <li class="nav-item">
                            <a href="#userMenu" data-toggle="collapse" aria-expanded="{{ (request()->is('usermgmt*') || request()->is('roles*')) ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ (request()->is('usermgmt*') || request()->is('roles*')) ? 'active' : '' }}">
                                <i class="fas fa-users"></i>
                                User Management
                            </a>
                            <ul class="collapse list-unstyled {{ (request()->is('usermgmt*') || request()->is('roles*')) ? 'show' : '' }}" id="userMenu">
                                @can('view_usermgmt')
                                <li>
                                    <a class="nav-link {{ request()->is('roles*') ? 'active' : '' }}" href="{{ url('/roles') }}">
                                        <i class="fas fa-users"></i> 
                                        Role
                                    </a>
                                </li>
                                @endcan
                                @can('view_roles')
                                <li>
                                    <a href="{{ url('/usermgmt') }}" class="nav-link {{ request()->is('usermgmt*') ? 'active' : '' }}">
                                        <i class="fas fa-users"></i>
                                        User
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif
                        @if(auth()->user()->can('view_clt'))
                        <li class="nav-item">
                            <a href="#produK" data-toggle="collapse" aria-expanded="{{ request()->is('clt*') ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ request()->is('clt*') ? 'active' : '' }}">
                                <i class="fas fa-boxes"></i>
                                Produk
                            </a>
                            <ul class="collapse list-unstyled {{ request()->is('clt*') ? 'show' : '' }}" id="produK">
                                @can('view_clt')
                                <li>
                                    <a class="nav-link {{ request()->is('clt*') ? 'active' : '' }}" href="{{ url('/clt') }}">
                                        <i class="fas fa-boxes"></i>
                                        CLT
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif
                        @if(auth()->user()->can('view_rkmReport')||auth()->user()->can('view_mandorPlantReport')||auth()->user()->can('view_mandorFruitReport')||auth()->user()->can('view_mandorPanenReport')||auth()->user()->can('view_kawilPlantReport')||auth()->user()->can('view_kawilFruitReport')||auth()->user()->can('view_kawilPanenReport')||auth()->user()->can('view_phTBReport')||auth()->user()->can('view_phHTReport')||auth()->user()->can('view_phCLTReport')||auth()->user()->can('view_spiMandorReport')||auth()->user()->can('view_spiSensusReport'))
                        <li class="nav-item">
                            <a href="#report" data-toggle="collapse" 
                            aria-expanded="{{ request()->is('rkmReport*') || request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') || request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') || request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') || request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'true' : 'false' }}" 
                            class="nav-link dropdown-toggle {{ request()->is('rkmReport*') || request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') || request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') || request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') || request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'active' : '' }}">
                                <i class="far fa-chart-bar"></i>
                                Report
                            </a>
                            <ul class="collapse list-unstyled {{ request()->is('rkmReport*') || request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') || request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') || request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') || request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'show' : '' }}" id="report">
                                @can('view_rkmReport')
                                <li>
                                    <a class="nav-link {{ request()->is('rkmReport*') ? 'active' : '' }}" href="{{ url('/rkmReport') }}">
                                        <i class="far fa-chart-bar"></i>
                                        RKM Reports
                                    </a>
                                </li>
                                @endcan
                                @if(auth()->user()->can('view_mandorPlantReport')||auth()->user()->can('view_mandorFruitReport')||auth()->user()->can('view_mandorPanenReport'))
                                <li>
                                    <a href="#mandor" data-toggle="collapse" aria-expanded="{{ request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') ? 'active' : '' }}">
                                        <i class="far fa-chart-bar"></i>
                                        Mandor
                                    </a>
                                    <ul class="collapse list-unstyled {{ request()->is('mandorPlantcareReport*') || request()->is('mandorFruitcareReport*') || request()->is('mandorPanenReport*') ? 'show' : '' }}" id="mandor">
                                        @can('view_mandorPlantReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('mandorPlantcareReport*') ? 'active' : '' }}" href="{{ url('/mandorPlantcareReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Plantcare
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_mandorFruitReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('mandorFruitcareReport*') ? 'active' : '' }}" href="{{ url('/mandorFruitcareReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Fruitcare
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_mandorPanenReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('mandorPanenReport*') ? 'active' : '' }}" href="{{ url('/mandorPanenReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Panen
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->can('view_kawilPlantReport')||auth()->user()->can('view_kawilFruitReport')||auth()->user()->can('view_kawilPanenReport'))
                                <li>
                                    <a href="#kawil" data-toggle="collapse" aria-expanded="{{ request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') ? 'active' : '' }}">
                                        <i class="far fa-chart-bar"></i>
                                        Kawil
                                    </a>
                                    <ul class="collapse list-unstyled {{ request()->is('kawilPlantcareReport*') || request()->is('kawilFruitcareReport*') || request()->is('kawilPanenReport*') ? 'show' : '' }}" id="kawil">
                                        @can('view_mandorPlantReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('kawilPlantcareReport*') ? 'active' : '' }}" href="{{ url('/kawilPlantcareReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Plantcare
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_kawilFruitReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('kawilFruitcareReport*') ? 'active' : '' }}" href="{{ url('/kawilFruitcareReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Fruitcare
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_kawilPanenReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('kawilPanenReport*') ? 'active' : '' }}" href="{{ url('/kawilPanenReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Panen
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->can('view_phTBReport')||auth()->user()->can('view_phHTReport')||auth()->user()->can('view_phCLTReport'))
                                <li>
                                    <a href="#ph" data-toggle="collapse" aria-expanded="{{ request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') ? 'active' : '' }}">
                                        <i class="far fa-chart-bar"></i>
                                        PH
                                    </a>
                                    <ul class="collapse list-unstyled {{ request()->is('phtbReport*') || request()->is('phhtReport*') || request()->is('phcltReport*') ? 'show' : '' }}" id="ph">
                                        @can('view_phTBReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('phtbReport*') ? 'active' : '' }}" href="{{ url('/phtbReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Tandan
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_phHTReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('phhtReport*') ? 'active' : '' }}" href="{{ url('/phhtReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                QC
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_phCLTReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('phcltReport*') ? 'active' : '' }}" href="{{ url('/phcltReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                CLT
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif
                                @if(auth()->user()->can('view_spiMandorReport')||auth()->user()->can('view_spiSensusReport'))
                                <li>
                                    <a href="#spi" data-toggle="collapse" aria-expanded="{{ request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'true' : 'false' }}" class="nav-link dropdown-toggle {{ request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'active' : '' }}">
                                        <i class="far fa-chart-bar"></i>
                                        SPI
                                    </a>
                                    <ul class="collapse list-unstyled {{ request()->is('spiMandorReport*') || request()->is('spiSensusReport*') ? 'show' : '' }}" id="spi">
                                        @can('view_spiMandorReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('spiMandorReport*') ? 'active' : '' }}" href="{{ url('/spiMandorReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Mandor
                                            </a>
                                        </li>
                                        @endcan
                                        @can('view_spiSensusReport')
                                        <li>
                                            <a class="nav-link {{ request()->is('spiSensusReport*') ? 'active' : '' }}" href="{{ url('/spiSensusReport') }}">
                                                <i class="far fa-chart-bar"></i>
                                                Sensus
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @endif
                        <!-- @can('view_phBTReport')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('phbtReport*') ? 'active' : '' }}" href="{{ url('/phbtReport') }}">
                            <i class="far fa-chart-bar"></i>
                            PH Berat Tandan Reports
                            </a>
                        </li>
                        @endcan
                        @can('view_phBBReport')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('phbbReport*') ? 'active' : '' }}" href="{{ url('/phbbReport') }}">
                            <i class="far fa-chart-bar"></i>
                            PH Berat Bonggol Reports
                            </a>
                        </li>
                        @endcan -->
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