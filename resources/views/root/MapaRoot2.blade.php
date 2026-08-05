<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- NO MODIFICAR -->
    <title>INIFAP C.E. Zacatecas - Geoportal</title>

    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/styles/main.css" rel="stylesheet">
    <link href="https://framework-gb.cdn.gob.mx/gm/v3/assets/images/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-xxx" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link href="/favicon.ico" rel="shortcut icon">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css">
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

        #map {
            height: 500px;
            width: 100%;
            position: relative;
            margin-bottom: 50px;
            z-index: 1;
        }

        #buscador-cultivos {
            margin-bottom: 12px;
        }

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

        #paginacion-cultivos .pagination > li > a,
        #paginacion-cultivos .pagination > li > span {
            min-width: 36px;
            min-height: 36px;
            padding: 8px 12px;
            font-size: 14px;
            text-align: center;
            margin: 6px 0;
        }

        #alertContainer:empty {
            display: none;
        }

        #alertContainer:not(:empty) {
            display: block;
            margin-top: 15px;
        }

        #tabla-cultivos-section {
            margin-top: 8px;
        }

        #tabla-cultivos-section label {
            display: block;
            margin-bottom: 6px;
        }

        #buscador-coordenadas-row {
            margin-bottom: 12px;
        }

        .texto-seleccion-poligono {
            margin-top: 0;
            margin-bottom: 12px;
        }

        .map-wrapper {
            width: 100%;
            position: relative;
        }

        .card-map-container {
            width: 100%;
        }
    </style>
</head>

<body>

<main class="page mb-5" style="text-align: left;">

    <nav class="navbar navbar-expand-md navbar-dark bg-light sub-navbar fixed-top">
        <div class="container">

            <button type="button" class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#subNavBarDropdown" aria-controls="subNavBarDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <a class="navbar-brand sub-navbar" href="#"></a>

            <div class="collapse navbar-collapse" id="subNavBarDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/tramites" target="_self" title="Ir a trámites del gobierno">Trámites</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/articulos">Blog</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/multimedia">Multimedia</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/prensa">Prensa</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/agenda">Agenda</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/acciones_y_programas">Acciones y programas</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/inifap/archivo/documentos">Documentos</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://vun.inifap.gob.mx/portalweb/_Transparencia">Transparencia</a></li>
                    <li class="nav-item"><a class="nav-link subnav-link" href="https://www.gob.mx/agricultura/es/#344">Contacto</a></li>
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
                    <button type="submit" class="btn btn-outline-secondary btn-sm">Cerrar sesión</button>
                </form>

            </div>
        </div>
    @endauth

    <!-- Contenedor de la barra de navegación -->
    <div class="container">
        <ol class="breadcrumb top-buffer">
            <li><a href="http://www.gob.mx"><i class="icon icon-home"></i></a></li>
            <li><a href="http://www.gob.mx/inifap">Instituto Nacional de Investigaciones Forestales, Agrícolas y Pecuarias</a></li>
            <li><a href="http://zacatecas.inifap.gob.mx/">Inifap C.E. Zacatecas</a></li>
            <li><a href="{{ route('inicio') }}">Geoportal</a></li>
            <li><a href="{{ route('root') }}">Superusuario</a></li>
            <li class="active">Vista general del mapa</li>
        </ol>
    </div>

    <!-- Contenedor principal -->
    <div class="container">
        <div class="row">

            <!-- Columna principal -->
            <div class="col-md-9">

                <h2>Vista general de los polígonos</h2>
                <hr class="red">

                <div id="alertContainer" class="alert-position"></div>

                <div class="row">
                    <div class="col-md-12">
                        <p>Número de hectáreas intervenidas: <span id="hectareas-totales">--</span></p>
                    </div>
                </div>

                <div class="row" id="toggle-tabla-cultivos-wrap">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="button" id="toggle-tabla-cultivos">Ocultar cultivos</button>
                    </div>
                </div>

                <div id="tabla-cultivos-section">

                    <label for="buscador-cultivos">Buscar cultivo:</label>

                    <input class="form-control" type="text" id="buscador-cultivos" placeholder="Escribe el nombre del cultivo">

                    <div class="table-responsive card-map-container" id="tabla-up-wrapper">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th style="background:#009933; color:#FFF;">Cultivo</th>
                                    <th style="background:#009933; color:#FFF;">No. de hectáreas</th>
                                    <th style="background:#009933; color:#FFF;">Visualizar</th>
                                </tr>
                            </thead>

                            <tbody id="tabla-cultivos-body"></tbody>
                        </table>

                        <div id="paginacion-cultivos" class="text-center"></div>
                    </div>

                </div>

                <div class="row" style="margin-top: 12px;">
                    <div class="col-sm-12">
                        <p>Buscar por coordenadas:</p>
                    </div>
                </div>

                <div class="row" id="buscador-coordenadas-row">

                    <div class="col-sm-6" id="buscador-coordenadas-wrapper">
                        <input class="form-control" type="text" id="buscador-coordenadas" placeholder="Ejemplo: 22.7700, -102.5700">
                    </div>

                    <div class="col-sm-6">
                        <button id="btn-buscar-coordenadas" class="btn btn-success" type="button">Buscar</button>
                    </div>

                </div>

                <p class="texto-seleccion-poligono">A continuación, seleccione un polígono para ver su información</p>
            </div>
            

            <!-- Menú lateral -->
            <div class="col-md-3">
                <div class="list-group">
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Inicio</a>
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('unidades-produccion-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Unidades de producción</a>
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-usuarios-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Usuarios</a>
                    <a class="list-group-item" style="text-decoration: none;" href="{{ route('administrar-cultivos-root') }}"><img src="/images/templatemo_list.png" style="margin-right:10px;" alt="">Cultivos</a>
                </div>
            </div>

            <div class="card-map-container">
                    <div class="map-wrapper">

                        <div id="map"></div>

                        <div id="coordinates">
                            <strong>Coordenadas:</strong>
                            <div id="lat-lng">Lat: --, Lng: --</div>
                        </div>

                    </div>
                </div>

        </div>
    </div>

</main>

<!-- Modal para guardar datos de la parcela -->
<div class="modal fade" id="parcelaModal" tabindex="-1" aria-labelledby="parcelaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="parcelaForm">

                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="parcelaModalLabel">Guardar datos de la parcela</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="geom" name="geom">
                    <input type="hidden" id="coordenadas" name="coordenadas">

                    <div class="mb-3">
                        <label for="cultivo" class="form-label">Tipo de cultivo</label>
                        <input type="text" class="form-control" id="cultivo" name="cultivo" required>
                    </div>

                    <div class="mb-3">
                        <label for="nombre_capturista" class="form-label">Nombre del capturista</label>
                        <input type="text" class="form-control" id="nombre_capturista" name="nombre_capturista" required>
                    </div>

                    <div class="mb-3">
                        <label for="tecnico_id" class="form-label">ID del técnico</label>
                        <input type="number" class="form-control" id="tecnico_id" name="tecnico_id" required>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar parcela</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>

            </form>

        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="{{ asset('js/root/mapaRoot.js') }}"></script>
<script src="https://framework-gb.cdn.gob.mx/gm/v3/assets/js/gobmx.js"></script>

<script>
    $gmx(document).ready(function() {

    });
</script>

@stack('scripts')

</body>
</html>