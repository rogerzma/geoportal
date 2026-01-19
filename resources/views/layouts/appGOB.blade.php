<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!--NO MODIFICAR-->
    <title>INIFAP C.E. Zacatecas</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-xxx" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/favicon.ico" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    <link rel="stylesheet" href="C://xampp//htdocs//geoportal//public//css//styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">


    <!-- Respond.js soporte de media queries para Internet Explorer 8 -->
    <!-- ie8.js EventTarget para cada nodo en Internet Explorer 8 -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ie8/0.2.2/ie8.js"></script>
    <![endif]-->

    <nav class="navbar navbar-inverse sub-navbar navbar-fixed-top">
    <div class="container">
        <div class="row">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#subenlaces">
            <span class="sr-only">Interruptor de Navegación</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
        </div>
        </div>
        <div class="row">
        <a class="navbar-brand" href="/"></a>
        <div class="collapse navbar-collapse" id="subenlaces">
            <ul class="nav navbar-nav navbar-right">
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/articulos">Blog</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/multimedia">Multimedia</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/prensa">Prensa</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/agenda">Agenda</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/acciones_y_programas">Acciones y programas</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/inifap/archivo/documentos">Documentos</a></li>
                <li class="landing-btn"><a href="https://vun.inifap.gob.mx/portalweb/_Transparencia">Transparencia</a></li>
                <li class="landing-btn"><a href="https://www.gob.mx/agricultura/es/#344">Contacto</a></li>
                @auth
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <span class="glyphicon glyphicon-user" style="font-size:15px;"></span>
                        {{ Auth::user()->name }}
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                                @csrf
                                <button type="submit" style="
                                    background:none;
                                    border:none;
                                    width:100%;
                                    text-align:left;
                                    padding:8px 20px;
                                    color:#333;">
                                    <i class="fa fa-sign-out"></i> Cerrar sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @endauth
            </ul>
        </div>
        </div>
    </div>
    </nav>


</head>
<body>

        @yield('content')

        <div class="modal fade" id="modalDir" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Directorio</h5></button>
                    </div><div class="modal-body">
                        <table class="table table-striped table-responsive" style="font-size:12px;">
                            <tr>
                                <td align="center" valign="middle"><strong>NOMBRE</strong></td>
                                <td align="center" valign="middle"><strong>CARGO</strong></td>
                                <td align="center" valign="middle"><strong>CORREO</strong></td>
                            </tr>
                            <tr>
                                <td>Zamarripa Martínez Rogelio, I.S.C.</td>
                                <td>Desarrollador</td>
                                <td>rzamarripam2001@gmail.com</td>
                            </tr>
                            <tr>
                                <td>Rivas Aranda Alejandro, I.S.C.</td>
                                <td>Desarrollador</td>
                                <td>alekeyrivas@gmail.com</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    <!-- JS -->
    <script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script>
	   $gmx(document).ready(function() {
		
	   });
	</script>


</body>
</html>