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
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css">
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
            /* Estilos para el contenedor de coordenadas */
        .buscador-coordenadas-container {
            width: 100%;
            margin: 0 0 20px 0;
            padding: 14px 16px;
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .buscador-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-size: 15px;
            font-weight: 600;
        }

        .buscador-geocoder-div {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .buscador-geocoder-custom {
            display: flex !important;
            align-items: stretch !important;
            width: 100% !important;
            max-width: 520px;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        .buscador-geocoder-custom form {
            display: flex !important;
            width: 100% !important;
        }

        .buscador-geocoder-custom input {
            width: 100% !important;
            height: 42px !important;
            padding: 8px 14px !important;
            color: #333 !important;
            font-size: 15px !important;
            border: 1px solid #bbb !important;
            border-right: none !important;
            border-radius: 6px 0 0 6px !important;
            outline: none !important;
            background: #fff !important;
        }

        .buscador-geocoder-custom input:focus {
            border-color: #009933 !important;
            box-shadow: 0 0 0 2px rgba(0, 153, 51, 0.15) !important;
        }

        .buscador-geocoder-custom .leaflet-control-geocoder-icon {
            width: 48px !important;
            height: 42px !important;
            min-width: 48px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: #009933 !important;
            border: 1px solid #009933 !important;
            border-radius: 0 6px 6px 0 !important;
            cursor: pointer !important;
            text-decoration: none !important;
        }

        .buscador-geocoder-custom .leaflet-control-geocoder-icon i {
            color: #fff;
            font-size: 17px;
        }

        .buscador-geocoder-custom .leaflet-control-geocoder-icon:hover {
            background: #007a29 !important;
            border-color: #007a29 !important;
        }

        .buscador-geocoder-custom .leaflet-control-geocoder-alternatives {
            margin-top: 4px !important;
            border-radius: 6px !important;
            overflow: hidden !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        }

        .leaflet-control-geocoder-error {
            display: none !important;
        }

        .mensaje-busqueda {
            display: block;
            margin-top: 6px;
            font-size: 14px;
        }

        .mensaje-error {
            color: #a94442;
        }

        /* Reduce separación entre paginación de cultivos y buscador */
        #tabla-up-wrapper {
            margin-bottom: 8px !important;
        }

        #toggle-tabla-cultivos-wrap {
            margin-bottom: 8px;
        }

        #toggle-tabla-cultivos {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1px solid #cfd8dc;
            border-radius: 8px;
            background: #ffffff;
            color: #2f4f3a;
            font-weight: 600;
            line-height: 1.2;
            cursor: pointer;
            transition: background-color .15s ease, border-color .15s ease, color .15s ease;
        }

        #toggle-tabla-cultivos:hover {
            background: #f2f8f4;
            border-color: #9fb8aa;
            color: #1f3b2c;
        }

        #buscador-coordenadas {
            height: 44px;
            font-size: 15px;
            border-radius: 8px;
        }

        #btn-buscar-coordenadas {
            height: 44px;
            min-width: 120px;
            padding: 0 18px;
            border-radius: 8px;
            font-weight: 600;
        }

        #paginacion-cultivos .pagination {
            margin: 6px 0 6px;
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
                <div class="row">
                    
                    <div class="col-sm-9 table-responsive" id="tabla-up-wrapper">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="background:#009933; color:#FFF;">Cultivo</th>
                                    <th style="background:#009933; color:#FFF;">No. de hectáreas</th>
                                    <th style="background:#009933; color:#FFF;">Visualizar</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-cultivos-body">
                                <!-- Las filas se llenarán dinámicamente con JS -->
                            </tbody>
                        </table>
                        <div class="col-md-12">
                            <p>Numero de hectareas intervenidas: 
                                <span id="hectareas-totales">--</span>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <p>Numero de polígonos intervenidos: 
                                <span id="poligonos-totales">--</span>
                            </p>
                        </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <p>Buscar por coordenadas:</p>
                    </div>
                </div>
                <div class ="row" id="buscador-coordenadas-row">
                    <div class ="col-sm-6" id="buscador-coordenadas-wrapper">
                        <input class="form-control" type="text" name="name" id="buscador-coordenadas" placeholder="Ejemplo: 22.7700, -102.5700">
                    </div>
                    <div class="col-sm-6">
                        <button id="btn-buscar-coordenadas" class="btn btn-success">Buscar</button>
                    </div>
                </div>
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
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script src="{{ asset('js/mapaGob.js') }}"></script>
</body>
</html>