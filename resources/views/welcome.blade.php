<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>INIFAP C.E. ZACATECAS</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-xxx" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/favicon.ico" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    <link rel="stylesheet" href="C://xampp//htdocs//geoportal//public//css//styles.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/estilos.css') }}">
    <style>

        .navbar-brand {
            color: white !important;
            font-size: 18px;
            font-weight: bold;
        }

        .navbar-nav > li > a {
            color: white !important;
        }

        /* Corrige el mapa que podría verse afectado */
        #map {
            height: 500px;
            width: 100%;
            position: relative;
            margin-bottom: 50px;
            z-index: 1; /* Asegura que se renderice encima */
        }
    </style>


    <!-- Respond.js soporte de media queries para Internet Explorer 8 -->
    <!-- ie8.js EventTarget para cada nodo en Internet Explorer 8 -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ie8/0.2.2/ie8.js"></script>
    <![endif]-->

    <nav class="navbar navbar-inverse sub-navbar navbar-fixed-top">
    <div class="container">
        <div class="row">
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
            </ul>
        </div>
        </div>
    </nav>
    
</head>
<body>
    <div class="container">
        <ol class="breadcrumb top-buffer">
            <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
            <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
            <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li class="active">Geoportal</li>
        </ol>
    </div>

    <!-- Contenedor principal centrado -->
    <div class="container">
        <div class="row">
                <div class="col-md-9">
                    <h2>Geoportal</h2>
                    <hr class="red">
                </div>
                <div class="col-md-3">

                    <!--SECCIÓN MODIFICABLE | MENU CONTEXTUAL -->
                    <div class="list-group">
                        <a class="list-group-item" style="text-decoration: none;" href="http://zacatecas.inifap.gob.mx/"><img src="images/templatemo_list.png" style="margin-right:10px;">Inicio</a>
                        <a class="list-group-item" style="text-decoration: none;" href="{{ route('login') }}"><img src="images/templatemo_list.png" style="margin-right:10px;">Modo administrador</a>
                    </div>
                </div>
            </div>
        <div class="row justify-content-end">
            <div class="col-md-11 card-map-container">
                <p>El siguiente mapa muestra los diferentes poligonos utilizados por los
                    agricultores de la región, así como los puntos de monitoreo
                    de cultivos y las estaciones meteorológicas que se encuentran
                    en el estado de Zacatecas.
                </p>
                <div class="map-wrapper">
                    <!-- Mapa -->
                    <div class="flex-grow-1 p-3">
                        <div id="map"></div>
                    </div>
                    <div id="coordinates">
                        <strong>Coordenadas:</strong>
                        <div id="lat-lng">Lat: --, Lng: --</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="{{ asset('js/mapaGob.js') }}"></script>
</body>
</html>