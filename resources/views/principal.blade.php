<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="SSistema de Gestion Empresarial">
    <meta name="author" content="">
    <meta name="keyword" content="Sistema de Gestion Empresarial">
    <link rel="shortcut icon" href="img/favicon.png">
    <!-- Id for channel Notification -->
    <meta name="userId" content="{{ Auth::check() ? Auth::user()->id : ''}}">
    
    <title>Sistema de Gestion Empresarial</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js" rel="stylesheet">
    
    <!-- Icons -->
    <link href="css/plantilla.css" rel="stylesheet">
</head>

<body class="app header-fixed sidebar-fixed aside-menu-fixed aside-menu-hidden footer-fixed breadcrumb-fixed">
    <div id="app">
        <header class="app-header navbar">
            <button class="navbar-toggler mobile-sidebar-toggler d-lg-none mr-auto" type="button">
            <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
            <button class="navbar-toggler sidebar-toggler d-md-down-none" type="button">
            <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="nav navbar-nav d-md-down-none">
                <li class="nav-item px-3">
                    <a class="nav-link" href="#">Escritorio</a>
                </li>
                <li class="nav-item px-3">
                    <a class="nav-link" href="#">Configuraciones</a>
                </li>
            </ul>
            <ul class="nav navbar-nav ml-auto">
                <notification :notifications="notifications"></notification>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        @if (Auth::user()->avatar != '')
                            <img src="img/avatars/{{ Auth::user()->avatar }}" class="img-avatar" />
                        @else
                            <img src="img/avatars/default.jpg" class="img-avatar">
                        @endif
                        <span class="d-md-down-none">{{ Auth::user()->persona->nombre }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <div class="dropdown-header text-center">
                            <strong>Cuenta</strong>
                        </div>
                        <!--<a class="dropdown-item" href="#"><i class="fa fa-user"></i> Perfil</a>-->
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" >
                            <i class="fa fa-lock"></i> Cerrar sesión
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: nome;">
                            {{ csrf_field() }}
                        </form>
                    </div>
                </li>
            </ul>
        </header>
        <div class="app-body">
            
            @if(Auth::check())
                @include('plantilla.sidebar')
                {{-- 
                @if (Auth::user()->idrol == 1)
                    @include('plantilla.sidebaradministrador')
                @elseif (Auth::user()->idrol == 2)
                    @include('plantilla.sidebarvendedor')
                @elseif (Auth::user()->idrol == 3)
                    @include('plantilla.sidebaralmacenero')
                @else
    
                @endif
                --}}
            @endif
            <!-- Contenido Principal -->
            @yield('contenido')
            <!-- /Fin del contenido principal -->

        </div>
        
    </div>

    <footer class="app-footer" id="appFooter">
        <span>Sistema de Gestion Empresarial &copy; <span v-text="anio_actual"></span></span>
    </footer>

    <div id="preloads" style="position: fixed; height: 0; bottom: 0; visibility: hidden;">
        <p style="font-family: Open Sans">
            <span style="font-weight: 300"><em></em></span>
            <span style="font-weight: 400"><em></em></span>
            <span style="font-weight: 500"><em></em></span>
            <span style="font-weight: 600"><em></em></span>
            <span style="font-weight: 700"><em></em></span>
        </p>
        <p style="font-family: Oswald">
            <span style="font-weight: 300"><em></em></span>
            <span style="font-weight: 400"><em></em></span>
            <span style="font-weight: 500"><em></em></span>
            <span style="font-weight: 600"><em></em></span>
            <span style="font-weight: 700"><em></em></span>
        </p>
    </div>
    <script src="js/app.js"></script>
    <script src="js/plantilla.js"></script>
</body>

</html>