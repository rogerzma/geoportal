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
    <link href="/favicon.ico" rel="shortcut icon">
    <link href="https://framework-gb.cdn.gob.mx/assets/styles/main.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
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

    <!-- Contenedor de la barra de navegación -->
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
                    </div>
                </div>
            </div>
            <!-- Contenedor de alertas -->
            <div id="alertContainer" class="alert-position container mt-3"></div>
                <div class="row justify-content-end">
                    <div class="col-md-11 card-map-container">
                        <div class="map-wrapper">
                            <!-- Mapa -->
                            <div class="flex-grow-1 p-3">
                                <div id="map">
                                    <div class="icon-container">
                                        <div class="icon-button" id="draw-parcela" title="Dibujar parcela">🖊️</div>
                                        <div class="icon-button" id="delete-parcela" title="Eliminar parcela">🗑️</div>
                                    </div>
                                </div>
                            </div>
                            <div id="coordinates">
                                <strong>Coordenadas:</strong>
                                <div id="lat-lng">Lat: --, Lng: --</div>
                            </div>
                        </div>
                    </div>
                </div>
    </div>

    <!-- Modal para guardar datos de la parcela -->
    <div class="modal fade" id="parcelaModal" tabindex="1" aria-labelledby="parcelaModalLabel" aria-hidden="true">
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
                    <label for="nombre_productor" class="form-label">Nombre del productor</label>
                    <input type="text" class="form-control" id="nombre_productor" name="nombre_productor" required>
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


    <!-- Scripts -->
    <script src="https://framework-gb.cdn.gob.mx/gobmx.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        console.log(typeof bootstrap.Modal); // Ahora sí debería ser "function"
    </script>
    <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
    <script src="{{ asset('js/mapaAdmin.js') }}"></script>
</body>
</html>
