<!DOCTYPE html>
<html lang="es">
   <head>
     <meta charset="utf-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1">
     <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--NO MODIFICAR-->
    <title>INIFAP C.E. Zacatecas - Geoportal</title>		
	<link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/images/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-xxx" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="C://xampp//htdocs//geoportal//public//css//styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">

</head>
<body>
		<main class="page mb-5" style="text-align: left;">
		
		<nav class="navbar navbar-expand-md navbar-dark bg-light sub-navbar fixed-top">
		  <div class="container">
			<button
			  type="button"
			  class="navbar-toggler"
			  data-bs-toggle="collapse"
			  data-bs-target="#subNavBarDropdown"
			  aria-controls="subNavBarDropdown"
			  aria-expanded="false"
			  aria-label="Toggle navigation"
			>
			  <span class="navbar-toggler-icon"></span>
			</button>
		
			<a class="navbar-brand sub-navbar" href="#"></a>
		
			<div class="collapse navbar-collapse" id="subNavBarDropdown">
			  <ul class="navbar-nav">
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/tramites" target="_self" title="Ir a trámites del gobierno">Trámites</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/articulos">Blog</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/multimedia">Multimedia</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/prensa">Prensa</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/agenda">Agenda</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/acciones_y_programas">Acciones y programas</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/documentos">Documentos</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://vun.inifap.gob.mx/portalweb/_Transparencia">Transparencia</a></li>
				<li class="nav-item "><a class="nav-link subnav-link" href="https://www.gob.mx/agricultura/es/#344">Contacto</a></li>
			</ul>
			</div>
		  </div>
		</nav>

@auth
<div class="container-fluid py-1">
    <div class="d-flex justify-content-end align-items-center pe-4">

        <span class="me-3 fw-bold mb-0">
            {{ Auth::user()->name }}
        </span>

        <form method="POST" action="{{ route('logout') }}" class="mb-0">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">
                Cerrar sesión
            </button>
        </form>

    </div>
</div>
@endauth

        @yield("content") 
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
    
			<script src="https://framework-gb.cdn.gob.mx/gm/v3/assets/js/gobmx.js"></script>
			
    <script>
        $gmx(document).ready(function() {
            
        });
	</script>

    @stack('scripts')

</body>
</html>